<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SchoolProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Sarpras',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        SchoolProfile::create([
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'npsn' => '20203040',
            'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
            'alamat' => 'Jl. Babakancikao',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get(route('admin.profile.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Profil Admin');
        $response->assertSee('admin');
        $response->assertSee('••••••••');
        $response->assertSee('Edit Profil');
    }

    public function test_admin_can_update_username_and_name(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => 'Super Admin Sarpras',
            'username' => 'superadmin',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Super Admin Sarpras',
            'username' => 'superadmin',
        ]);
    }

    public function test_admin_can_update_password_with_hashing(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'password' => 'newsecretpass',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('newsecretpass', $this->admin->password));
    }

    public function test_admin_cannot_use_duplicate_username(): void
    {
        User::factory()->create([
            'username' => 'anotheruser',
            'role' => 'peminjam',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => $this->admin->name,
            'username' => 'anotheruser',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_admin_can_upload_profile_photo(): void
    {
        $photo = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => $this->admin->name,
            'username' => $this->admin->username,
            'foto' => $photo,
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertNotNull($this->admin->avatar);
        $this->assertFileExists(public_path($this->admin->avatar));

        // Cleanup
        if (file_exists(public_path($this->admin->avatar))) {
            unlink(public_path($this->admin->avatar));
        }
    }
}
