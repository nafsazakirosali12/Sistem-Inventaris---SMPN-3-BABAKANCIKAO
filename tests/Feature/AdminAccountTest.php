<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin_acc'],
            [
                'name' => 'Admin Account Test',
                'nip_nuptk' => '123456789012345679',
                'email' => 'admin_acc@smpn3babakancikao.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
    }

    public function test_guest_cannot_access_accounts(): void
    {
        $response = $this->get(route('admin.akun.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_peminjam_accounts(): void
    {
        User::create([
            'name' => 'siswapeminjam',
            'username' => 'siswapeminjam',
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.akun.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Peminjam');
        $response->assertSee('siswapeminjam');
    }

    public function test_admin_can_create_peminjam_account(): void
    {
        $username = 'peminjam_new_' . rand(100, 999);
        $response = $this->actingAs($this->admin)->post(route('admin.akun.store'), [
            'username' => $username,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.akun.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => $username,
            'role' => 'peminjam',
        ]);
    }

    public function test_admin_can_update_peminjam_account(): void
    {
        $user = User::create([
            'name' => 'user_old',
            'username' => 'user_upd_' . rand(100, 999),
            'password' => bcrypt('oldpassword'),
            'role' => 'peminjam',
        ]);

        $newUsername = 'user_new_' . rand(100, 999);
        $response = $this->actingAs($this->admin)->put(route('admin.akun.update', $user), [
            'username' => $newUsername,
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.akun.index'));
        $response->assertSessionHas('success');

        $updatedUser = $user->fresh();
        $this->assertEquals($newUsername, $updatedUser->username);
        $this->assertTrue(Hash::check('newpassword123', $updatedUser->password));
    }

    public function test_admin_can_delete_peminjam_account(): void
    {
        $user = User::create([
            'name' => 'user_del',
            'username' => 'user_del_' . rand(100, 999),
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.akun.destroy', $user));

        $response->assertRedirect(route('admin.akun.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
