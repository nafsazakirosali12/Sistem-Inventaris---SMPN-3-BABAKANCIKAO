<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

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
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.kategori.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_category_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kategori.index'));

        $response->assertStatus(200);
        $response->assertSee('Kategori Barang');
        $response->assertSee('Tambah Kategori');
    }

    public function test_admin_can_create_category(): void
    {
        $testCode = 'KTG-TEST-' . rand(100, 999);
        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'kode_kategori' => $testCode,
            'nama_kategori' => 'Alat Musik Sanggar',
            'keterangan' => 'Gamelan, angklung, dan maracas',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'kode_kategori' => $testCode,
            'nama_kategori' => 'Alat Musik Sanggar',
        ]);
    }

    public function test_admin_cannot_create_duplicate_code(): void
    {
        $existing = Category::first();
        if (!$existing) {
            $existing = Category::create([
                'kode_kategori' => 'KTG-DUP',
                'nama_kategori' => 'Kategori Duplikat',
            ]);
        }

        $response = $this->actingAs($this->admin)->post(route('admin.kategori.store'), [
            'kode_kategori' => $existing->kode_kategori,
            'nama_kategori' => 'Kategori Baru Dengan Kode Sama',
        ]);

        $response->assertSessionHasErrors('kode_kategori');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create([
            'kode_kategori' => 'KTG-UPD-' . rand(100, 999),
            'nama_kategori' => 'Kategori Sebelum Update',
            'keterangan' => 'Deskripsi lama',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.kategori.update', $category), [
            'kode_kategori' => $category->kode_kategori,
            'nama_kategori' => 'Kategori Setelah Update',
            'keterangan' => 'Deskripsi baru',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'nama_kategori' => 'Kategori Setelah Update',
        ]);
    }

    public function test_admin_cannot_delete_category_with_inventories(): void
    {
        $category = Category::whereHas('inventories')->first();
        if (!$category) {
            $category = Category::create([
                'kode_kategori' => 'KTG-INV-' . rand(100, 999),
                'nama_kategori' => 'Kategori Dengan Inventaris',
            ]);
            $room = Room::first() ?? Room::create([
                'kode_ruangan' => 'R-TEST',
                'nama_ruangan' => 'Ruang Test',
            ]);
            Inventory::create([
                'category_id' => $category->id,
                'room_id' => $room->id,
                'kode_barang' => 'BRG-TEST-' . rand(1000, 9999),
                'nama_barang' => 'Laptop Test',
                'status' => 'baik',
            ]);
        }

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $category));

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $emptyCategory = Category::create([
            'kode_kategori' => 'KTG-EMP-' . rand(100, 999),
            'nama_kategori' => 'Kategori Kosong Siap Hapus',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $emptyCategory));

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $emptyCategory->id]);
    }
}
