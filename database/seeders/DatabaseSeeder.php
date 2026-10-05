<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\Category;
use App\Models\Room;
use App\Models\Inventory;
use App\Models\Loan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. School Profile
        $profile = SchoolProfile::updateOrCreate(
            ['id' => 1],
            [
                'nama_sekolah' => 'SMPN 3 BABAKANCIKAO',
                'npsn' => '20203040',
                'nama_sistem' => 'Sistem Informasi Inventaris & Peminjaman',
                'tagline' => 'Portal Terpadu Sarana & Prasarana',
                'alamat' => 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat',
                'telepon' => '(0264) 8301234',
                'email' => 'info@smpn3babakancikao.sch.id',
                'provinsi' => 'Jawa Barat',
                'kabupaten_kota' => 'Kabupaten Purwakarta',
                'bidang' => 'Pendidikan',
                'unit_organisasi' => 'Dinas Pendidikan Purwakarta',
                'sub_unit_organisasi' => 'SMPN 3 Babakancikao',
                'upb' => 'SMPN 3 Babakancikao',
                'no_kode_lokasi' => '12.34.56.78.90',
            ]
        );

        // 2. Users
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Sarpras',
                'nip_nuptk' => '198001012005011001',
                'email' => 'admin@smpn3babakancikao.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $guru = User::updateOrCreate(
            ['username' => 'guru'],
            [
                'name' => 'Akun Guru',
                'nip_nuptk' => '198501012010011001',
                'email' => 'guru@smpn3babakancikao.sch.id',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
            ]
        );

        // 3. Categories
        $catElektronik = Category::create([
            'kode_kategori' => 'KAT-ELEK',
            'nama_kategori' => 'Elektronik Multimedia',
            'keterangan' => 'Proyektor, laptop, TV, audio, kamera',
        ]);
        $catIpa = Category::create([
            'kode_kategori' => 'KAT-IPA',
            'nama_kategori' => 'Alat Praktikum Biologi & IPA',
            'keterangan' => 'Mikroskop, alat peraga, timbangan',
        ]);
        $catOlahraga = Category::create([
            'kode_kategori' => 'KAT-OLAH',
            'nama_kategori' => 'Peralatan Olahraga',
            'keterangan' => 'Matras, bola, net, catur',
        ]);
        $catMebel = Category::create([
            'kode_kategori' => 'KAT-MEB',
            'nama_kategori' => 'Mebel & Meja Kursi',
            'keterangan' => 'Meja guru, kursi lipat, lemari',
        ]);

        // 4. Rooms
        $roomLabKom1 = Room::create([
            'kode_ruangan' => 'R-LAB-KOM1',
            'nama_ruangan' => 'Lab Komputer 1',
            'penanggung_jawab' => 'Dra. Hj. Nurjanah',
            'keterangan' => 'Gedung B Lantai 2',
        ]);
        $roomLabIpa = Room::create([
            'kode_ruangan' => 'R-LAB-IPA',
            'nama_ruangan' => 'Lab IPA',
            'penanggung_jawab' => 'Bambang Setiawan, S.Pd',
            'keterangan' => 'Gedung A Lantai 1',
        ]);
        $roomRuangGuru = Room::create([
            'kode_ruangan' => 'R-GURU',
            'nama_ruangan' => 'Ruang Guru',
            'penanggung_jawab' => 'Wakasek Sarpras',
            'keterangan' => 'Gedung Utama',
        ]);
        $roomAula = Room::create([
            'kode_ruangan' => 'R-AULA',
            'nama_ruangan' => 'Aula Sekolah',
            'penanggung_jawab' => 'Rudi Hermawan, S.Pd',
            'keterangan' => 'Gedung C',
        ]);
        $roomGudang = Room::create([
            'kode_ruangan' => 'R-GDG-OLAH',
            'nama_ruangan' => 'Gudang Olahraga',
            'penanggung_jawab' => 'Guru PJOK',
            'keterangan' => 'Area Lapangan',
        ]);

        // 5. Inventories
        $inv1 = Inventory::create([
            'kode_barang' => 'INV-LAB-042',
            'nama_barang' => 'Proyektor Epson EB-X500',
            'category_id' => $catElektronik->id,
            'room_id' => $roomLabKom1->id,
            'status' => 'baik',
            'nomor_register' => 'REG-2023-001',
            'merk_type' => 'Epson EB-X500',
            'ukuran_cc' => 'XGA 3600 Lumens',
            'bahan' => 'Plastik/Logam',
            'tahun_pembelian' => '2023',
            'asal_usul' => 'APBD Kabupaten',
            'harga' => 6500000,
            'keterangan' => 'Kondisi fisik mulus, lampu proyektor normal',
        ]);

        $inv2 = Inventory::create([
            'kode_barang' => 'INV-IPA-015',
            'nama_barang' => 'Mikroskop Olympus CX23',
            'category_id' => $catIpa->id,
            'room_id' => $roomLabIpa->id,
            'status' => 'baik',
            'nomor_register' => 'REG-2022-012',
            'merk_type' => 'Olympus CX23',
            'ukuran_cc' => 'Binokuler LED',
            'bahan' => 'Logam Optik',
            'tahun_pembelian' => '2022',
            'asal_usul' => 'BOS Kinerja',
            'harga' => 12500000,
            'keterangan' => 'Lensa jernih, kabel power lengkap',
        ]);

        $inv3 = Inventory::create([
            'kode_barang' => 'INV-RGU-108',
            'nama_barang' => 'Laptop Asus ExpertBook B1400',
            'category_id' => $catElektronik->id,
            'room_id' => $roomRuangGuru->id,
            'status' => 'baik',
            'nomor_register' => 'REG-2023-045',
            'merk_type' => 'Asus ExpertBook B1400',
            'ukuran_cc' => 'Core i5 14 Inch',
            'bahan' => 'Alumunium Plastik',
            'tahun_pembelian' => '2023',
            'asal_usul' => 'BOS Reguler',
            'harga' => 9800000,
            'keterangan' => 'Tersimpan di Lemari A Ruang Guru',
        ]);

        $inv4 = Inventory::create([
            'kode_barang' => 'INV-OLA-004',
            'nama_barang' => 'Matras Senam Lantai 2x1m',
            'category_id' => $catOlahraga->id,
            'room_id' => $roomGudang->id,
            'status' => 'baik',
            'nomor_register' => 'REG-2021-088',
            'merk_type' => 'Speeds Matras',
            'ukuran_cc' => '200x100x15 cm',
            'bahan' => 'Busa Rebonded / Oscar',
            'tahun_pembelian' => '2021',
            'asal_usul' => 'BOS Reguler',
            'harga' => 1800000,
            'keterangan' => 'Tersedia di Rak C Gudang',
        ]);

        $inv5 = Inventory::create([
            'kode_barang' => 'INV-AUL-021',
            'nama_barang' => 'Pengeras Suara Toa ZS-202C',
            'category_id' => $catElektronik->id,
            'room_id' => $roomAula->id,
            'status' => 'baik',
            'nomor_register' => 'REG-2020-019',
            'merk_type' => 'Toa ZS-202C',
            'ukuran_cc' => 'Column Speaker 20W',
            'bahan' => 'Metal Frame',
            'tahun_pembelian' => '2020',
            'asal_usul' => 'Hibah Komite',
            'harga' => 3200000,
            'keterangan' => 'Dipinjam untuk latihan paskibra',
        ]);

        $inv6 = Inventory::create([
            'kode_barang' => 'INV-LAB-019',
            'nama_barang' => 'PC All-in-One HP 24-df',
            'category_id' => $catElektronik->id,
            'room_id' => $roomLabKom1->id,
            'status' => 'rusak',
            'nomor_register' => 'REG-2021-004',
            'merk_type' => 'HP 24-df0012d',
            'ukuran_cc' => '23.8 inch IPS Core i3',
            'bahan' => 'Plastik Komposisi',
            'tahun_pembelian' => '2021',
            'asal_usul' => 'BOS Reguler',
            'harga' => 8400000,
            'keterangan' => 'Layar bergaris, perlu servis panel LCD',
        ]);

        // 6. Loans (Strictly only 'dipinjam' and 'selesai')
        // Active loan 1 (Dipinjam - Proyektor)
        Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-001',
            'nama_peminjam' => 'Dra. Hj. Nurjanah',
            'user_id' => $guru->id,
            'category_id' => $catElektronik->id,
            'inventory_id' => $inv1->id,
            'tanggal_pinjam' => Carbon::now()->setTime(7, 30),
            'tanggal_kembali' => Carbon::now()->setTime(11, 30),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Pembelajaran Presentasi Matematika di Lab 1',
        ]);

        // Active loan 2 (Dipinjam - Mikroskop)
        Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-002',
            'nama_peminjam' => 'Bambang Setiawan, S.Pd',
            'user_id' => $guru->id,
            'category_id' => $catIpa->id,
            'inventory_id' => $inv2->id,
            'tanggal_pinjam' => Carbon::now()->setTime(9, 0),
            'tanggal_kembali' => Carbon::now()->setTime(14, 0),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Praktikum Pengamatan Sel Biologi',
        ]);

        // Completed loan 1 (Selesai tepat waktu)
        Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-003',
            'nama_peminjam' => 'Rudi Hermawan, S.Pd',
            'user_id' => $guru->id,
            'category_id' => $catElektronik->id,
            'inventory_id' => $inv3->id,
            'tanggal_pinjam' => Carbon::yesterday()->setTime(8, 0),
            'tanggal_kembali' => Carbon::yesterday()->setTime(12, 0),
            'tanggal_kembali_aktual' => Carbon::yesterday()->setTime(11, 45),
            'status' => 'selesai',
            'alasan_tujuan' => 'Kegiatan Gladi Bersih OSIS',
        ]);

        // Completed loan 2 (Selesai terlambat)
        Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-004',
            'nama_peminjam' => 'Bambang Setiawan, S.Pd',
            'user_id' => $guru->id,
            'category_id' => $catOlahraga->id,
            'inventory_id' => $inv4->id,
            'tanggal_pinjam' => Carbon::yesterday()->setTime(9, 0),
            'tanggal_kembali' => Carbon::yesterday()->setTime(11, 0),
            'tanggal_kembali_aktual' => Carbon::yesterday()->setTime(13, 15),
            'status' => 'selesai',
            'alasan_tujuan' => 'Latihan Senam Siswa',
        ]);

        // Active loan 3 (Terlambat - Overdue active)
        Loan::create([
            'kode_peminjaman' => 'PINJAM-2026-005',
            'nama_peminjam' => 'Rudi Hermawan, S.Pd',
            'user_id' => $guru->id,
            'category_id' => $catElektronik->id,
            'inventory_id' => $inv5->id,
            'tanggal_pinjam' => Carbon::yesterday()->setTime(8, 0),
            'tanggal_kembali' => Carbon::yesterday()->setTime(16, 0),
            'status' => 'dipinjam',
            'alasan_tujuan' => 'Acara Apel dan Pembinaan Kesiswaan',
        ]);
    }
}
