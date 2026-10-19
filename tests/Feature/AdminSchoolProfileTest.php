<?php

namespace Tests\Feature;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['username' => 'test_admin_profile'],
            [
                'name' => 'Admin Profile Test',
                'nip_nuptk' => '123456789012345680',
                'email' => 'admin_profile@smpn3babakancikao.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );
    }

    public function test_guest_cannot_access_school_profile(): void
    {
        $response = $this->get(route('admin.profil.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_school_profile(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profil.index'));

        $response->assertStatus(200);
        $response->assertSee('Profil Sekolah');
        $response->assertSee('Mode Baca (Read-Only)');
    }

    public function test_admin_can_update_school_profile(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO UPDATED',
            'npsn' => '87654321',
            'alamat' => 'Jl. Industri Ubrug No. 123, Purwakarta',
            'telepon' => '02641234567',
            'email' => 'info_new@smpn3babakancikao.sch.id',
            'instagram' => '@smpn3_official',
            'youtube' => '@smpn3_channel',
            'provinsi' => 'Jawa Barat',
            'kabupaten_kota' => 'Kabupaten Purwakarta',
            'bidang' => 'Pendidikan',
            'unit_organisasi' => 'Dinas Pendidikan',
            'sub_unit_organisasi' => 'SMPN 3 Babakancikao',
            'upb' => 'SMPN 3 Babakancikao',
            'no_kode_lokasi' => '12.34.56.78.90',
            'nama_kepala_sekolah' => 'Drs. H. Ahmad Dahlan, M.Pd',
            'nip_kepala_sekolah' => '19750101 200003 1 001',
            'nama_pembuat' => 'Budi Santoso, S.Kom',
            'nip_pembuat' => '19820512 201001 2 005',
        ]);

        $response->assertRedirect(route('admin.profil.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('school_profiles', [
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO UPDATED',
            'npsn' => '87654321',
            'instagram' => '@smpn3_official',
            'nama_kepala_sekolah' => 'Drs. H. Ahmad Dahlan, M.Pd',
            'nip_kepala_sekolah' => '19750101 200003 1 001',
            'nama_pembuat' => 'Budi Santoso, S.Kom',
            'nip_pembuat' => '19820512 201001 2 005',
        ]);
    }

    public function test_npsn_must_be_8_digits(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
            'npsn' => '12345', // Only 5 digits
            'alamat' => 'Jl. Industri Ubrug',
        ]);

        $response->assertSessionHasErrors('npsn');
    }
}
