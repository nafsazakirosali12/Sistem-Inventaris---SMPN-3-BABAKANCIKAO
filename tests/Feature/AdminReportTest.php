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
        $response->assertSee('Format Dokumen Laporan');
        $response->assertSee('PDF Document (.pdf)');
        $response->assertSee('Microsoft Excel (.xlsx)');
        $response->assertSee('Microsoft Word (.docx)');
        $response->assertDontSee('Gambar PNG (.png)');
        $response->assertSee('REKAPITULASI INVENTARIS BARANG (KIB) B');
        $response->assertSee('PERALATAN DAN MESIN');
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('Pratinjau', false);
        $response->assertSee('Drs. H. Mamat Slamet, M.Pd.'); // Nama kepala sekolah from school profile
        $response->assertSee('Ahmad Fulan, S.Kom.'); // Nama pembuat from school profile
        // Ensure 3 cards and Total Nilai Aset are NOT present in filter
        $response->assertDontSee('Total Data Inventaris');
        $response->assertDontSee('Total Nilai Perolehan Aset');
        $response->assertDontSee('Total Nilai Aset');
        $response->assertDontSee('Format Output Didukung');
        $response->assertSee('A4 Landscape');
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

    public function test_admin_downloading_unknown_or_image_format_defaults_to_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'png']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_report_index_has_searchable_select2_status_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('select2-kondisi-multiple');
        $response->assertSee('name="status[]"', false);
        $response->assertSee('name="category_id[]"', false);
        $response->assertSee('name="room_id[]"', false);
        $response->assertSee('multiple="multiple"', false);
        $response->assertSee('Semua Kondisi');
        $response->assertSee('Semua Kategori');
        $response->assertSee('Semua Ruangan');
        $response->assertSee('downloadOfficialDocument()', false);
        $response->assertSee('Unduh Dokumen (.pdf)');
    }

    public function test_admin_can_filter_by_multiple_categories_and_rooms(): void
    {
        $cat2 = \App\Models\Category::create(['nama_kategori' => 'Kategori Lain', 'kode_kategori' => 'KAT2']);
        $room2 = \App\Models\Room::create(['nama_ruangan' => 'Ruangan Lain', 'kode_ruangan' => 'R2']);

        $inv2 = \App\Models\Inventory::create([
            'kode_barang' => 'BRG-002',
            'nama_barang' => 'Barang Kategori Dua',
            'category_id' => $cat2->id,
            'room_id' => $room2->id,
            'status' => 'baik',
            'tahun_pengadaan' => 2024,
            'harga' => 3000000,
        ]);

        // Filter both categories
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.preview', [
            'format' => 'pdf',
            'category_id' => [$this->category->id, $cat2->id],
            'room_id' => [$this->room->id, $room2->id],
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('Barang Kategori Dua');
    }

    public function test_admin_pdf_view_has_landscape_a4_margins_254_and_no_repeating_thead(): void
    {
        $view = view('admin.laporan.pdf', [
            'schoolProfile' => $this->schoolProfile,
            'inventories'   => [$this->inventory],
            'totalItems'    => 1,
            'totalHarga'    => 8500000,
            'generatedDate' => '09 November 2026',
            'logoBase64'    => null,
        ])->render();

        $this->assertStringContainsString('size: a4 landscape', $view);
        $this->assertStringContainsString('margin: 2.54cm 2.54cm 2.54cm 2.54cm', $view);
        $this->assertStringContainsString('display: table-row-group !important', $view);
        $this->assertStringContainsString('width: 18%;', $view); // Kolom Nama Barang lebar

        // Assert identitas instansi tersusun vertikal memanjang ke bawah (satu baris per entitas, bukan dua kolom)
        $this->assertStringContainsString('PROVINSI', $view);
        $this->assertStringContainsString('KAB./KOTA', $view);
        $this->assertStringContainsString('BIDANG', $view);
        $this->assertStringContainsString('UNIT ORGANISASI', $view);
        $this->assertStringContainsString('SUB UNIT ORGANISASI', $view);
        $this->assertStringContainsString('UPB', $view);
        $this->assertStringContainsString('NO. KODE LOKASI', $view);
    }

    public function test_admin_word_export_is_strictly_landscape_a4(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.download', ['format' => 'docx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $content = $response->streamedContent();
        $tempFile = tempnam(sys_get_temp_dir(), 'word_test_') . '.docx';
        file_put_contents($tempFile, $content);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempFile) === true);
        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();
        @unlink($tempFile);

        $this->assertStringContainsString('w:orient="landscape"', $documentXml);
        $this->assertMatchesRegularExpression('/w:w="1683[0-9]/', $documentXml);
        $this->assertMatchesRegularExpression('/w:h="1190[0-9]/', $documentXml);
    }

    public function test_admin_can_filter_index_with_single_condition_string(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'status' => 'baik',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laptop ASUS ExpertBook');

        $responseRusak = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'status' => 'rusak',
        ]));

        $responseRusak->assertStatus(200);
        $responseRusak->assertDontSee('Laptop ASUS ExpertBook');
    }
}

