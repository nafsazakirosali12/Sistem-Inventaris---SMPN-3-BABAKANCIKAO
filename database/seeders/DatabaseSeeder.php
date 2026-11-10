<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolProfile;
use App\Models\Category;
use App\Models\Room;
use App\Models\Inventory;
use App\Models\Loan;
use App\Models\LoanDetail;
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
                'tagline' => 'Portal Sarana & Prasarana',
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
                'nama_kepala_sekolah' => 'Drs. H. Ahmad Dahlan, M.Pd',
                'nip_kepala_sekolah' => '19720315 199802 1 002',
                'nama_pembuat' => 'Admin Sarpras',
                'nip_pembuat' => '19800101 200501 1 001',
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

        // 3. Categories (Nama jenis/kelompok barang secara umum, e.g. Laptop, Proyektor, dll)
        $catLaptop = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-LAPTOP'],
            ['nama_kategori' => 'Laptop', 'keterangan' => 'Perangkat laptop dan notebook untuk pembelajaran dan operasional sekolah']
        );

        $catProyektor = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-PROYEKTOR'],
            ['nama_kategori' => 'Proyektor', 'keterangan' => 'Perangkat proyektor LCD dan perlengkapan presentasi visual']
        );

        $catMatras = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-MATRAS'],
            ['nama_kategori' => 'Matras', 'keterangan' => 'Matras senam lantai dan perlengkapan olahraga siswa']
        );

        $catSpeaker = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-SPEAKER'],
            ['nama_kategori' => 'Speaker', 'keterangan' => 'Pengeras suara, portable sound system, dan audio aula']
        );

        $catKomputer = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-KOMPUTER'],
            ['nama_kategori' => 'Komputer', 'keterangan' => 'Perangkat komputer desktop PC dan All-in-One laboratorium']
        );

        $catMikroskop = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-MIKROSKOP'],
            ['nama_kategori' => 'Mikroskop', 'keterangan' => 'Peralatan mikroskop laboratorium IPA untuk praktikum siswa']
        );

        $catKamera = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-KAMERA'],
            ['nama_kategori' => 'Kamera', 'keterangan' => 'Kamera digital untuk dokumentasi kegiatan dan publikasi sekolah']
        );

        $catPrinter = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-PRINTER'],
            ['nama_kategori' => 'Printer', 'keterangan' => 'Printer dan scanner untuk pencetakan dokumen administrasi sekolah']
        );

        $catMikrofon = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-MIKROFON'],
            ['nama_kategori' => 'Mikrofon', 'keterangan' => 'Mikrofon kabel, wireless microphone, dan clip-on']
        );

        $catTablet = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-TABLET'],
            ['nama_kategori' => 'Tablet', 'keterangan' => 'Tablet edukasi dan perangkat mobile layar sentuh']
        );

        $catKeyboard = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-KEYBOARD'],
            ['nama_kategori' => 'Keyboard', 'keterangan' => 'Keyboard komputer USB, wireless, dan periferal input']
        );

        $catMouse = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-MOUSE'],
            ['nama_kategori' => 'Mouse', 'keterangan' => 'Mouse optik USB, mouse wireless, dan touchpad']
        );

        $catMeja = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-MEJA'],
            ['nama_kategori' => 'Meja', 'keterangan' => 'Meja guru, meja kerja, dan meja komputer']
        );

        $catKursi = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-KURSI'],
            ['nama_kategori' => 'Kursi', 'keterangan' => 'Kursi lipat rapat, kursi guru, dan kursi susun']
        );

        $catLemari = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-LEMARI'],
            ['nama_kategori' => 'Lemari', 'keterangan' => 'Lemari arsip besi, lemari kaca, dan rak penyimpanan']
        );

        $catAksesoris = Category::updateOrCreate(
            ['kode_kategori' => 'KAT-AKSESORIS'],
            ['nama_kategori' => 'Kabel dan Aksesoris', 'keterangan' => 'Kabel HDMI, VGA, kabel rol, power strip, dan konverter']
        );

        // 4. Rooms
        $roomLabKom1 = Room::updateOrCreate(
            ['kode_ruangan' => 'R-LAB-KOM1'],
            ['nama_ruangan' => 'Lab Komputer 1', 'penanggung_jawab' => 'Dra. Hj. Nurjanah', 'keterangan' => 'Gedung B Lantai 2']
        );

        $roomLabIpa = Room::updateOrCreate(
            ['kode_ruangan' => 'R-LAB-IPA'],
            ['nama_ruangan' => 'Lab IPA', 'penanggung_jawab' => 'Bambang Setiawan, S.Pd', 'keterangan' => 'Gedung A Lantai 1']
        );

        $roomRuangGuru = Room::updateOrCreate(
            ['kode_ruangan' => 'R-GURU'],
            ['nama_ruangan' => 'Ruang Guru', 'penanggung_jawab' => 'Wakasek Sarpras', 'keterangan' => 'Gedung Utama']
        );

        $roomAula = Room::updateOrCreate(
            ['kode_ruangan' => 'R-AULA'],
            ['nama_ruangan' => 'Aula Sekolah', 'penanggung_jawab' => 'Rudi Hermawan, S.Pd', 'keterangan' => 'Gedung C']
        );

        $roomGudang = Room::updateOrCreate(
            ['kode_ruangan' => 'R-GDG-OLAH'],
            ['nama_ruangan' => 'Gudang Olahraga', 'penanggung_jawab' => 'Guru PJOK', 'keterangan' => 'Area Lapangan']
        );

        // 5. Inventories (Data inventaris = unit fisik spesifik, e.g. Laptop Asus Vivobook A11, Proyektor Epson EB-X500)
        // 5.1 Kategori: Proyektor
        $inv1 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-LAB-042'],
            [
                'nama_barang' => 'Proyektor Epson EB-X500',
                'category_id' => $catProyektor->id,
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
            ]
        );

        $invProyektor2 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-LAB-043'],
            [
                'nama_barang' => 'Proyektor BenQ MX560',
                'category_id' => $catProyektor->id,
                'room_id' => $roomLabKom1->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2024-002',
                'merk_type' => 'BenQ MX560 XGA',
                'ukuran_cc' => '4000 ANSI Lumens',
                'bahan' => 'Plastik Komposit',
                'tahun_pembelian' => '2024',
                'asal_usul' => 'BOS Kinerja',
                'harga' => 6200000,
                'keterangan' => 'Lensa jernih, dilengkapi tas dan kabel HDMI',
            ]
        );

        // 5.2 Kategori: Mikroskop
        $inv2 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-IPA-015'],
            [
                'nama_barang' => 'Mikroskop Olympus CX23',
                'category_id' => $catMikroskop->id,
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
            ]
        );

        // 5.3 Kategori: Laptop
        $inv3 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-RGU-108'],
            [
                'nama_barang' => 'Laptop Asus Vivobook A11',
                'category_id' => $catLaptop->id,
                'room_id' => $roomRuangGuru->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2023-045',
                'merk_type' => 'Asus Vivobook A11',
                'ukuran_cc' => 'Core i5 14 Inch',
                'bahan' => 'Alumunium Plastik',
                'tahun_pembelian' => '2023',
                'asal_usul' => 'BOS Reguler',
                'harga' => 9800000,
                'keterangan' => 'Tersimpan di Lemari A Ruang Guru',
            ]
        );

        $invLaptop2 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-RGU-109'],
            [
                'nama_barang' => 'Laptop Lenovo ThinkPad E14',
                'category_id' => $catLaptop->id,
                'room_id' => $roomRuangGuru->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2023-046',
                'merk_type' => 'Lenovo ThinkPad E14 Gen 4',
                'ukuran_cc' => 'Core i5 14 Inch',
                'bahan' => 'Alumunium Plastik',
                'tahun_pembelian' => '2023',
                'asal_usul' => 'BOS Reguler',
                'harga' => 10500000,
                'keterangan' => 'Siap pakai untuk pembelajaran di kelas',
            ]
        );

        $invLaptop3 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-RGU-110'],
            [
                'nama_barang' => 'Laptop Acer Aspire 5',
                'category_id' => $catLaptop->id,
                'room_id' => $roomRuangGuru->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2024-051',
                'merk_type' => 'Acer Aspire 5 A514-55G',
                'ukuran_cc' => 'Core i5 14 Inch',
                'bahan' => 'Plastik Komposit',
                'tahun_pembelian' => '2024',
                'asal_usul' => 'BOS Reguler',
                'harga' => 8700000,
                'keterangan' => 'Kondisi prima dengan charger original',
            ]
        );

        // 5.4 Kategori: Matras
        $inv4 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-OLA-004'],
            [
                'nama_barang' => 'Matras Senam Lantai Speeds 2x1m',
                'category_id' => $catMatras->id,
                'room_id' => $roomGudang->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2021-088',
                'merk_type' => 'Speeds Matras Senam 2x1m',
                'ukuran_cc' => '200x100x15 cm',
                'bahan' => 'Busa Rebonded / Oscar',
                'tahun_pembelian' => '2021',
                'asal_usul' => 'BOS Reguler',
                'harga' => 1800000,
                'keterangan' => 'Tersedia di Rak C Gudang',
            ]
        );

        $invMatras2 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-OLA-005'],
            [
                'nama_barang' => 'Matras Yoga Kettler 6mm',
                'category_id' => $catMatras->id,
                'room_id' => $roomGudang->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2022-090',
                'merk_type' => 'Kettler Yoga Mat 6mm',
                'ukuran_cc' => '173x61x0.6 cm',
                'bahan' => 'TPE Eco-Friendly',
                'tahun_pembelian' => '2022',
                'asal_usul' => 'BOS Reguler',
                'harga' => 450000,
                'keterangan' => 'Matras senam kelenturan dan yoga siswa',
            ]
        );

        // 5.5 Kategori: Speaker
        $inv5 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-AUL-021'],
            [
                'nama_barang' => 'Speaker Column TOA ZS-202C',
                'category_id' => $catSpeaker->id,
                'room_id' => $roomAula->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2020-019',
                'merk_type' => 'TOA Column Speaker ZS-202C',
                'ukuran_cc' => 'Column Speaker 20W',
                'bahan' => 'Metal Frame',
                'tahun_pembelian' => '2020',
                'asal_usul' => 'Hibah Komite',
                'harga' => 3200000,
                'keterangan' => 'Tersimpan di ruang sound system Aula',
            ]
        );

        // 5.6 Kategori: Komputer
        $inv6 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-LAB-019'],
            [
                'nama_barang' => 'Komputer All-in-One HP 24-df0012d',
                'category_id' => $catKomputer->id,
                'room_id' => $roomLabKom1->id,
                'status' => 'rusak',
                'nomor_register' => 'REG-2021-004',
                'merk_type' => 'HP All-in-One 24-df0012d',
                'ukuran_cc' => '23.8 inch IPS Core i3',
                'bahan' => 'Plastik Komposisi',
                'tahun_pembelian' => '2021',
                'asal_usul' => 'BOS Reguler',
                'harga' => 8400000,
                'keterangan' => 'Layar bergaris, perlu servis panel LCD',
            ]
        );

        $invKomputer2 = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-LAB-020'],
            [
                'nama_barang' => 'Komputer Desktop Lenovo IdeaCentre 3',
                'category_id' => $catKomputer->id,
                'room_id' => $roomLabKom1->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2024-015',
                'merk_type' => 'Lenovo IdeaCentre 3 07IAB7',
                'ukuran_cc' => 'Core i3 Desktop PC',
                'bahan' => 'Plastik Matte',
                'tahun_pembelian' => '2024',
                'asal_usul' => 'BOS Kinerja',
                'harga' => 7800000,
                'keterangan' => 'Set lengkap CPU, monitor, keyboard, dan mouse',
            ]
        );

        // 5.7 Kategori: Kamera
        $invKamera = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-AUL-011'],
            [
                'nama_barang' => 'Kamera Canon EOS 1500D',
                'category_id' => $catKamera->id,
                'room_id' => $roomAula->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2023-011',
                'merk_type' => 'Canon EOS 1500D Kit 18-55mm',
                'ukuran_cc' => '24.1 Megapixel APS-C',
                'bahan' => 'Magnesium Alloy / Polycarbonate',
                'tahun_pembelian' => '2023',
                'asal_usul' => 'BOS Kinerja',
                'harga' => 6800000,
                'keterangan' => 'Lengkap dengan lensa kit, charger, tas, dan memori 32GB',
            ]
        );

        // 5.8 Kategori: Printer
        $invPrinter = Inventory::updateOrCreate(
            ['kode_barang' => 'INV-RGU-022'],
            [
                'nama_barang' => 'Printer Epson EcoTank L3210',
                'category_id' => $catPrinter->id,
                'room_id' => $roomRuangGuru->id,
                'status' => 'baik',
                'nomor_register' => 'REG-2023-022',
                'merk_type' => 'Epson EcoTank L3210 All-in-One',
                'ukuran_cc' => 'A4 Color Scanner/Printer',
                'bahan' => 'Plastik ABS',
                'tahun_pembelian' => '2023',
                'asal_usul' => 'BOS Reguler',
                'harga' => 2400000,
                'keterangan' => 'Tinta original terisi penuh, siap cetak dokumen sekolah',
            ]
        );

        // 6. Loans (Strictly only 'dipinjam' and 'selesai')
        // Active loan 1 (Dipinjam - Proyektor Epson EB-X500)
        $loan1 = Loan::updateOrCreate(
            ['kode_peminjaman' => 'PINJAM-2026-001'],
            [
                'nama_peminjam' => 'Dra. Hj. Nurjanah',
                'user_id' => $guru->id,
                'category_id' => $catProyektor->id,
                'inventory_id' => $inv1->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::now()->setTime(7, 30),
                'tanggal_kembali' => Carbon::now()->setTime(11, 30),
                'status' => 'dipinjam',
                'alasan_tujuan' => 'Pembelajaran Presentasi Matematika di Lab 1',
            ]
        );
        LoanDetail::updateOrCreate(
            ['loan_id' => $loan1->id, 'kode_barang' => $inv1->kode_barang],
            ['inventory_id' => $inv1->id]
        );

        // Active loan 2 (Dipinjam - Mikroskop Olympus CX23)
        $loan2 = Loan::updateOrCreate(
            ['kode_peminjaman' => 'PINJAM-2026-002'],
            [
                'nama_peminjam' => 'Bambang Setiawan, S.Pd',
                'user_id' => $guru->id,
                'category_id' => $catMikroskop->id,
                'inventory_id' => $inv2->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::now()->setTime(9, 0),
                'tanggal_kembali' => Carbon::now()->setTime(14, 0),
                'status' => 'dipinjam',
                'alasan_tujuan' => 'Praktikum Pengamatan Sel Biologi',
            ]
        );
        LoanDetail::updateOrCreate(
            ['loan_id' => $loan2->id, 'kode_barang' => $inv2->kode_barang],
            ['inventory_id' => $inv2->id]
        );

        // Completed loan 1 (Selesai tepat waktu - Laptop ASUS Vivobook 14)
        $loan3 = Loan::updateOrCreate(
            ['kode_peminjaman' => 'PINJAM-2026-003'],
            [
                'nama_peminjam' => 'Rudi Hermawan, S.Pd',
                'user_id' => $guru->id,
                'category_id' => $catLaptop->id,
                'inventory_id' => $inv3->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::yesterday()->setTime(8, 0),
                'tanggal_kembali' => Carbon::yesterday()->setTime(12, 0),
                'tanggal_kembali_aktual' => Carbon::yesterday()->setTime(11, 45),
                'status' => 'selesai',
                'alasan_tujuan' => 'Kegiatan Gladi Bersih OSIS',
            ]
        );
        LoanDetail::updateOrCreate(
            ['loan_id' => $loan3->id, 'kode_barang' => $inv3->kode_barang],
            ['inventory_id' => $inv3->id]
        );

        // Completed loan 2 (Selesai terlambat - Matras Speeds Senam Lantai)
        $loan4 = Loan::updateOrCreate(
            ['kode_peminjaman' => 'PINJAM-2026-004'],
            [
                'nama_peminjam' => 'Bambang Setiawan, S.Pd',
                'user_id' => $guru->id,
                'category_id' => $catMatras->id,
                'inventory_id' => $inv4->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::yesterday()->setTime(9, 0),
                'tanggal_kembali' => Carbon::yesterday()->setTime(11, 0),
                'tanggal_kembali_aktual' => Carbon::yesterday()->setTime(13, 15),
                'status' => 'selesai',
                'alasan_tujuan' => 'Latihan Senam Siswa',
            ]
        );
        LoanDetail::updateOrCreate(
            ['loan_id' => $loan4->id, 'kode_barang' => $inv4->kode_barang],
            ['inventory_id' => $inv4->id]
        );

        // Active loan 3 (Terlambat - Overdue active - Speaker TOA ZS-202C)
        $loan5 = Loan::updateOrCreate(
            ['kode_peminjaman' => 'PINJAM-2026-005'],
            [
                'nama_peminjam' => 'Rudi Hermawan, S.Pd',
                'user_id' => $guru->id,
                'category_id' => $catSpeaker->id,
                'inventory_id' => $inv5->id,
                'jumlah' => 1,
                'tanggal_pinjam' => Carbon::yesterday()->setTime(8, 0),
                'tanggal_kembali' => Carbon::yesterday()->setTime(16, 0),
                'status' => 'dipinjam',
                'alasan_tujuan' => 'Acara Apel dan Pembinaan Kesiswaan',
            ]
        );
        LoanDetail::updateOrCreate(
            ['loan_id' => $loan5->id, 'kode_barang' => $inv5->kode_barang],
            ['inventory_id' => $inv5->id]
        );
    }
}
