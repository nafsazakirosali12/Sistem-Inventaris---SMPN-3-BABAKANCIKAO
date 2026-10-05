<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin_inv'],
            [
                'name' => 'Admin Test Inv',
                'nip_nuptk' => '123456789012345678',
                'email' => 'admin_inv@smpn3babakancikao.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        $this->category = Category::create([
            'kode_kategori' => 'KTG-KOMP',
            'nama_kategori' => 'Perangkat Komputer',
            'keterangan' => 'PC, laptop, dan monitor',
        ]);

        $this->room = Room::create([
            'kode_ruangan' => 'RUG-LAB1',
            'nama_ruangan' => 'Laboratorium Komputer 1',
            'penanggung_jawab' => 'Budi Santoso, S.Kom',
            'keterangan' => 'Lantai 2 Gedung B',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.inventaris.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_inventory_index_without_breadcrumb(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Inventaris');
        $response->assertSee('Tambah Inventaris');
        // Breadcrumb inside page is removed
        $response->assertDontSee('Beranda / Data Inventaris');
        // Date range picker is present
        $response->assertSee('filter_date_range');
    }

    public function test_admin_can_view_create_page_without_card1_card2_labels_and_without_barcode_icon(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.create'));

        $response->assertStatus(200);
        // "Card 1" and "Card 2" labels must not appear on UI
        $response->assertDontSee('Card 1');
        $response->assertDontSee('Card 2');
        // Required section titles
        $response->assertSee('Informasi Utama');
        $response->assertSee('Detail Aset & Rekapitulasi');
        // Status Unit must be used instead of Status Barang
        $response->assertSee('Status Unit');
        $response->assertSee('Baik');
        $response->assertSee('Rusak');
        $response->assertSee('Hilang');
        // Barcode icon in kode_barang must be removed
        $response->assertDontSee('>barcode<', false);
        // Categories & Rooms from database
        $response->assertSee($this->category->nama_kategori);
        $response->assertSee($this->room->nama_ruangan);
    }

    public function test_admin_can_store_inventory_with_clean_numeric_price_and_status_unit(): void
    {
        $payload = [
            'kode_barang' => 'INV-2024-LAP-001',
            'nama_barang' => 'Laptop ASUS Vivobook 14',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik', // Status Unit
            'nomor_register' => '0001',
            'merk_type' => 'ASUS A416',
            'ukuran_cc' => '14 Inci',
            'bahan' => 'Aluminium',
            'tahun_pembelian' => '2024',
            'nomor_pabrik' => 'PBK-12345',
            'nomor_rangka' => 'RGK-98765',
            'nomor_mesin' => 'MSN-55443',
            'nomor_polisi' => 'T 1234 AB',
            'nomor_bpkb' => 'BPKB-8877',
            'asal_usul' => 'Dana BOS',
            'harga' => '5.000.000', // Form formatted string
            'deskripsi' => 'Kelengkapan charger dan tas bawaan',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.inventaris.store'), $payload);

        $response->assertRedirect(route('admin.inventaris.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventories', [
            'kode_barang' => 'INV-2024-LAP-001',
            'nama_barang' => 'Laptop ASUS Vivobook 14',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'harga' => 5000000.00, // Clean numeric in DB
            'nomor_register' => '0001',
            'tahun_pembelian' => '2024',
        ]);
    }

    public function test_kode_barang_must_be_unique(): void
    {
        Inventory::create([
            'kode_barang' => 'INV-EXIST-01',
            'nama_barang' => 'Barang Lama',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.inventaris.store'), [
            'kode_barang' => 'INV-EXIST-01',
            'nama_barang' => 'Barang Baru Kode Sama',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        $response->assertSessionHasErrors('kode_barang');
    }

    public function test_admin_can_view_inventory_detail(): void
    {
        $inv = Inventory::create([
            'kode_barang' => 'INV-DETAIL-01',
            'nama_barang' => 'Proyektor Epson EB-X400',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'nomor_register' => '0042',
            'merk_type' => 'Epson',
            'tahun_pembelian' => '2023',
            'harga' => 6500000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.show', $inv->id));

        $response->assertStatus(200);
        $response->assertSee('Proyektor Epson EB-X400');
        $response->assertSee('INV-DETAIL-01');
        $response->assertSee('0042');
        $response->assertSee('Epson');
        $response->assertSee('Rp 6.500.000');
    }

    public function test_admin_can_update_inventory(): void
    {
        $inv = Inventory::create([
            'kode_barang' => 'INV-UPDATE-01',
            'nama_barang' => 'PC Server',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'harga' => 8000000,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.inventaris.update', $inv->id), [
            'kode_barang' => 'INV-UPDATE-01',
            'nama_barang' => 'PC Server High Performance Updated',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'rusak', // Changed condition
            'harga' => '9.500.000',
        ]);

        $response->assertRedirect(route('admin.inventaris.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventories', [
            'id' => $inv->id,
            'nama_barang' => 'PC Server High Performance Updated',
            'status' => 'rusak',
            'harga' => 9500000.00,
        ]);
    }

    public function test_borrowed_inventory_cannot_be_deleted(): void
    {
        $inv = Inventory::create([
            'kode_barang' => 'INV-PINJAM-01',
            'nama_barang' => 'Laptop Dipinjam',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik', // Unit condition is Baik
        ]);

        // Active loan in loans table
        Loan::create([
            'kode_peminjaman' => 'PINJAM-TEST-001',
            'nama_peminjam' => 'Guru Penguji',
            'category_id' => $this->category->id,
            'inventory_id' => $inv->id,
            'tanggal_pinjam' => Carbon::now()->subHour(),
            'tanggal_kembali' => Carbon::now()->addHours(2),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.inventaris.destroy', $inv->id));

        $response->assertRedirect(route('admin.inventaris.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('inventories', ['id' => $inv->id]);
    }

    public function test_available_inventory_can_be_deleted(): void
    {
        $inv = Inventory::create([
            'kode_barang' => 'INV-HAPUS-01',
            'nama_barang' => 'Barang Untuk Dihapus',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.inventaris.destroy', $inv->id));

        $response->assertRedirect(route('admin.inventaris.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('inventories', ['id' => $inv->id]);
    }

    public function test_date_range_picker_filters_database_accurately(): void
    {
        $inv2022 = Inventory::create([
            'kode_barang' => 'INV-2022-A',
            'nama_barang' => 'Barang Tahun 2022',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'tahun_pembelian' => '2022',
        ]);

        $inv2024 = Inventory::create([
            'kode_barang' => 'INV-2024-B',
            'nama_barang' => 'Barang Tahun 2024',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'tahun_pembelian' => '2024',
        ]);

        // Filter date range covering only 2024 (e.g. 01/01/2024 — 31/12/2024)
        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.index', [
            'date_range' => '01/01/2024 — 31/12/2024'
        ]));

        $response->assertSee('Barang Tahun 2024');
        $response->assertDontSee('Barang Tahun 2022');

        // Filter date range covering 2022 to 2024
        $response2 = $this->actingAs($this->admin)->get(route('admin.inventaris.index', [
            'date_range' => '01/01/2022 — 31/12/2024'
        ]));

        $response2->assertSee('Barang Tahun 2024');
        $response2->assertSee('Barang Tahun 2022');
    }

    public function test_status_unit_filter(): void
    {
        Inventory::create([
            'kode_barang' => 'INV-BAIK-01',
            'nama_barang' => 'Unit Kondisi Baik',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        Inventory::create([
            'kode_barang' => 'INV-RUSAK-01',
            'nama_barang' => 'Unit Kondisi Rusak',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'rusak',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.index', [
            'status' => 'rusak'
        ]));

        $response->assertSee('Unit Kondisi Rusak');
        $response->assertDontSee('Unit Kondisi Baik');
    }

    public function test_date_range_picker_rejects_future_dates_beyond_today(): void
    {
        Inventory::create([
            'kode_barang' => 'INV-TODAY-01',
            'nama_barang' => 'Barang Hari Ini',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'tahun_pembelian' => date('Y'),
        ]);

        $futureStartDate = \Carbon\Carbon::now('Asia/Jakarta')->addDays(2)->format('d/m/Y');
        $futureEndDate = \Carbon\Carbon::now('Asia/Jakarta')->addDays(5)->format('d/m/Y');

        $response = $this->actingAs($this->admin)->get(route('admin.inventaris.index', [
            'date_range' => "{$futureStartDate} — {$futureEndDate}"
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('Barang Hari Ini');
    }
}
