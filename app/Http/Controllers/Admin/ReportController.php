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
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Supported export formats (Only PDF, Excel, and Word).
     */
    protected array $supportedFormats = [
        'pdf'  => ['label' => 'PDF Document', 'ext' => 'pdf', 'icon' => 'picture_as_pdf', 'badge' => 'Dokumen Resmi', 'desc' => 'Dokumen resmi KIB B siap cetak atau arsip (A4 Landscape)'],
        'xlsx' => ['label' => 'Microsoft Excel', 'ext' => 'xlsx', 'icon' => 'table_view', 'badge' => 'Spreadsheet', 'desc' => 'File Excel lengkap dengan rumus total, border tabel, dan data terstruktur'],
        'docx' => ['label' => 'Microsoft Word', 'ext' => 'docx', 'icon' => 'description', 'badge' => 'Editable', 'desc' => 'File dokumen Microsoft Word siap edit dengan format tabel KIB B'],
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
            'filters'            => $request->only(['search', 'category_id', 'room_id', 'status', 'tahun', 'periode', 'format']),
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

        // Page Setup: Landscape & A4 Size Strictly
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        // Margins: 2.54 cm = 1.0 inch on all 4 sides (Top, Bottom, Left, Right)
        $sheet->getPageMargins()->setTop(1.0);
        $sheet->getPageMargins()->setRight(1.0);
        $sheet->getPageMargins()->setLeft(1.0);
        $sheet->getPageMargins()->setBottom(1.0);

        // Logo Instansi / Sekolah (Bila ada)
        $logoPath = null;
        if (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) {
            $logoPath = public_path($schoolProfile->logo);
        } elseif (file_exists(public_path('images/logo-sekolah.png'))) {
            $logoPath = public_path('images/logo-sekolah.png');
        }

        if ($logoPath && file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setDescription('Logo');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('B2');
            $drawing->setHeight(52);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(2);
            $drawing->setWorksheet($sheet);
        }

        // Title Rows
        $kabKotaTitle = 'PEMERINTAH ' . strtoupper($schoolProfile->kabupaten_kota ?: 'KABUPATEN PURWAKARTA');
        $sheet->setCellValue('A1', $kabKotaTitle);
        $sheet->setCellValue('A2', 'REKAPITULASI INVENTARIS BARANG (KIB) B');
        $sheet->setCellValue('A3', 'PERALATAN DAN MESIN');
        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');
        $sheet->mergeCells('A3:P3');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Identity School Info (Row 5 to 11, NO. KODE LOKASI tetap di bawah kiri)
        $sheet->setCellValue('A5', 'PROVINSI');
        $sheet->setCellValue('B5', ': ' . ($schoolProfile->provinsi ?: '-'));
        $sheet->setCellValue('A6', 'KAB./KOTA');
        $sheet->setCellValue('B6', ': ' . ($schoolProfile->kabupaten_kota ?: '-'));
        $sheet->setCellValue('A7', 'BIDANG');
        $sheet->setCellValue('B7', ': ' . ($schoolProfile->bidang ?: '-'));
        $sheet->setCellValue('A8', 'UNIT ORGANISASI');
        $sheet->setCellValue('B8', ': ' . ($schoolProfile->unit_organisasi ?: '-'));
        $sheet->setCellValue('A9', 'SUB UNIT ORGANISASI');
        $sheet->setCellValue('B9', ': ' . ($schoolProfile->sub_unit_organisasi ?: '-'));
        $sheet->setCellValue('A10', 'UPB');
        $sheet->setCellValue('B10', ': ' . ($schoolProfile->upb ?: '-'));
        $sheet->setCellValue('A11', 'NO. KODE LOKASI');
        $sheet->setCellValue('B11', ': ' . ($schoolProfile->no_kode_lokasi ?: '-'));

        $sheet->getStyle('A5:A11')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B5:B11')->getFont()->setSize(9);
        $sheet->getStyle('B11')->getFont()->setBold(true);

        // Table Header (Row 13 & 14)
        $sheet->setCellValue('A13', 'No.');
        $sheet->mergeCells('A13:A14');

        $sheet->setCellValue('B13', 'Kode Barang');
        $sheet->mergeCells('B13:B14');

        $sheet->setCellValue('C13', 'Jenis Barang / Nama Barang');
        $sheet->mergeCells('C13:C14');

        $sheet->setCellValue('D13', 'Nomor Registrasi');
        $sheet->mergeCells('D13:D14');

        $sheet->setCellValue('E13', 'Merk / Type');
        $sheet->mergeCells('E13:E14');

        $sheet->setCellValue('F13', 'Ukuran / CC');
        $sheet->mergeCells('F13:F14');

        $sheet->setCellValue('G13', 'Bahan');
        $sheet->mergeCells('G13:G14');

        $sheet->setCellValue('H13', 'Tahun Pembelian');
        $sheet->mergeCells('H13:H14');

        // Sub-columns for NOMOR
        $sheet->setCellValue('I13', 'Nomor');
        $sheet->mergeCells('I13:M13');
        $sheet->setCellValue('I14', 'Pabrik');
        $sheet->setCellValue('J14', 'Rangka');
        $sheet->setCellValue('K14', 'Mesin');
        $sheet->setCellValue('L14', 'Polisi');
        $sheet->setCellValue('M14', 'BPKB');

        $sheet->setCellValue('N13', 'Asal Usul Cara Perolehan');
        $sheet->mergeCells('N13:N14');

        $sheet->setCellValue('O13', 'Harga (Rp)');
        $sheet->mergeCells('O13:O14');

        $sheet->setCellValue('P13', 'Keterangan');
        $sheet->mergeCells('P13:P14');

        // Column Numbering Row 15: [1] to [16]
        for ($i = 1; $i <= 16; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->setCellValue($colLetter . '15', $i);
        }

        // Style Table Header (Row 13-15)
        $headerRange = 'A13:P15';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(8.5);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F4F7');

        // Fill Data Rows
        $currentRow = 16;
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

        $startDataRow = 16;
        $endDataRow = max(16, $currentRow - 1);
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
        $tableRange = "A13:P{$totalRow}";
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A16:P{$totalRow}")->getFont()->setSize(8.5);

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
     * Clean and prepare text for Word XML export, ensuring valid XML structure and character encoding.
     */
    protected function cleanWordText($text): string
    {
        if ($text === null || $text === '') {
            return '';
        }
        $str = html_entity_decode((string) $text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        // Remove characters that are illegal in XML 1.0 (except \t, \n, \r)
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
        if ($clean === null) {
            $clean = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
            $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean);
        }
        return $clean ?? '';
    }

    /**
     * Export report to Word (.docx) format.
     */
    protected function exportWord($schoolProfile, $inventories, $totalItems, $totalHarga, $generatedDate, $baseName)
    {
        // Pastikan XML output escaping aktif agar karakter seperti &, <, >, dan tanda petik di-escape dengan benar
        Settings::setOutputEscapingEnabled(true);

        $phpWord = new PhpWord();

        // Document styles & properties
        $creator = $this->cleanWordText($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao');
        $phpWord->getDocInfo()
            ->setCreator($creator)
            ->setTitle('Kartu Inventaris Barang (KIB) B - Peralatan dan Mesin');

        // Landscape A4 Page with 1440 twips margins (1 inch = 2.54 cm on all 4 sides)
        $section = $phpWord->addSection([
            'paperSize'    => 'A4',
            'orientation'  => 'landscape',
            'pageSizeW'    => 16838,
            'pageSizeH'    => 11906,
            'marginTop'    => 1440,
            'marginBottom' => 1440,
            'marginLeft'   => 1440,
            'marginRight'  => 1440,
        ]);
        $sectionStyle = $section->getStyle();
        $sectionStyle->setPaperSize('A4');
        $sectionStyle->setOrientation('landscape');

        // Logo Instansi
        $logoPath = null;
        if (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) {
            $logoPath = public_path($schoolProfile->logo);
        } elseif (file_exists(public_path('images/logo-sekolah.png'))) {
            $logoPath = public_path('images/logo-sekolah.png');
        }

        // Kop Header Table
        $kopTable = $section->addTable([
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin'  => 20,
            'alignment'   => Jc::CENTER,
        ]);
        $kopTable->addRow();
        $cellLogo = $kopTable->addCell(1600);
        if ($logoPath && file_exists($logoPath)) {
            $cellLogo->addImage($logoPath, [
                'width'     => 52,
                'height'    => 52,
                'alignment' => Jc::CENTER,
            ]);
        }
        $cellTitle = $kopTable->addCell(14000);
        $cellTitle->addText(
            'PEMERINTAH ' . strtoupper($this->cleanWordText($schoolProfile->kabupaten_kota ?: 'KABUPATEN PURWAKARTA')),
            ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 20]
        );
        $cellTitle->addText(
            'REKAPITULASI INVENTARIS BARANG (KIB) B',
            ['bold' => true, 'size' => 13, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 20]
        );
        $cellTitle->addText(
            'PERALATAN DAN MESIN',
            ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 40]
        );

        $section->addTextBreak(0.5);

        // Header Info (School Profile) Table - NO. KODE LOKASI tetap di bawah kiri
        $infoTable = $section->addTable([
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin'  => 15,
        ]);

        $infoItems = [
            'PROVINSI'            => $this->cleanWordText($schoolProfile->provinsi ?: '-'),
            'KAB./KOTA'           => $this->cleanWordText($schoolProfile->kabupaten_kota ?: '-'),
            'BIDANG'              => $this->cleanWordText($schoolProfile->bidang ?: '-'),
            'UNIT ORGANISASI'     => $this->cleanWordText($schoolProfile->unit_organisasi ?: '-'),
            'SUB UNIT ORGANISASI' => $this->cleanWordText($schoolProfile->sub_unit_organisasi ?: '-'),
            'UPB'                 => $this->cleanWordText($schoolProfile->upb ?: '-'),
            'NO. KODE LOKASI'     => $this->cleanWordText($schoolProfile->no_kode_lokasi ?: '-'),
        ];

        foreach ($infoItems as $label => $val) {
            $infoTable->addRow();
            $c1 = $infoTable->addCell(2400);
            $c1->addText($label, ['size' => 8, 'name' => 'Arial', 'bold' => true], ['spaceAfter' => 8]);
            $c2 = $infoTable->addCell(200);
            $c2->addText(':', ['size' => 8, 'name' => 'Arial', 'bold' => true], ['spaceAfter' => 8]);
            $c3 = $infoTable->addCell(13000);
            $c3->addText($val, ['size' => 8, 'name' => 'Arial', 'bold' => ($label === 'NO. KODE LOKASI')], ['spaceAfter' => 8]);
        }

        $section->addTextBreak(0.5);

        // Main Inventory Table (16 columns)
        $tableStyle = [
            'borderSize'  => 6,
            'borderColor' => '333333',
            'cellMargin'  => 40,
            'alignment'   => Jc::CENTER,
        ];
        $phpWord->addTableStyle('KibTable', $tableStyle);
        $table = $section->addTable('KibTable');

        $headerStyle = ['bold' => true, 'size' => 7.5, 'name' => 'Arial'];
        $headerBg = ['bgColor' => 'F2F4F7'];

        // Table Header Row 1
        $table->addRow();
        $table->addCell(420, array_merge($headerBg, ['vMerge' => 'restart']))->addText('No.', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1120, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Kode Barang', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(2520, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Nama Barang', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(630, array_merge($headerBg, ['vMerge' => 'restart']))->addText('No. Reg', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1120, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Merk / Type', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(630, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Ukuran', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(630, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Bahan', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(630, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Tahun', $headerStyle, ['alignment' => Jc::CENTER]);

        // Grouped Header NOMOR (5 columns)
        $table->addCell(2800, array_merge($headerBg, ['gridSpan' => 5]))->addText('Nomor', $headerStyle, ['alignment' => Jc::CENTER]);

        $table->addCell(980, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Asal Usul', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1260, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Harga (Rp)', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(1260, array_merge($headerBg, ['vMerge' => 'restart']))->addText('Ket.', $headerStyle, ['alignment' => Jc::CENTER]);

        // Table Header Row 2 (Sub-headers for Nomor)
        $table->addRow();
        $table->addCell(420, ['vMerge' => 'continue']);
        $table->addCell(1120, ['vMerge' => 'continue']);
        $table->addCell(2520, ['vMerge' => 'continue']);
        $table->addCell(630, ['vMerge' => 'continue']);
        $table->addCell(1120, ['vMerge' => 'continue']);
        $table->addCell(630, ['vMerge' => 'continue']);
        $table->addCell(630, ['vMerge' => 'continue']);
        $table->addCell(630, ['vMerge' => 'continue']);

        $table->addCell(560, $headerBg)->addText('Pabrik', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(560, $headerBg)->addText('Rangka', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(560, $headerBg)->addText('Mesin', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(560, $headerBg)->addText('Polisi', $headerStyle, ['alignment' => Jc::CENTER]);
        $table->addCell(560, $headerBg)->addText('BPKB', $headerStyle, ['alignment' => Jc::CENTER]);

        $table->addCell(980, ['vMerge' => 'continue']);
        $table->addCell(1260, ['vMerge' => 'continue']);
        $table->addCell(1260, ['vMerge' => 'continue']);

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
            $table->addCell(420)->addText((string)$no, $numStyle, ['alignment' => Jc::CENTER]);
            $table->addCell(1120)->addText($this->cleanWordText($inv->kode_barang ?: '-'), $bodyStyle);
            $namaCell = $table->addCell(2520);
            $namaCell->addText($this->cleanWordText($inv->nama_barang ?: '-'), $bodyStyle);
            if ($inv->category) {
                $namaCell->addText('(' . $this->cleanWordText($inv->category->nama_kategori) . ')', ['size' => 6.5, 'color' => '666666', 'name' => 'Arial']);
            }
            $table->addCell(630)->addText($this->cleanWordText($inv->nomor_register ?: '-'), $bodyStyle, ['alignment' => Jc::CENTER]);
            $table->addCell(1120)->addText($this->cleanWordText($inv->merk_type ?: '-'), $bodyStyle);
            $table->addCell(630)->addText($this->cleanWordText($inv->ukuran_cc ?: '-'), $bodyStyle);
            $table->addCell(630)->addText($this->cleanWordText($inv->bahan ?: '-'), $bodyStyle);
            $table->addCell(630)->addText($this->cleanWordText($inv->tahun_pembelian ?: '-'), $bodyStyle, ['alignment' => Jc::CENTER]);

            $table->addCell(560)->addText($this->cleanWordText($inv->nomor_pabrik ?: '-'), $bodyStyle);
            $table->addCell(560)->addText($this->cleanWordText($inv->nomor_rangka ?: '-'), $bodyStyle);
            $table->addCell(560)->addText($this->cleanWordText($inv->nomor_mesin ?: '-'), $bodyStyle);
            $table->addCell(560)->addText($this->cleanWordText($inv->nomor_polisi ?: '-'), $bodyStyle);
            $table->addCell(560)->addText($this->cleanWordText($inv->nomor_bpkb ?: '-'), $bodyStyle);

            $table->addCell(980)->addText($this->cleanWordText($inv->asal_usul ?: '-'), $bodyStyle);
            $table->addCell(1260)->addText('Rp ' . number_format($inv->harga ?? 0, 0, ',', '.'), $bodyStyle, ['alignment' => Jc::RIGHT]);
            $table->addCell(1260)->addText($this->cleanWordText($inv->keterangan ?? $inv->deskripsi ?? '-'), $bodyStyle);

            $no++;
        }

        // Total Row
        $table->addRow();
        $totalLabelCell = $table->addCell(11480, ['gridSpan' => 14]);
        $totalLabelCell->addText('JUMLAH HARGA KESELURUHAN', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::RIGHT]);

        $totalValCell = $table->addCell(1260);
        $totalValCell->addText('Rp ' . number_format($totalHarga, 0, ',', '.'), ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::RIGHT]);

        $table->addCell(1260)->addText('', $bodyStyle);

        // Signatures Block Table
        $section->addTextBreak(2);

        $signTable = $section->addTable([
            'borderSize'  => 0,
            'borderColor' => 'FFFFFF',
            'cellMargin'  => 40,
        ]);
        $signTable->addRow();

        // Left Signatory: Kepala Sekolah
        $signLeft = $signTable->addCell(7500);
        $signLeft->addText('Mengetahui,', ['size' => 9, 'name' => 'Arial']);
        $signLeft->addText('Kepala Sekolah', ['size' => 9, 'name' => 'Arial']);
        $signLeft->addTextBreak(3);
        $namaKepsek = $this->cleanWordText($schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd');
        $nipKepsek = $this->cleanWordText($schoolProfile->nip_kepala_sekolah ?: '19720315 199802 1 002');
        $signLeft->addText('(' . $namaKepsek . ')', ['size' => 9, 'name' => 'Arial', 'bold' => true]);
        $signLeft->addText('NIP. ' . $nipKepsek, ['size' => 9, 'name' => 'Arial']);

        // Right Signatory: Pembuat Laporan
        $kabKotaClean = $schoolProfile->kabupaten_kota ? str_replace(['Kabupaten ', 'Kota '], '', $schoolProfile->kabupaten_kota) : 'Babakancikao';
        $signRight = $signTable->addCell(7500);
        $signRight->addText($this->cleanWordText($kabKotaClean) . ', ' . $this->cleanWordText($generatedDate), ['size' => 9, 'name' => 'Arial']);
        $signRight->addText('Pembuat Laporan', ['size' => 9, 'name' => 'Arial']);
        $signRight->addTextBreak(3);
        $namaPembuat = $this->cleanWordText($schoolProfile->nama_pembuat ?: 'Admin Sarpras');
        $nipPembuat = $this->cleanWordText($schoolProfile->nip_pembuat ?: '19800101 200501 1 001');
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

        // Search: Kode Barang, Nama Barang, Nomor Register, Merk / Type, Kategori, Ruangan
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('nomor_register', 'like', "%{$search}%")
                  ->orWhere('merk_type', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('nama_kategori', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($search) {
                      $rq->where('nama_ruangan', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Category (support single ID or array of multiple category IDs)
        $cat = $request->input('category_id');
        if (!empty($cat)) {
            if (is_array($cat)) {
                $cleanCat = array_filter($cat);
                if (!empty($cleanCat)) {
                    $query->whereIn('category_id', $cleanCat);
                }
            } else {
                $query->where('category_id', $cat);
            }
        }

        // Filter: Room (support single ID or array of multiple room IDs)
        $room = $request->input('room_id');
        if (!empty($room)) {
            if (is_array($room)) {
                $cleanRoom = array_filter($room);
                if (!empty($cleanRoom)) {
                    $query->whereIn('room_id', $cleanRoom);
                }
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

        // Filter: Rentang Periode / Tahun (e.g. "01/01/2020 — 31/12/2024" or "2020 - 2024" or single date/year)
        $periodeInput = trim($request->input('periode', $request->input('tahun', '')));
        if (!empty($periodeInput)) {
            // Check if format is date range dd/mm/yyyy - dd/mm/yyyy
            if (preg_match('/(\d{1,2}\/\d{1,2}\/(\d{4}))\s*[-—–to]+\s*(\d{1,2}\/\d{1,2}\/(\d{4}))/u', $periodeInput, $matches)) {
                $startYear = (int)$matches[2];
                $endYear = (int)$matches[4];
                if ($startYear > $endYear) {
                    [$startYear, $endYear] = [$endYear, $startYear];
                }
                $query->where(function ($q) use ($startYear, $endYear) {
                    $q->whereBetween('tahun_pembelian', [$startYear, $endYear])
                      ->orWhereBetween('tahun_pembelian', [(string)$startYear, (string)$endYear]);
                });
            } elseif (preg_match('/^(\d{4})\s*[-—–to]+\s*(\d{4})$/u', $periodeInput, $matches)) {
                $startYear = (int)$matches[1];
                $endYear = (int)$matches[2];
                if ($startYear > $endYear) {
                    [$startYear, $endYear] = [$endYear, $startYear];
                }
                $query->where(function ($q) use ($startYear, $endYear) {
                    $q->whereBetween('tahun_pembelian', [$startYear, $endYear])
                      ->orWhereBetween('tahun_pembelian', [(string)$startYear, (string)$endYear]);
                });
            } elseif (preg_match('/\/(\d{4})$/', $periodeInput, $matches)) {
                $year = $matches[1];
                $query->where('tahun_pembelian', $year);
            } else {
                $query->where('tahun_pembelian', $periodeInput);
            }
        }

        return $query;
    }
}
