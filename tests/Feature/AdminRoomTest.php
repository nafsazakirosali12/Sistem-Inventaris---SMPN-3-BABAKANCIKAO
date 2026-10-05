<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoomTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin_room'],
            [
                'name' => 'Admin Room Test',
                'nip_nuptk' => '123456789012345678',
                'email' => 'admin_room@smpn3babakancikao.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
    }

    public function test_guest_cannot_access_rooms(): void
    {
        $response = $this->get(route('admin.ruangan.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_room_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ruangan.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Ruangan');
        $response->assertSee('Tambah Ruangan');
    }

    public function test_admin_can_create_room(): void
    {
        $testCode = 'RUG-TEST-' . rand(100, 999);
        $response = $this->actingAs($this->admin)->post(route('admin.ruangan.store'), [
            'kode_ruangan' => $testCode,
            'nama_ruangan' => 'Laboratorium Multimedia',
            'penanggung_jawab' => 'Budi Santoso, S.Kom',
            'keterangan' => 'Lantai 2 Gedung Utama',
        ]);

        $response->assertRedirect(route('admin.ruangan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'kode_ruangan' => $testCode,
            'nama_ruangan' => 'Laboratorium Multimedia',
            'penanggung_jawab' => 'Budi Santoso, S.Kom',
        ]);
    }

    public function test_admin_can_update_room(): void
    {
        $room = Room::create([
            'kode_ruangan' => 'RUG-OLD-' . rand(100, 999),
            'nama_ruangan' => 'Ruang Teori Lama',
            'penanggung_jawab' => 'PJ Lama',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.ruangan.update', $room), [
            'kode_ruangan' => $room->kode_ruangan,
            'nama_ruangan' => 'Ruang Teori Baru',
            'penanggung_jawab' => 'PJ Baru',
            'keterangan' => 'Diperbarui',
        ]);

        $response->assertRedirect(route('admin.ruangan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'nama_ruangan' => 'Ruang Teori Baru',
            'penanggung_jawab' => 'PJ Baru',
        ]);
    }

    public function test_admin_cannot_delete_room_with_inventories(): void
    {
        $room = Room::create([
            'kode_ruangan' => 'RUG-INV-' . rand(100, 999),
            'nama_ruangan' => 'Ruangan Berisi Barang',
        ]);

        $category = Category::create([
            'kode_kategori' => 'KTG-ROOM-' . rand(100, 999),
            'nama_kategori' => 'Kategori Ruangan',
        ]);

        Inventory::create([
            'category_id' => $category->id,
            'room_id' => $room->id,
            'kode_barang' => 'BRG-RUG-' . rand(1000, 9999),
            'nama_barang' => 'Proyektor BenQ',
            'status' => 'baik',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.ruangan.destroy', $room));

        $response->assertRedirect(route('admin.ruangan.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
    }

    public function test_admin_can_delete_empty_room(): void
    {
        $emptyRoom = Room::create([
            'kode_ruangan' => 'RUG-EMP-' . rand(100, 999),
            'nama_ruangan' => 'Gudang Kosong',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.ruangan.destroy', $emptyRoom));

        $response->assertRedirect(route('admin.ruangan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('rooms', ['id' => $emptyRoom->id]);
    }
}
