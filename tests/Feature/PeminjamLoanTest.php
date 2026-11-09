<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeminjamLoanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $peminjam;
    protected Category $category;
    protected Room $room;
    protected Inventory $inv1;
    protected Inventory $inv2;
    protected Inventory $inv3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Sarpras',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->peminjam = User::factory()->create([
            'name' => 'Akun Guru',
            'username' => 'guru',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
        ]);

        SchoolProfile::create([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'npsn' => '20203040',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
            'alamat' => 'Jl. Babakancikao',
        ]);

        $this->category = Category::create([
            'kode_kategori' => 'KAT-LAPTOP',
            'nama_kategori' => 'Laptop Multimedia',
            'keterangan' => 'Laptop inventaris sekolah',
        ]);

        $this->room = Room::create([
            'kode_ruangan' => 'R-LAB1',
            'nama_ruangan' => 'Lab Komputer 1',
            'penanggung_jawab' => 'Dra. Nurjanah',
        ]);

        // Create 3 inventory units with sequential codes
        $this->inv1 = Inventory::create([
            'kode_barang' => 'INV-LAP-001',
            'nama_barang' => 'Laptop ASUS ExpertBook',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'merk_type' => 'ExpertBook B1400',
        ]);

        $this->inv2 = Inventory::create([
            'kode_barang' => 'INV-LAP-002',
            'nama_barang' => 'Laptop ASUS ExpertBook',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'merk_type' => 'ExpertBook B1400',
        ]);

        $this->inv3 = Inventory::create([
            'kode_barang' => 'INV-LAP-003',
            'nama_barang' => 'Laptop ASUS ExpertBook',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
            'merk_type' => 'ExpertBook B1400',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('peminjam.inventaris.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('peminjam.riwayat.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_peminjam_features(): void
    {
        $response = $this->actingAs($this->admin)->get(route('peminjam.inventaris.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->admin)->get(route('peminjam.riwayat.index'));
        $response->assertStatus(403);
    }

    public function test_peminjam_can_view_inventaris_catalog_with_stock_format(): void
    {
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.inventaris.index'));

        $response->assertStatus(200);
        $response->assertSee('Peminjaman Inventaris');
        $response->assertSee('Laptop Multimedia');
        $response->assertSee('Laptop ASUS ExpertBook');
        $response->assertSee('Lab Komputer 1');
        // Check format: "Stok: 3"
        $response->assertSee('Stok: 3');
        $response->assertSee('Detail');
        $response->assertSee('Pinjam');
    }

    public function test_stock_calculation_excludes_borrowed_and_damaged_items(): void
    {
        // Create an active loan borrowing inv1
        Loan::create([
            'kode_peminjaman' => 'PINJAM-ACT-001',
            'nama_peminjam' => 'Guru Peminjam',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inv1->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDay(),
            'status' => 'dipinjam',
        ]);

        // Mark inv2 as damaged
        $this->inv2->update(['status' => 'rusak']);

        // Only inv3 should be available -> 1 Unit
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.inventaris.index'));

        $response->assertStatus(200);
        $response->assertSee('Stok: 1');
    }

    public function test_peminjam_can_submit_loan_with_auto_allocated_units(): void
    {
        $pinjamTime = Carbon::now()->addHour();
        $kembaliTime = Carbon::now()->addDays(2);

        // Borrow 2 units from category Laptop Multimedia
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Budi Santoso',
            'jumlah' => 2,
            'tanggal_pinjam' => $pinjamTime->format('Y-m-d H:i:s'),
            'tanggal_kembali' => $kembaliTime->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Keperluan Rapat Guru',
        ]);

        $loan = Loan::where('nama_peminjam', 'Budi Santoso')->first();
        $this->assertNotNull($loan);
        $response->assertRedirect(route('peminjam.riwayat.index', ['highlight' => $loan->id]));
        $response->assertSessionHas('success', 'Peminjaman berhasil! 2 unit barang telah berhasil dipinjam.');

        // Check loan was created
        $this->assertNotNull($loan);
        $this->assertEquals(2, $loan->jumlah);
        $this->assertEquals('dipinjam', $loan->status);
        $this->assertEquals($this->peminjam->id, $loan->user_id);
        $this->assertEquals($this->category->id, $loan->category_id);

        // Check details were created with the first 2 available codes: INV-LAP-001 and INV-LAP-002
        $this->assertCount(2, $loan->details);
        $allocatedCodes = $loan->details->pluck('kode_barang')->all();
        $this->assertEquals(['INV-LAP-001', 'INV-LAP-002'], $allocatedCodes);

        // Check that inv1 and inv2 are currently borrowed, while their physical status remains 'baik'
        $this->inv1->refresh();
        $this->inv2->refresh();
        $this->inv3->refresh();

        $this->assertTrue($this->inv1->is_currently_borrowed);
        $this->assertTrue($this->inv2->is_currently_borrowed);
        $this->assertFalse($this->inv3->is_currently_borrowed);
        $this->assertEquals('baik', $this->inv1->status);
        $this->assertEquals('baik', $this->inv2->status);
        $this->assertEquals('baik', $this->inv3->status); // inv3 is still available and baik!
    }

    public function test_cannot_borrow_more_than_available_stock(): void
    {
        // Try to borrow 5 units when only 3 exist
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Budi Santoso',
            'jumlah' => 5,
            'tanggal_pinjam' => Carbon::now()->format('Y-m-d H:i:s'),
            'tanggal_kembali' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Keperluan Rapat Guru',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Ensure no loan was created
        $this->assertDatabaseMissing('loans', [
            'nama_peminjam' => 'Budi Santoso',
        ]);
    }

    public function test_skips_unavailable_unit_and_allocates_next_available(): void
    {
        // Suppose inv1 is already borrowed by another loan
        Loan::create([
            'kode_peminjaman' => 'PINJAM-ACT-002',
            'nama_peminjam' => 'Guru Lain',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inv1->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDay(),
            'status' => 'dipinjam',
        ]);

        // Peminjam wants to borrow 1 unit
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Siti Aminah',
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now()->format('Y-m-d H:i:s'),
            'tanggal_kembali' => Carbon::now()->addDay()->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Mengajar Kelas 7B',
        ]);

        $loan = Loan::where('nama_peminjam', 'Siti Aminah')->first();
        $this->assertNotNull($loan);
        $response->assertRedirect(route('peminjam.riwayat.index', ['highlight' => $loan->id]));
        $response->assertSessionHas('success', 'Peminjaman berhasil! 1 unit barang telah berhasil dipinjam.');

        // System should have allocated inv2 (INV-LAP-002) because inv1 is unavailable
        $this->assertNotNull($loan);
        $this->assertEquals(['INV-LAP-002'], $loan->daftar_kode_barang);
    }

    public function test_peminjam_can_access_riwayat_page_without_errors(): void
    {
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Peminjaman');
    }

    public function test_riwayat_displays_cards_with_required_fields(): void
    {
        $loan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-TEST01',
            'nama_peminjam' => 'Dra. Hj. Fatimah',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inv1->id,
            'jumlah' => 2,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(2),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Praktikum IPA Multimedia',
        ]);

        LoanDetail::create([
            'loan_id' => $loan->id,
            'inventory_id' => $this->inv1->id,
            'kode_barang' => $this->inv1->kode_barang,
        ]);
        LoanDetail::create([
            'loan_id' => $loan->id,
            'inventory_id' => $this->inv2->id,
            'kode_barang' => $this->inv2->kode_barang,
        ]);

        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index'));

        $response->assertStatus(200);
        $response->assertSee('PINJAM-2026-TEST01');
        $response->assertSee('Dra. Hj. Fatimah');
        $response->assertSee('Laptop Multimedia');
        $response->assertSee('2 Unit');
        $response->assertSee('Dipinjam');
        $response->assertSee('INV-LAP-001');
        $response->assertSee('INV-LAP-002');
    }

    public function test_riwayat_search_filters_borrower_name_and_code(): void
    {
        $loan1 = Loan::create([
            'kode_peminjaman' => 'PINJAM-SEARCH-01',
            'nama_peminjam' => 'Asep Saepuloh',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDay(),
            'status' => 'dipinjam',
        ]);

        $loan2 = Loan::create([
            'kode_peminjaman' => 'PINJAM-SEARCH-02',
            'nama_peminjam' => 'Dewi Sartika',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDay(),
            'status' => 'dipinjam',
        ]);

        // Search for Asep
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index', ['search' => 'Asep']));
        $response->assertStatus(200);
        $response->assertSee('Asep Saepuloh');
        $response->assertDontSee('Dewi Sartika');

        // Search for code 02
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index', ['search' => 'PINJAM-SEARCH-02']));
        $response->assertStatus(200);
        $response->assertSee('Dewi Sartika');
        $response->assertDontSee('Asep Saepuloh');
    }

    public function test_riwayat_status_filter_and_overdue_badge(): void
    {
        // 1 active
        $activeLoan = Loan::create([
            'kode_peminjaman' => 'PINJAM-STATUS-ACT',
            'nama_peminjam' => 'Guru Aktif',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDays(3),
            'status' => 'dipinjam',
        ]);

        // 1 completed
        $completedLoan = Loan::create([
            'kode_peminjaman' => 'PINJAM-STATUS-DON',
            'nama_peminjam' => 'Guru Selesai',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now()->subDays(5),
            'tanggal_kembali' => Carbon::now()->subDays(3),
            'tanggal_kembali_aktual' => Carbon::now()->subDays(3),
            'status' => 'selesai',
        ]);

        // 1 overdue (borrowed, past return date)
        $overdueLoan = Loan::create([
            'kode_peminjaman' => 'PINJAM-STATUS-OVD',
            'nama_peminjam' => 'Guru Terlambat',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now()->subDays(4),
            'tanggal_kembali' => Carbon::now()->subDays(1),
            'status' => 'dipinjam',
        ]);

        // Filter status = dipinjam
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index', ['status' => 'dipinjam']));
        $response->assertStatus(200);
        $response->assertSee('Guru Aktif');
        $response->assertSee('Guru Terlambat');
        $response->assertDontSee('Guru Selesai');

        // Filter status = selesai
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index', ['status' => 'selesai']));
        $response->assertStatus(200);
        $response->assertSee('Guru Selesai');
        $response->assertDontSee('Guru Aktif');

        // Filter status = terlambat
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index', ['status' => 'terlambat']));
        $response->assertStatus(200);
        $response->assertSee('Guru Terlambat');
        $response->assertDontSee('Guru Aktif');
        $response->assertDontSee('Guru Selesai');
    }

    public function test_riwayat_pagination_six_per_page(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            Loan::create([
                'kode_peminjaman' => "PINJAM-PAG-{$i}",
                'nama_peminjam' => "Peminjam {$i}",
                'user_id' => $this->peminjam->id,
                'category_id' => $this->category->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => Carbon::now()->addDay(),
                'status' => 'dipinjam',
            ]);
        }

        $response = $this->actingAs($this->peminjam)->get(route('peminjam.riwayat.index'));
        $response->assertStatus(200);

        // Page 1 should contain 6 items
        $this->assertEquals(6, $response->viewData('loans')->count());
        $this->assertEquals(8, $response->viewData('loans')->total());
    }

    public function test_admin_completing_loan_releases_all_loan_details_back_to_available(): void
    {
        $loan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-MULTI',
            'nama_peminjam' => 'Guru Penguji',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inv1->id,
            'jumlah' => 2,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addDay(),
            'status' => 'dipinjam',
        ]);

        LoanDetail::create([
            'loan_id' => $loan->id,
            'inventory_id' => $this->inv1->id,
            'kode_barang' => 'INV-LAP-001',
        ]);

        LoanDetail::create([
            'loan_id' => $loan->id,
            'inventory_id' => $this->inv2->id,
            'kode_barang' => 'INV-LAP-002',
        ]);

        // Prior to completion, units are borrowed
        $this->assertTrue($this->inv1->fresh()->is_currently_borrowed);
        $this->assertTrue($this->inv2->fresh()->is_currently_borrowed);

        // Admin marks as selesai
        $response = $this->actingAs($this->admin)->put(route('admin.peminjaman.update', $loan->id), [
            'status' => 'selesai',
        ]);

        $response->assertRedirect();

        $loan->refresh();
        $this->assertEquals('selesai', $loan->status);

        // Both inv1 and inv2 are released (no longer borrowed), and physical condition is 'baik'
        $this->assertFalse($this->inv1->fresh()->is_currently_borrowed);
        $this->assertFalse($this->inv2->fresh()->is_currently_borrowed);
        $this->assertEquals('baik', $this->inv1->fresh()->status);
        $this->assertEquals('baik', $this->inv2->fresh()->status);
    }

    public function test_peminjam_can_submit_multi_category_loan_in_single_transaction(): void
    {
        // 1. Create categories: Proyektor and Matras (in addition to $this->category Laptop)
        $catProyektor = Category::create([
            'kode_kategori' => 'KAT-PROYEKTOR',
            'nama_kategori' => 'Proyektor',
        ]);

        $catMatras = Category::create([
            'kode_kategori' => 'KAT-MATRAS',
            'nama_kategori' => 'Matras',
        ]);

        // Create 1 Proyektor unit
        $proy1 = Inventory::create([
            'kode_barang' => 'INV-PROY-001',
            'nama_barang' => 'Epson EB-X06',
            'category_id' => $catProyektor->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        // Create 3 Matras units
        $matras1 = Inventory::create([
            'kode_barang' => 'INV-MAT-001',
            'nama_barang' => 'Matras Senam Speeds',
            'category_id' => $catMatras->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);
        $matras2 = Inventory::create([
            'kode_barang' => 'INV-MAT-002',
            'nama_barang' => 'Matras Yoga Kettler',
            'category_id' => $catMatras->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);
        $matras3 = Inventory::create([
            'kode_barang' => 'INV-MAT-003',
            'nama_barang' => 'Matras Latihan Busa',
            'category_id' => $catMatras->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        // Example from prompt: 2 Laptop, 1 Proyektor, 3 Matras = Total 6 units
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'nama_peminjam' => 'Drs. H. Ahmad Dahlan',
            'tanggal_pinjam' => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
            'tanggal_kembali' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Kegiatan Pelatihan dan Senam Bersama',
            'items' => [
                ['category_id' => $this->category->id, 'jumlah' => 2], // 2 Laptop (INV-LAP-001, INV-LAP-002)
                ['category_id' => $catProyektor->id, 'jumlah' => 1],   // 1 Proyektor (INV-PROY-001)
                ['category_id' => $catMatras->id, 'jumlah' => 3],      // 3 Matras (INV-MAT-001, INV-MAT-002, INV-MAT-003)
            ]
        ]);

        $loan = Loan::where('nama_peminjam', 'Drs. H. Ahmad Dahlan')->first();
        $this->assertNotNull($loan);
        $response->assertRedirect(route('peminjam.riwayat.index', ['highlight' => $loan->id]));
        $response->assertSessionHas('success', 'Peminjaman berhasil! 6 unit barang telah berhasil dipinjam.');

        // Check single loan was created with total 6 items
        $this->assertNotNull($loan);
        $this->assertEquals(6, $loan->jumlah);
        $this->assertEquals('dipinjam', $loan->status);

        // Check details count is 6
        $this->assertCount(6, $loan->details);

        // Check exact allocated codes
        $allocatedCodes = $loan->details->pluck('kode_barang')->all();
        $this->assertEquals([
            'INV-LAP-001',
            'INV-LAP-002',
            'INV-PROY-001',
            'INV-MAT-001',
            'INV-MAT-002',
            'INV-MAT-003',
        ], $allocatedCodes);

        // Verify all 6 units are marked borrowed via active loan, while physical status remains 'baik'
        $this->assertTrue($this->inv1->fresh()->is_currently_borrowed);
        $this->assertTrue($this->inv2->fresh()->is_currently_borrowed);
        $this->assertFalse($this->inv3->fresh()->is_currently_borrowed); // inv3 was not borrowed!
        $this->assertTrue($proy1->fresh()->is_currently_borrowed);
        $this->assertTrue($matras1->fresh()->is_currently_borrowed);
        $this->assertTrue($matras2->fresh()->is_currently_borrowed);
        $this->assertTrue($matras3->fresh()->is_currently_borrowed);

        $this->assertEquals('baik', $this->inv1->fresh()->status);
        $this->assertEquals('baik', $this->inv2->fresh()->status);
        $this->assertEquals('baik', $this->inv3->fresh()->status);
        $this->assertEquals('baik', $proy1->fresh()->status);
        $this->assertEquals('baik', $matras1->fresh()->status);

        // Verify category summary
        $loan->load('details.inventory.category');
        $this->assertStringContainsString('Laptop Multimedia (2)', $loan->category_summary);
        $this->assertStringContainsString('Proyektor (1)', $loan->category_summary);
        $this->assertStringContainsString('Matras (3)', $loan->category_summary);
    }

    public function test_multi_category_loan_rolls_back_if_any_category_exceeds_stock(): void
    {
        $catProyektor = Category::create([
            'kode_kategori' => 'KAT-PROYEKTOR-2',
            'nama_kategori' => 'Proyektor',
        ]);

        // Create only 1 Proyektor unit
        Inventory::create([
            'kode_barang' => 'INV-PROY-999',
            'nama_barang' => 'Epson EB-X06',
            'category_id' => $catProyektor->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        // Request 1 Laptop (available: 3) and 2 Proyektor (available: only 1!)
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'nama_peminjam' => 'Guru Testing',
            'tanggal_pinjam' => Carbon::now()->addHour()->format('Y-m-d H:i:s'),
            'tanggal_kembali' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Uji Coba Sistem',
            'items' => [
                ['category_id' => $this->category->id, 'jumlah' => 1],
                ['category_id' => $catProyektor->id, 'jumlah' => 2], // Exceeds available stock!
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Verify transaction was rolled back: no loan created, laptop unit still 'baik'
        $this->assertDatabaseMissing('loans', ['nama_peminjam' => 'Guru Testing']);
        $this->assertEquals('baik', $this->inv1->fresh()->status);
    }

    public function test_cannot_borrow_with_past_tanggal_pinjam(): void
    {
        // Try to submit loan with a past borrow datetime (2 hours ago)
        $pastTime = Carbon::now()->subHours(2);
        $returnTime = Carbon::now()->addHours(2);

        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Guru Masa Lalu',
            'jumlah' => 1,
            'tanggal_pinjam' => $pastTime->format('Y-m-d H:i:s'),
            'tanggal_kembali' => $returnTime->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Keperluan Masa Lalu',
        ]);

        $response->assertSessionHasErrors('tanggal_pinjam');
        $this->assertDatabaseMissing('loans', ['nama_peminjam' => 'Guru Masa Lalu']);
    }

    public function test_cannot_return_before_pinjam_datetime(): void
    {
        // Tanggal pinjam valid, tapi tanggal kembali sebelum tanggal pinjam
        $pinjamTime = Carbon::now()->addHour();
        $kembaliTime = Carbon::now()->subMinutes(10); // Sebelum pinjam

        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Guru Salah Waktu',
            'jumlah' => 1,
            'tanggal_pinjam' => $pinjamTime->format('Y-m-d H:i:s'),
            'tanggal_kembali' => $kembaliTime->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Keperluan Presentasi',
        ]);

        $response->assertSessionHasErrors('tanggal_kembali');
        $this->assertDatabaseMissing('loans', ['nama_peminjam' => 'Guru Salah Waktu']);
    }

    public function test_can_borrow_with_next_day_earlier_hour_kembali(): void
    {
        // Pinjam hari ini, kembali besok pagi jam 08:00 (kecuali jam pada jam pengembalian)
        $pinjamTime = Carbon::now()->addMinutes(10);
        $kembaliTime = Carbon::tomorrow()->setTime(8, 0); // Jam 8 pagi besok

        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Ibu Guru Matematika',
            'jumlah' => 1,
            'tanggal_pinjam' => $pinjamTime->format('Y-m-d H:i:s'),
            'tanggal_kembali' => $kembaliTime->format('Y-m-d H:i:s'),
            'alasan_tujuan' => 'Pelajaran Matematika',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('loans', [
            'nama_peminjam' => 'Ibu Guru Matematika',
            'status' => 'dipinjam',
        ]);
    }

    public function test_cannot_borrow_without_alasan_tujuan(): void
    {
        $response = $this->actingAs($this->peminjam)->post(route('peminjam.peminjaman.store'), [
            'category_id' => $this->category->id,
            'nama_peminjam' => 'Guru Tanpa Alasan',
            'jumlah' => 1,
            'tanggal_pinjam' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s'),
            'tanggal_kembali' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
            'alasan_tujuan' => '', // Blank reason
        ]);

        $response->assertSessionHasErrors('alasan_tujuan');
        $this->assertDatabaseMissing('loans', [
            'nama_peminjam' => 'Guru Tanpa Alasan',
        ]);
    }

    public function test_catalog_paginates_at_eight_categories_per_page(): void
    {
        // $this->category already has 1 category with 3 'baik' items in setUp
        // Create 8 more categories with at least 1 'baik' item each
        for ($i = 1; $i <= 8; $i++) {
            $cat = Category::create([
                'kode_kategori' => "KAT-TEST-{$i}",
                'nama_kategori' => "Kategori Uji {$i}",
            ]);
            Inventory::create([
                'kode_barang' => "INV-TEST-{$i}-001",
                'nama_barang' => "Barang Uji {$i}",
                'category_id' => $cat->id,
                'room_id' => $this->room->id,
                'status' => 'baik',
            ]);
        }

        // Total 9 categories. With paginate(8), page 1 should contain exactly 8 items.
        $response = $this->actingAs($this->peminjam)->get(route('peminjam.inventaris.index'));
        $response->assertStatus(200);

        $categories = $response->viewData('categories');
        $this->assertEquals(8, $categories->count());
        $this->assertEquals(9, $categories->total());
    }

    public function test_only_items_with_status_baik_are_counted_in_stock_and_displayed(): void
    {
        // Create a category with only broken/repair items
        $brokenCat = Category::create([
            'kode_kategori' => 'KAT-RUSAK',
            'nama_kategori' => 'Kategori Rusak Total',
        ]);
        Inventory::create([
            'kode_barang' => 'INV-BAD-001',
            'nama_barang' => 'Barang Rusak Total',
            'category_id' => $brokenCat->id,
            'room_id' => $this->room->id,
            'status' => 'rusak',
        ]);

        $response = $this->actingAs($this->peminjam)->get(route('peminjam.inventaris.index'));
        $response->assertStatus(200);

        // Kategori Rusak Total has stock 0 and Habis badge
        $stockMap = $response->viewData('stockPerCategory');
        $this->assertEquals(0, $stockMap[$brokenCat->id]);

        // Broken item must not be included in all_units
        $dataMap = $response->viewData('categoriesDataMap');
        $this->assertEmpty($dataMap[$brokenCat->id]['all_units']);
    }
}

