<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected SchoolProfile $schoolProfile;
    protected Category $category;
    protected Room $room;
    protected Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin'],
            [
                'name' => 'Admin Test',
                'nip_nuptk' => '123456789012345678',
                'email' => 'admin_test@smpn3babakancikao.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $this->schoolProfile = SchoolProfile::firstOrCreate([], [
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'npsn' => '20217391',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kabupaten Purwakarta',
            'bidang' => 'Pendidikan',
            'unit_organisasi' => 'Dinas Pendidikan Purwakarta',
            'sub_unit_organisasi' => 'SMPN 3 Babakancikao',
            'upb' => 'SMPN 3 Babakancikao',
            'no_kode_lokasi' => '12.34.56.78.90',
            'nama_kepala_sekolah' => 'Drs. H. Mamat Slamet, M.Pd.',
            'nip_kepala_sekolah' => '196805121994031005',
            'nama_pembuat' => 'Ahmad Fulan, S.Kom.',
            'nip_pembuat' => '198502142010011008',
        ]);

        $this->category = Category::create([
            'kode_kategori' => 'ELEK',
            'nama_kategori' => 'Elektronik & Multimedia',
            'keterangan' => 'Peralatan elektronik laboratorium dan kelas',
        ]);

        $this->room = Room::create([
            'kode_ruangan' => 'LAB-KOM',
            'nama_ruangan' => 'Laboratorium Komputer 1',
            'penanggung_jawab' => 'Guru TIK',
        ]);

        $this->inventory = Inventory::create([
            'kode_barang' => 'BRG-2024-001',
            'nama_barang' => 'Laptop ASUS ExpertBook',
            'nomor_register' => '0001',
            'merk_type' => 'ASUS B1400',
            'ukuran_cc' => '14 Inch',
            'bahan' => 'Aluminium / Plastik',
            'tahun_pembelian' => '2024',
            'nomor_pabrik' => 'SN12345678',
            'nomor_rangka' => '-',
            'nomor_mesin' => '-',
            'nomor_polisi' => '-',
            'nomor_bpkb' => '-',
            'asal_usul' => 'Pembelian APBD',
            'harga' => 8500000,
            'keterangan' => 'Kondisi sangat baik',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.laporan.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.laporan.preview'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.laporan.download'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_report_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Inventaris Barang');
        $response->assertSee('Cakupan Data Laporan');
        $response->assertSee('REKAPITULASI INVENTARIS BARANG (KIB) B');
        $response->assertSee('PERALATAN DAN MESIN');
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('Pratinjau', false);
        $response->assertSee('Drs. H. Mamat Slamet, M.Pd.'); // Nama kepala sekolah from school profile
        $response->assertSee('Ahmad Fulan, S.Kom.'); // Nama pembuat from school profile
        // Three direct download buttons are present
        $response->assertSee('btnDownloadPdf', false);
        $response->assertSee('btnDownloadXlsx', false);
        $response->assertSee('btnDownloadDocx', false);
        $response->assertSee('A4 Landscape');
        // Ensure stat cards are NOT present
        $response->assertDontSee('Total Data Inventaris');
        $response->assertDontSee('Total Nilai Perolehan Aset');
        $response->assertDontSee('Total Nilai Aset');
        $response->assertDontSee('Format Output Didukung');
    }

    public function test_admin_can_filter_index_with_multiple_conditions(): void
    {
        // Create second inventory with status rusak
        Inventory::create([
            'kode_barang' => 'BRG-2024-002',
            'nama_barang' => 'Proyektor Epson EB-X400',
            'nomor_register' => '0002',
            'merk_type' => 'Epson EB-X400',
            'ukuran_cc' => '3000 Lumens',
            'bahan' => 'Plastik Komposit',
            'tahun_pembelian' => '2023',
            'nomor_pabrik' => 'SN998877',
            'nomor_rangka' => '-',
            'nomor_mesin' => '-',
            'nomor_polisi' => '-',
            'nomor_bpkb' => '-',
            'asal_usul' => 'Pembelian BOS',
            'harga' => 6000000,
            'keterangan' => 'Lampu rusak',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'rusak',
        ]);

        // Filter selecting multiple conditions: baik and rusak
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'status' => ['baik', 'rusak'],
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('Proyektor Epson EB-X400');

        // Filter selecting only rusak
        $responseRusak = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'status' => ['rusak'],
        ]));

        $responseRusak->assertStatus(200);
        $responseRusak->assertSee('Proyektor Epson EB-X400');
        $responseRusak->assertDontSee('Laptop ASUS ExpertBook');
    }

    public function test_admin_can_view_report_preview_with_default_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.preview'));

        $response->assertStatus(200);
        $response->assertSee('Pratinjau Dokumen KIB B');
        $response->assertSee('REKAPITULASI INVENTARIS BARANG (KIB) B');
        $response->assertSee('PERALATAN DAN MESIN');
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('BRG-2024-001');
        $response->assertSee('12.34.56.78.90'); // No kode lokasi
        $response->assertSee('JUMLAH HARGA KESELURUHAN');
        $response->assertSee('Drs. H. Mamat Slamet, M.Pd.'); // Kepala sekolah
        $response->assertSee('Ahmad Fulan, S.Kom.'); // Pembuat laporan
    }

    public function test_admin_can_view_report_preview_with_different_format(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.preview', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertSee('.xlsx');
        $response->assertSee('Download .XLSX');
    }

    public function test_admin_can_filter_preview_by_category_and_year(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.preview', [
            'format' => 'pdf',
            'category_id' => $this->category->id,
            'tahun' => '2024',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laptop ASUS ExpertBook');
    }

    public function test_admin_preview_filter_out_non_matching_data(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.preview', [
            'format' => 'pdf',
            'search' => 'BarangYangTidakPernahAda123',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada data inventaris');
    }

    public function test_admin_can_download_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'pdf']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_download_excel_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_download_word_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'docx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }

    public function test_admin_downloading_unsupported_format_falls_back_to_pdf(): void
    {
        // Unsupported/unknown format is normalized to PDF (fallback behavior in download())
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'xyz']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_download_all_three_formats(): void
    {
        // PDF
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');

        // Excel
        $xlsxResponse = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'xlsx']));
        $xlsxResponse->assertStatus(200);
        $xlsxResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Word
        $docxResponse = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'docx']));
        $docxResponse->assertStatus(200);
        $docxResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }
}

