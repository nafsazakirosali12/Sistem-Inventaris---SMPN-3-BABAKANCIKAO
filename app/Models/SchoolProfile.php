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
        'hari_layanan',
        'jam_buka',
        'jam_tutup',
        'hari_operasional',
    ];

    protected $casts = [
        'hari_operasional' => 'array',
    ];

    public function getHariLayananAttribute($value)
    {
        return $value ?: 'Senin s.d. Jumat';
    }

    public function getJamBukaAttribute($value)
    {
        return $value ? substr($value, 0, 5) : '07:00';
    }

    public function getJamTutupAttribute($value)
    {
        return $value ? substr($value, 0, 5) : '13:00';
    }

    public function getHariOperasionalAttribute($value)
    {
        if (empty($value)) {
            return [1, 2, 3, 4, 5];
        }
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [1, 2, 3, 4, 5];
    }
}
