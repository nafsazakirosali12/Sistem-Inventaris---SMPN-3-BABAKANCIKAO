<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Room;
use App\Models\SchoolProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Supported export formats.
     */
    protected array $supportedFormats = [
        'pdf'  => ['label' => 'PDF Document', 'ext' => 'pdf', 'icon' => 'picture_as_pdf', 'badge' => 'Dokumen Resmi', 'desc' => 'Dokumen resmi KIB B siap cetak atau arsip (A4 Landscape)'],
        'xlsx' => ['label' => 'Microsoft Excel', 'ext' => 'xlsx', 'icon' => 'table_view', 'badge' => 'Spreadsheet', 'desc' => 'File Excel lengkap dengan rumus total, border tabel, dan data terstruktur'],
        'docx' => ['label' => 'Microsoft Word', 'ext' => 'docx', 'icon' => 'description', 'badge' => 'Editable', 'desc' => 'File dokumen Microsoft Word siap edit dengan format tabel KIB B'],
        'png'  => ['label' => 'Gambar PNG', 'ext' => 'png', 'icon' => 'image', 'badge' => 'High-Res', 'desc' => 'Gambar kualitas tinggi (lossless) hasil render dokumen pratinjau'],
        'jpg'  => ['label' => 'Gambar JPG', 'ext' => 'jpg', 'icon' => 'image', 'badge' => 'Gambar Standar', 'desc' => 'Format gambar standar untuk lampiran atau dibagikan dengan cepat'],
        'jpeg' => ['label' => 'Gambar JPEG', 'ext' => 'jpeg', 'icon' => 'image', 'badge' => 'Kompresi Tinggi', 'desc' => 'Gambar kompresi optimal dengan ukuran file efisien dan visual tajam'],
        'webp' => ['label' => 'Gambar WebP', 'ext' => 'webp', 'icon' => 'image', 'badge' => 'Web Modern', 'desc' => 'Format gambar modern terkompresi efisien untuk tampilan web'],
    ];

    /**
     * Display the report generator options page (Filters, Format dropdown, and Data Table preview).
     */
    public function index(Request $request)
    {
        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile();
        $categories = Category::orderBy('nama_kategori')->get();
        $rooms = Room::orderBy('nama_ruangan')->get();

        $years = Inventory::whereNotNull('tahun_pembelian')
            ->where('tahun_pembelian', '!=', '')
            ->distinct()
            ->orderBy('tahun_pembelian', 'desc')
            ->pluck('tahun_pembelian');

        // Query filtered data
        $query = $this->buildQuery($request);
        $filteredTotalCount = (clone $query)->count();
        $filteredTotalHarga = (float) (clone $query)->sum('harga');

        $inventories = $query->orderBy('kode_barang', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        Carbon::setLocale('id');
        $generatedDate = Carbon::now()->translatedFormat('d F Y');

        return view('admin.laporan.index', [
            'schoolProfile'      => $schoolProfile,
            'categories'         => $categories,
            'rooms'              => $rooms,
            'years'              => $years,
            'inventories'        => $inventories,
            'tableInventories'   => $inventories,
            'filteredTotalCount' => $filteredTotalCount,
            'filteredTotalHarga' => $filteredTotalHarga,
            'formats'            => $this->supportedFormats,
            'filters'            => $request->only(['search', 'category_id', 'room_id', 'status', 'tahun', 'format']),
            'generatedDate'      => $generatedDate,
        ]);
    }

    /**
     * Display document preview page before downloading.
     */
    public function preview(Request $request)
    {
        $format = strtolower($request->query('format', 'pdf'));
        if (!array_key_exists($format, $this->supportedFormats)) {
            $format = 'pdf';
        }

        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile();
        $query = $this->buildQuery($request);

        $totalItems = (clone $query)->count();
        $totalHarga = (float) (clone $query)->sum('harga');
        $inventories = $query->orderBy('kode_barang', 'asc')->orderBy('id', 'asc')->get();

        Carbon::setLocale('id');
        $generatedDate = Carbon::now()->translatedFormat('d F Y');

        return view('admin.laporan.preview', [
            'schoolProfile'    => $schoolProfile,
            'inventories'      => $inventories,
            'totalItems'       => $totalItems,
            'totalHarga'       => $totalHarga,
            'activeFormat'     => $format,
            'formatInfo'       => $this->supportedFormats[$format],
            'supportedFormats' => $this->supportedFormats,
            'generatedDate'    => $generatedDate,
            'filters'          => $request->only(['search', 'category_id', 'room_id', 'status', 'tahun']),
        ]);
    }

    /**
     * Download the report in the requested format.
     */
    public function download(Request $request)
    {
        $format = strtolower($request->query('format', 'pdf'));
        if (!array_key_exists($format, $this->supportedFormats)) {
            $format = 'pdf';
        }

        $schoolProfile = SchoolProfile::first() ?? new SchoolProfile();
        $query = $this->buildQuery($request);
        $totalItems = (clone $query)->count();
        $totalHarga = (float) (clone $query)->sum('harga');
        $inventories = $query->orderBy('kode_barang', 'asc')->orderBy('id', 'asc')->get();

        Carbon::setLocale('id');
        $generatedDate = Carbon::now()->translatedFormat('d F Y');
        $timestamp = date('Ymd_His');
        $baseName = 'Laporan_Inventaris_KIB_B_' . $timestamp;

        // Image formats (PNG, JPG, JPEG, WebP) are rendered client-side via preview page html2canvas
        if (in_array($format, ['png', 'jpg', 'jpeg', 'webp'])) {
            return redirect()->route('admin.laporan.preview', array_merge(
                $request->all(),
                ['format' => $format, 'auto_download' => 1]
            ));
        }

        // PDF Generation via DomPDF
        if ($format === 'pdf') {
            return $this->exportPdf($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName);
        }

        // Excel Generation via PhpSpreadsheet
        if ($format === 'xlsx') {
            return $this->exportExcel($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName);
        }

        // Word Generation via PhpWord
        if ($format === 'docx') {
            return $this->exportWord($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName);
        }

        return redirect()->route('admin.laporan.index');
    }

    /**
     * Export report to PDF format.
     */
    protected function exportPdf($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName)
    {
        // Convert logo to base64 for reliable DomPDF rendering (requires GD)
        $logoBase64 = null;
        if (extension_loaded('gd')) {
            $logoPath = null;
            if (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) {
                $logoPath = public_path($schoolProfile->logo);
            } elseif (file_exists(public_path('images/logo-sekolah.png'))) {
                $logoPath = public_path('images/logo-sekolah.png');
            }

            if ($logoPath && file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'schoolProfile' => $schoolProfile,
            'inventories'   => $inventories,
            'totalItems'    => $totalItems,
            'totalHarga'    => $totalHarga,
            'generatedDate' => $generatedDate,
            'logoBase64'    => $logoBase64,
        ])
        ->setPaper('a4', 'landscape')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        return $pdf->download($baseName . '.pdf');
    }

    /**
     * Export report to Excel (.xlsx) format.
     */
    protected function exportExcel($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('KIB B');

        // Document Properties
        $spreadsheet->getProperties()
            ->setCreator($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao')
            ->setTitle('Kartu Inventaris Barang (KIB) B - Peralatan dan Mesin')
            ->setSubject('Laporan Inventaris Barang')
            ->setDescription('Laporan resmi inventaris barang KIB B');

        // Title Rows
        $sheet->setCellValue('A1', 'KARTU INVENTARIS BARANG (KIB) B');
        $sheet->setCellValue('A2', 'PERALATAN DAN MESIN');
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Identity School Info (Row 4 to 10)
        $sheet->setCellValue('A4', 'PROVINSI');
        $sheet->setCellValue('B4', ': ' . ($schoolProfile->provinsi ?: '-'));
        $sheet->setCellValue('A5', 'KAB./KOTA');
        $sheet->setCellValue('B5', ': ' . ($schoolProfile->kabupaten_kota ?: '-'));
        $sheet->setCellValue('A6', 'BIDANG');
        $sheet->setCellValue('B6', ': ' . ($schoolProfile->bidang ?: '-'));
        $sheet->setCellValue('A7', 'UNIT ORGANISASI');
        $sheet->setCellValue('B7', ': ' . ($schoolProfile->unit_organisasi ?: '-'));
        $sheet->setCellValue('A8', 'SUB UNIT ORGANISASI');
        $sheet->setCellValue('B8', ': ' . ($schoolProfile->sub_unit_organisasi ?: '-'));
        $sheet->setCellValue('A9', 'UPB');
        $sheet->setCellValue('B9', ': ' . ($schoolProfile->upb ?: '-'));

        $sheet->getStyle('A4:A9')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B4:B9')->getFont()->setSize(9);

        // No. Kode Lokasi on the right side
        $sheet->setCellValue('N5', 'NO. KODE LOKASI');
        $sheet->setCellValue('O5', ': ' . ($schoolProfile->no_kode_lokasi ?: '-'));
        $sheet->mergeCells('O5:P5');
        $sheet->getStyle('N5')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('O5')->getFont()->setBold(true)->setSize(9);

        // Table Header (Row 11 & 12)
        $sheet->setCellValue('A11', 'No.');
        $sheet->mergeCells('A11:A12');

        $sheet->setCellValue('B11', 'Kode Barang');
        $sheet->mergeCells('B11:B12');

        $sheet->setCellValue('C11', 'Jenis Barang / Nama Barang');
        $sheet->mergeCells('C11:C12');

        $sheet->setCellValue('D11', 'Nomor Registrasi');
        $sheet->mergeCells('D11:D12');

        $sheet->setCellValue('E11', 'Merk / Type');
        $sheet->mergeCells('E11:E12');

        $sheet->setCellValue('F11', 'Ukuran / CC');
        $sheet->mergeCells('F11:F12');

        $sheet->setCellValue('G11', 'Bahan');
        $sheet->mergeCells('G11:G12');

        $sheet->setCellValue('H11', 'Tahun Pembelian');
        $sheet->mergeCells('H11:H12');

        // Sub-columns for NOMOR
        $sheet->setCellValue('I11', 'Nomor');
        $sheet->mergeCells('I11:M11');
        $sheet->setCellValue('I12', 'Pabrik');
        $sheet->setCellValue('J12', 'Rangka');
        $sheet->setCellValue('K12', 'Mesin');
        $sheet->setCellValue('L12', 'Polisi');
        $sheet->setCellValue('M12', 'BPKB');

        $sheet->setCellValue('N11', 'Asal Usul Cara Perolehan');
        $sheet->mergeCells('N11:N12');

        $sheet->setCellValue('O11', 'Harga (Rp)');
        $sheet->mergeCells('O11:O12');

        $sheet->setCellValue('P11', 'Keterangan');
        $sheet->mergeCells('P11:P12');

        // Column Numbering Row 13: [1] to [16]
        for ($i = 1; $i <= 16; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->setCellValue($colLetter . '13', $i);
        }

        // Style Table Header (Row 11-13)
        $headerRange = 'A11:P13';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(8.5);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F4F7');

        // Fill Data Rows
        $currentRow = 14;
        $no = 1;

        foreach ($inventories as $inv) {
            $sheet->setCellValue('A' . $currentRow, $no);
            $sheet->setCellValueExplicit('B' . $currentRow, (string) ($inv->kode_barang ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $namaBarangWithCat = $inv->category ? ($inv->nama_barang . ' (' . $inv->category->nama_kategori . ')') : ($inv->nama_barang ?: '-');
            $sheet->setCellValue('C' . $currentRow, $namaBarangWithCat);
            $sheet->setCellValueExplicit('D' . $currentRow, (string) ($inv->nomor_register ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $currentRow, $inv->merk_type ?: '-');
            $sheet->setCellValue('F' . $currentRow, $inv->ukuran_cc ?: '-');
            $sheet->setCellValue('G' . $currentRow, $inv->bahan ?: '-');
            $sheet->setCellValue('H' . $currentRow, $inv->tahun_pembelian ?: '-');
            $sheet->setCellValueExplicit('I' . $currentRow, (string) ($inv->nomor_pabrik ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $currentRow, (string) ($inv->nomor_rangka ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K' . $currentRow, (string) ($inv->nomor_mesin ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L' . $currentRow, (string) ($inv->nomor_polisi ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M' . $currentRow, (string) ($inv->nomor_bpkb ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('N' . $currentRow, $inv->asal_usul ?: '-');
            $sheet->setCellValue('O' . $currentRow, (float) ($inv->harga ?? 0));
            $sheet->setCellValue('P' . $currentRow, $inv->keterangan ?? $inv->deskripsi ?? '-');

            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('O' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $currentRow++;
            $no++;
        }

        // Total Row
        $totalRow = $currentRow;
        $sheet->mergeCells("A{$totalRow}:N{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'JUMLAH HARGA KESELURUHAN');
        $sheet->getStyle("A{$totalRow}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $startDataRow = 14;
        $endDataRow = max(14, $currentRow - 1);
        if ($totalItems > 0) {
            $sheet->setCellValue("O{$totalRow}", "=SUM(O{$startDataRow}:O{$endDataRow})");
        } else {
            $sheet->setCellValue("O{$totalRow}", 0);
        }
        $sheet->getStyle("O{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("O{$totalRow}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("O{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue("P{$totalRow}", '');

        // Apply Borders to Entire Table
        $tableRange = "A11:P{$totalRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A14:P{$totalRow}")->getFont()->setSize(8.5);

        // Signatures Block
        $signRow = $totalRow + 3;

        // Kolom Kiri: Mengetahui / Kepala Sekolah
        $sheet->setCellValue("B{$signRow}", 'Mengetahui,');
        $sheet->setCellValue("B" . ($signRow + 1), 'Kepala Sekolah');
        $namaKepsek = $schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd';
        $nipKepsek = $schoolProfile->nip_kepala_sekolah ?: '19720315 199802 1 002';
        $sheet->setCellValue("B" . ($signRow + 5), '(' . $namaKepsek . ')');
        $sheet->setCellValue("B" . ($signRow + 6), 'NIP. ' . $nipKepsek);
        $sheet->getStyle("B{$signRow}:B" . ($signRow + 6))->getFont()->setSize(9);
        $sheet->getStyle("B" . ($signRow + 5))->getFont()->setBold(true);

        // Kolom Kanan: Pembuat Laporan
        $kabKotaClean = $schoolProfile->kabupaten_kota ? str_replace(['Kabupaten ', 'Kota '], '', $schoolProfile->kabupaten_kota) : 'Babakancikao';
        $sheet->setCellValue("M{$signRow}", $kabKotaClean . ', ' . $generatedDate);
        $sheet->setCellValue("M" . ($signRow + 1), 'Pembuat Laporan');
        $namaPembuat = $schoolProfile->nama_pembuat ?: 'Admin Sarpras';
        $nipPembuat = $schoolProfile->nip_pembuat ?: '19800101 200501 1 001';
        $sheet->setCellValue("M" . ($signRow + 5), '(' . $namaPembuat . ')');
        $sheet->setCellValue("M" . ($signRow + 6), 'NIP. ' . $nipPembuat);
        $sheet->getStyle("M{$signRow}:M" . ($signRow + 6))->getFont()->setSize(9);
        $sheet->getStyle("M" . ($signRow + 5))->getFont()->setBold(true);

        // Auto-size or set widths for columns
        $columnWidths = [
            'A' => 6,   // No
            'B' => 16,  // Kode Barang
            'C' => 26,  // Jenis / Nama Barang
            'D' => 14,  // Nomor Register
            'E' => 18,  // Merk / Type
            'F' => 14,  // Ukuran / CC
            'G' => 14,  // Bahan
            'H' => 12,  // Tahun Pembelian
            'I' => 14,  // No Pabrik
            'J' => 14,  // No Rangka
            'K' => 14,  // No Mesin
            'L' => 14,  // No Polisi
            'M' => 14,  // No BPKB
            'N' => 18,  // Asal Usul
            'O' => 18,  // Harga
            'P' => 20,  // Keterangan
        ];

        foreach ($columnWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $baseName . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Export report to Word (.docx) format.
     */
    protected function exportWord($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName)
    {
        $phpWord = new PhpWord();

        // Document styles & properties
        $phpWord->getDocInfo()
            ->setCreator($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao')
            ->setTitle('Kartu Inventaris Barang (KIB) B - Peralatan dan Mesin');

        // Landscape A4 Page
        $section = $phpWord->addSection([
            'orientation'  => 'landscape',
            'paperSize'    => 'A4',
            'marginTop'    => 720,  // ~0.5 inch
            'marginBottom' => 720,
            'marginLeft'   => 720,
            'marginRight'  => 720,
        ]);

        // Document Title
        $section->addText(
            'KARTU INVENTARIS BARANG (KIB) B',
            ['bold' => true, 'size' => 12, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 40]
        );
        $section->addText(
            'PERALATAN DAN MESIN',
            ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // Header Info (School Profile) Table
        $infoTable = $section->addTable([
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 40,
        ]);

        $infoTable->addRow();
        $cellLeft = $infoTable->addCell(9000);
        $cellRight = $infoTable->addCell(6000);

        $cellLeft->addText('PROVINSI : ' . ($schoolProfile->provinsi ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);
        $cellLeft->addText('KAB./KOTA : ' . ($schoolProfile->kabupaten_kota ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);
        $cellLeft->addText('BIDANG : ' . ($schoolProfile->bidang ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);
        $cellLeft->addText('UNIT ORGANISASI : ' . ($schoolProfile->unit_organisasi ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);
        $cellLeft->addText('SUB UNIT ORGANISASI : ' . ($schoolProfile->sub_unit_organisasi ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);
        $cellLeft->addText('UPB : ' . ($schoolProfile->upb ?: '-'), ['size' => 8.5, 'name' => 'Arial', 'bold' => true]);

        $cellRight->addText('NO. KODE LOKASI : ' . ($schoolProfile->no_kode_lokasi ?: '-'), ['size' => 9, 'name' => 'Arial', 'bold' => true], ['alignment' => Jc::RIGHT]);

        $section->addTextBreak(1);

        // Main Inventory Table
        $tableStyle = [
            'borderSize'  => 6,
            'borderColor' => '333333',
            'cellMargin'  => 50,
            'alignment'   => Jc::CENTER,
        ];
        $phpWord->addTableStyle('KibTable', $tableStyle);
        $table = $section->addTable('KibTable');

        $headerStyle = ['bold' => true, 'size' => 7.5, 'name' => 'Arial'];
        $headerBg = ['bgColor' => 'F2F4F7'];

        // Table Header Row 1
        $table->addRow();
        $table->addCell(400, array_merge($headerBg, ['vMerge' => 'restart']))->addText('No.', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1100, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Kode Barang', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1600, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Nama Barang', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(900, array_merge($headerBg, ['vMerge' => 'restart']))->addText('No. Reg', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1100, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Merk / Type', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(800, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Ukuran', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(800, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Bahan', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(700, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Tahun', $headerStyle, ['alignment' => Jc::CENTER]);

        // Grouped Header NOMOR (5 columns)
        $table->addCell(3500, array_merge($headerBg, ['gridSpan' => 5]))->addText('Nomor', $headerStyle, ['alignment' => Jc::CENTER]);

        $table->addCell(1100, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Asal Usul', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1300, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Harga (Rp)', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1200, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Ket.', $headerStyle, ['alignment' => Jc::CENTER]);

        // Table Header Row 2 (Sub-headers for Nomor)
        $table->addRow();
        $table->addCell(400, ['vMerge' => 'continue']);
        $table->addCell(1100, ['vMerge' => 'continue']);
        $table->addCell(1600, ['vMerge' => 'continue']);
        $table->addCell(900, ['vMerge' => 'continue']);
        $table->addCell(1100, ['vMerge' => 'continue']);
        $table->addCell(800, ['vMerge' => 'continue']);
        $table->addCell(800, ['vMerge' => 'continue']);
        $table->addCell(700, ['vMerge' => 'continue']);

        $table->addCell(700, $headerBg)->addText('Pabrik', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(700, $headerBg)->addText('Rangka', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(700, $headerBg)->addText('Mesin', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(700, $headerBg)->addText('Polisi', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(700, $headerBg)->addText('BPKB', $headerStyle, ['alignment' => Jc::CENTER]);

        $table->addCell(1100, ['vMerge' => 'continue']);
        $table->addCell(1300, ['vMerge' => 'continue']);
        $table->addCell(1200, ['vMerge' => 'continue']);

        // Row 3: Column Numbers 1 to 16
        $table->addRow();
        for ($col = 1; $col <= 16; $col++) {
            $table->addCell(null, $headerBg)->addText((string)$col, ['size' => 7, 'bold' => true, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
        }

        // Data Rows
        $bodyStyle = ['size' => 7.5, 'name' => 'Arial'];
        $numStyle = ['size' => 7.5, 'name' => 'Arial'];
        $no = 1;

        foreach ($inventories as $inv) {
            $table->addRow();
            $table->addCell(400)->addText((string)$no, $numStyle, ['alignment' => Jc::CENTER]);
            $table->addCell(1100)->addText($inv->kode_barang ?: '-', $bodyStyle);
            $namaCell = $table->addCell(1600);
            $namaCell->addText($inv->nama_barang ?: '-', $bodyStyle);
            if ($inv->category) {
                $namaCell->addText('(' . $inv->category->nama_kategori . ')', ['size' => 6.5, 'color' => '666666', 'name' => 'Arial']);
            }
            $table->addCell(900)->addText($inv->nomor_register ?: '-', $bodyStyle, ['alignment' => Jc::CENTER]);
            $table->addCell(1100)->addText($inv->merk_type ?: '-', $bodyStyle);
            $table->addCell(800)->addText($inv->ukuran_cc ?: '-', $bodyStyle);
            $table->addCell(800)->addText($inv->bahan ?: '-', $bodyStyle);
            $table->addCell(700)->addText($inv->tahun_pembelian ?: '-', $bodyStyle, ['alignment' => Jc::CENTER]);

            $table->addCell(700)->addText($inv->nomor_pabrik ?: '-', $bodyStyle);
            $table->addCell(700)->addText($inv->nomor_rangka ?: '-', $bodyStyle);
            $table->addCell(700)->addText($inv->nomor_mesin ?: '-', $bodyStyle);
            $table->addCell(700)->addText($inv->nomor_polisi ?: '-', $bodyStyle);
            $table->addCell(700)->addText($inv->nomor_bpkb ?: '-', $bodyStyle);

            $table->addCell(1100)->addText($inv->asal_usul ?: '-', $bodyStyle);
            $table->addCell(1300)->addText('Rp ' . number_format($inv->harga ?? 0, 0, ',', '.'), $bodyStyle, ['alignment' => Jc::RIGHT]);
            $table->addCell(1200)->addText($inv->keterangan ?? $inv->deskripsi ?? '-', $bodyStyle);

            $no++;
        }

        // Total Row
        $table->addRow();
        $totalLabelCell = $table->addCell(12500, ['gridSpan' => 14]);
        $totalLabelCell->addText('JUMLAH HARGA KESELURUHAN', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::RIGHT]);

        $totalValCell = $table->addCell(1300);
        $totalValCell->addText('Rp ' . number_format($totalHarga, 0, ',', '.'), ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::RIGHT]);

        $table->addCell(1200)->addText('', $bodyStyle);

        // Signatures Block Table
        $section->addTextBreak(2);

        $signTable = $section->addTable([
            'borderSize' => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin' => 40,
        ]);
        $signTable->addRow();

        // Left Signatory: Kepala Sekolah
        $signLeft = $signTable->addCell(7500);
        $signLeft->addText('Mengetahui,', ['size' => 9, 'name' => 'Arial']);
        $signLeft->addText('Kepala Sekolah', ['size' => 9, 'name' => 'Arial']);
        $signLeft->addTextBreak(3);
        $namaKepsek = $schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd';
        $nipKepsek = $schoolProfile->nip_kepala_sekolah ?: '19720315 199802 1 002';
        $signLeft->addText('(' . $namaKepsek . ')', ['size' => 9, 'name' => 'Arial', 'bold' => true]);
        $signLeft->addText('NIP. ' . $nipKepsek, ['size' => 9, 'name' => 'Arial']);

        // Right Signatory: Pembuat Laporan
        $kabKotaClean = $schoolProfile->kabupaten_kota ? str_replace(['Kabupaten ', 'Kota '], '', $schoolProfile->kabupaten_kota) : 'Babakancikao';
        $signRight = $signTable->addCell(7500);
        $signRight->addText($kabKotaClean . ', ' . $generatedDate, ['size' => 9, 'name' => 'Arial']);
        $signRight->addText('Pembuat Laporan', ['size' => 9, 'name' => 'Arial']);
        $signRight->addTextBreak(3);
        $namaPembuat = $schoolProfile->nama_pembuat ?: 'Admin Sarpras';
        $nipPembuat = $schoolProfile->nip_pembuat ?: '19800101 200501 1 001';
        $signRight->addText('(' . $namaPembuat . ')', ['size' => 9, 'name' => 'Arial', 'bold' => true]);
        $signRight->addText('NIP. ' . $nipPembuat, ['size' => 9, 'name' => 'Arial']);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $baseName . '.docx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Build standard filtered query for Inventories.
     */
    protected function buildQuery(Request $request)
    {
        $query = Inventory::with(['category', 'room']);

        // Search: Kode Barang, Nama Barang, Nomor Register, Merk / Type
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('nomor_register', 'like', "%{$search}%")
                  ->orWhere('merk_type', 'like', "%{$search}%");
            });
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $cat = $request->input('category_id');
            if (is_array($cat)) {
                $query->whereIn('category_id', array_filter($cat));
            } else {
                $query->where('category_id', $cat);
            }
        }

        // Filter: Room
        if ($request->filled('room_id')) {
            $room = $request->input('room_id');
            if (is_array($room)) {
                $query->whereIn('room_id', array_filter($room));
            } else {
                $query->where('room_id', $room);
            }
        }

        // Filter: Status / Kondisi (support single string or array of multiple conditions)
        $status = $request->input('status');
        if (!empty($status)) {
            if (is_array($status)) {
                $cleanStatuses = array_map('strtolower', array_filter($status));
                if (!empty($cleanStatuses)) {
                    $query->whereIn('status', $cleanStatuses);
                }
            } else {
                $query->where('status', strtolower($status));
            }
        }

        // Filter: Year / Year Range (e.g. "2020 - 2024" or "2024")
        if ($request->filled('tahun')) {
            $tahunInput = trim($request->input('tahun'));
            if (preg_match('/^(\d{4})\s*[-—–to]+\s*(\d{4})$/u', $tahunInput, $matches)) {
                $startYear = (int)$matches[1];
                $endYear = (int)$matches[2];
                if ($startYear > $endYear) {
                    [$startYear, $endYear] = [$endYear, $startYear];
                }
                $query->where(function ($q) use ($startYear, $endYear) {
                    $q->whereBetween('tahun_pembelian', [$startYear, $endYear])
                      ->orWhereBetween('tahun_pembelian', [(string)$startYear, (string)$endYear]);
                });
            } else {
                $query->where('tahun_pembelian', $tahunInput);
            }
        }

        return $query;
    }
}
