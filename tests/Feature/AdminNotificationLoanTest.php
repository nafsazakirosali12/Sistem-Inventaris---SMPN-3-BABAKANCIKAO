<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminNotificationLoanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $peminjam;
    protected Category $category;
    protected Room $room;
    protected Inventory $inventory;
    protected Inventory $inventory2;

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
            'kode_kategori' => 'KAT-ELEK',
            'nama_kategori' => 'Elektronik Laptop',
            'keterangan' => 'Laptop inventaris',
        ]);

        $this->room = Room::create([
            'kode_ruangan' => 'R-LAB1',
            'nama_ruangan' => 'Lab Komputer',
            'penanggung_jawab' => 'PJ Lab',
        ]);

        $this->inventory = Inventory::create([
            'kode_barang' => 'INV-LAP-001',
            'nama_barang' => 'Laptop Lenovo ThinkPad',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);

        $this->inventory2 = Inventory::create([
            'kode_barang' => 'INV-LAP-002',
            'nama_barang' => 'Laptop Asus ExpertBook',
            'category_id' => $this->category->id,
            'room_id' => $this->room->id,
            'status' => 'baik',
        ]);
    }

    public function test_navbar_shows_amber_indicator_and_loan_data_when_active_loan_exists(): void
    {
        $loan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-991',
            'nama_peminjam' => 'Budi Santoso',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addHours(2),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Presentasi Pembelajaran',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Indicator and popover details
        $response->assertSee('Budi Santoso');
        $response->assertSee('Elektronik Laptop');
        $response->assertSee('bg-[#F59E0B]');
    }

    public function test_navbar_shows_empty_state_when_no_active_loans(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Tidak ada peminjaman baru');
    }

    public function test_admin_can_view_data_peminjaman_page_with_highlighted_row(): void
    {
        $loan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-992',
            'nama_peminjam' => 'Siti Aminah',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addHours(3),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Rapat Guru',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.peminjaman.index', ['highlight' => $loan->id]));
        $response->assertStatus(200);
        $response->assertSee('Data Peminjaman');
        $response->assertSee('PINJAM-2026-992');
        $response->assertSee('Siti Aminah');
        $response->assertSee('bg-[#FEF3C7]'); // Highlight accent background
        $response->assertSee('border-[#F59E0B]'); // Highlight amber border
    }

    public function test_admin_can_complete_loan_and_record_actual_return_time(): void
    {
        $loan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-993',
            'nama_peminjam' => 'Ahmad Fauzi',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now()->subHours(2),
            'tanggal_kembali' => Carbon::now()->addHours(2),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.peminjaman.update', $loan->id), [
            'status' => 'selesai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $loan->refresh();
        $this->assertEquals('selesai', $loan->status);
        $this->assertNotNull($loan->tanggal_kembali_aktual);
        $this->assertFalse($this->inventory->refresh()->is_currently_borrowed);
    }

    public function test_double_borrowing_prevention_auto_selects_next_available_unit(): void
    {
        // Loan 1 takes inventory 1
        $loan1 = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-001',
            'nama_peminjam' => 'Guru Satu',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now(),
            'tanggal_kembali' => Carbon::now()->addHours(2),
            'status' => 'dipinjam',
        ]);

        $this->assertEquals(1, $this->category->available_stock);

        // Loan 2 request via Controller store
        $response = $this->actingAs($this->admin)->post(route('admin.peminjaman.store'), [
            'nama_peminjam' => 'Guru Dua',
            'category_id' => $this->category->id,
            'tanggal_pinjam' => Carbon::now()->format('Y-m-d\TH:i'),
            'tanggal_kembali' => Carbon::now()->addHours(3)->format('Y-m-d\TH:i'),
            'alasan_tujuan' => 'Praktikum Kelas 8',
        ]);

        $response->assertSessionHas('success');
        
        $loan2 = Loan::where('nama_peminjam', 'Guru Dua')->first();
        $this->assertNotNull($loan2);
        $this->assertEquals($this->inventory2->id, $loan2->inventory_id);
        $this->assertEquals('dipinjam', $loan2->status);
        $this->assertEquals(0, $this->category->available_stock);
    }

    public function test_overdue_indicator_logic(): void
    {
        $overdueLoan = Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-OVERDUE',
            'nama_peminjam' => 'Guru Terlambat',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now()->subDays(2),
            'tanggal_kembali' => Carbon::now()->subDay(), // Past due
            'status' => 'dipinjam',
        ]);

        $this->assertTrue($overdueLoan->is_terlambat);

        $response = $this->actingAs($this->admin)->get(route('admin.peminjaman.index'));
        $response->assertStatus(200);
        $response->assertSee('Terlambat');
    }

    public function test_date_range_picker_rejects_future_dates_in_peminjaman(): void
    {
        $todayLoan = Loan::create([
            'kode_peminjaman' => 'PINJAM-TODAY-01',
            'nama_peminjam' => 'Peminjam Hari Ini',
            'user_id' => $this->peminjam->id,
            'category_id' => $this->category->id,
            'inventory_id' => $this->inventory->id,
            'tanggal_pinjam' => Carbon::now('Asia/Jakarta'),
            'tanggal_kembali' => Carbon::now('Asia/Jakarta')->addHours(2),
            'status' => 'dipinjam',
        ]);

        $futureStartDate = Carbon::now('Asia/Jakarta')->addDays(2)->format('d/m/Y');
        $futureEndDate = Carbon::now('Asia/Jakarta')->addDays(5)->format('d/m/Y');

        $response = $this->actingAs($this->admin)->get(route('admin.peminjaman.index', [
            'date_range' => "{$futureStartDate} — {$futureEndDate}"
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada data peminjaman ditemukan');
    }
}
