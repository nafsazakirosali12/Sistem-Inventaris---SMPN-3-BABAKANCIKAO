<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'nama_sistem',
        'tagline',
        'alamat',
        'telepon',
        'email',
        'logo',
        'foto',
        'denah',
        'provinsi',
        'kabupaten_kota',
        'bidang',
        'unit_organisasi',
        'sub_unit_organisasi',
        'upb',
        'no_kode_lokasi',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'nama_pembuat',
        'nip_pembuat',
        'instagram',
        'youtube',
        'facebook',
        'tiktok',
    ];
}
