# DESIGN.md
**Sistem Inventaris & Peminjaman, SMPN 3 Babakancikao**
Palet: **Teal Modern**

Dokumen ini adalah acuan tunggal tampilan untuk role Peminjam dan Admin. Semua warna, ukuran, dan komponen dipakai lewat token di bawah, bukan nilai langsung di halaman. Perilaku fitur ada di `flow-sistem-inventaris.md`.

---

## 1. Prinsip Desain

1. **Resmi tapi ramah.** Aplikasi instansi sekolah negeri: rapi, tenang, mudah dibaca guru dari segala usia.
2. **Satu warna utama.** Teal untuk aksi; warna lain hanya untuk status dan perhatian.
3. **Aksen hemat.** Amber hanya untuk hal yang perlu segera dilihat (notifikasi, baris baru).
4. **Warna tidak berdiri sendiri.** Status selalu memakai label teks, bukan hanya warna.
5. **Konsisten.** Satu pola untuk satu jenis aksi (tambah, edit, hapus, konfirmasi) di semua halaman.
6. **Ramah ponsel.** Peminjam kemungkinan membuka dari HP; admin dari laptop.

---

## 2. Token Warna

### 2.1 Warna merek

| Token | Hex | Pemakaian |
|---|---|---|
| `--color-primary` | `#0F766E` | Tombol utama, link, tab/menu aktif, fokus |
| `--color-primary-hover` | `#115E59` | Hover/pressed tombol utama |
| `--color-primary-tint` | `#CCFBF1` | Latar badge/hover ringan, ikon berlatar |
| `--color-primary-soft` | `#F0FDFA` | Latar baris terpilih, area info |
| `--color-sidebar` | `#134E4A` | Sidebar admin, navbar, footer, latar login |
| `--color-sidebar-text` | `#CCFBF1` | Teks/ikon menu sidebar tidak aktif |
| `--color-accent` | `#F59E0B` | Titik notifikasi, highlight baris baru |
| `--color-accent-tint` | `#FEF3C7` | Latar baris yang di-highlight |

### 2.2 Netral

| Token | Hex | Pemakaian |
|---|---|---|
| `--color-bg` | `#F3F7F6` | Latar halaman |
| `--color-surface` | `#FFFFFF` | Card, tabel, pop-up, form |
| `--color-border` | `#D9E4E2` | Garis card, tabel, input |
| `--color-border-strong` | `#B7C9C6` | Hover input, pemisah tegas |
| `--color-text` | `#1F2937` | Teks utama |
| `--color-text-secondary` | `#4B5563` | Teks pendukung, label |
| `--color-text-muted` | `#6B7280` | Placeholder, keterangan kecil |
| `--color-text-on-primary` | `#FFFFFF` | Teks di atas teal dan sidebar |
| `--color-text-on-accent` | `#1F2937` | Teks di atas amber (jangan putih) |

### 2.3 Status

Badge memakai latar lembut + teks gelap dari keluarga warna yang sama. Warna solid dipakai untuk diagram dan titik indikator.

| Status | Latar badge | Teks badge | Solid (diagram/titik) |
|---|---|---|---|
| Tersedia / Selesai / Baik | `#DCFCE7` | `#166534` | `#16A34A` |
| Menunggu | `#FEF9C3` | `#854D0E` | `#EAB308` |
| Dipinjam | `#DBEAFE` | `#1E40AF` | `#2563EB` |
| Terlambat / Rusak / Ditolak | `#FEE2E2` | `#991B1B` | `#DC2626` |

> Status **Dipinjam** sengaja biru, bukan teal, agar tidak menyatu dengan warna tombol.

### 2.4 Umpan balik (alert)

| Jenis | Ikon | Warna |
|---|---|---|
| Sukses | check-circle | `#16A34A` |
| Peringatan / konfirmasi | alert-triangle | `#F59E0B` |
| Bahaya / hapus | trash / x-circle | `#DC2626` |
| Info | info | `#2563EB` |

### 2.5 Kontras (sudah dicek)

- Putih di atas `#0F766E` dan `#134E4A`: lolos AA.
- `#1F2937` di atas `#F59E0B`: lolos AA. **Teks putih di atas amber tidak boleh dipakai.**
- Teks badge status di atas latar lembutnya: lolos AA.
- `#6B7280` hanya untuk teks pendukung berukuran 13px ke atas.

---

## 3. Tipografi

**Font:** `Inter`, cadangan `system-ui, -apple-system, "Segoe UI", sans-serif`. Dua bobot saja: 400 dan 600.

| Token | Ukuran / tinggi baris | Bobot | Pemakaian |
|---|---|---|---|
| `--text-display` | 28 / 36 | 600 | Judul hero Home |
| `--text-h1` | 24 / 32 | 600 | Judul halaman |
| `--text-h2` | 20 / 28 | 600 | Judul card, pop-up |
| `--text-h3` | 16 / 24 | 600 | Sub judul, header tabel |
| `--text-body` | 14 / 22 | 400 | Isi umum, tabel, form |
| `--text-body-lg` | 16 / 26 | 400 | Teks peminjam (Home, katalog) |
| `--text-small` | 13 / 20 | 400 | Keterangan, label kecil |
| `--text-caption` | 12 / 16 | 400 | Badge, footer |

Aturan: kapitalisasi kalimat biasa (bukan HURUF BESAR SEMUA), kecuali nama sekolah di navbar dan kop laporan yang memang resmi kapital.

---

## 4. Spasi, Bentuk, dan Bayangan

**Skala spasi (px):** 4, 8, 12, 16, 24, 32, 48. Jarak antar card 16 (ponsel) / 24 (laptop).

| Token | Nilai |
|---|---|
| `--radius-sm` (badge, chip) | 6px |
| `--radius-md` (input, tombol) | 8px |
| `--radius-lg` (card, pop-up) | 12px |
| `--radius-full` (avatar, titik) | 999px |
| `--shadow-card` | `0 1px 2px rgba(19,78,74,0.06)` |
| `--shadow-popup` | `0 12px 32px rgba(19,78,74,0.18)` |
| `--focus-ring` | `0 0 0 3px rgba(15,118,110,0.35)` |

Gaya datar: hanya dua bayangan di atas, tanpa gradien. Garis card 1px `--color-border`.

**Ikon:** Lucide, garis (outline), 20px standar, 16px di dalam tombol kecil/badge, 24px maksimal.

---

## 5. Layout dan Responsif

| Breakpoint | Lebar | Perilaku |
|---|---|---|
| Ponsel | < 640px | Satu kolom, tabel admin bisa digeser horizontal |
| Tablet | 640-1023px | Katalog 2 kolom; sidebar admin jadi laci (drawer) |
| Laptop | ≥ 1024px | Katalog 3-4 kolom; sidebar admin tetap terlihat |

- **Peminjam:** kontainer maks. 1200px, di tengah, padding samping 16 (ponsel) / 24 (laptop).
- **Admin:** sidebar lebar 256px (tetap), konten mengisi sisanya, padding 24px.
- **Sentuh:** target ketuk minimal 44×44px di ponsel.

---

## 6. Komponen

### 6.1 Navbar
- Tinggi 64px, latar `--color-sidebar`, teks putih.
- **Kiri:** logo sekolah (tinggi 40px) + nama sekolah (di admin: **SMPN 3 BABAKANCIKAO**).
- **Kanan:** admin = lonceng + burger; peminjam = burger saja.
- **Dropdown burger:** nama akun, Profil (admin), Logout (pemisah garis di atas Logout).
- **Lonceng:** titik amber `#F59E0B` (10px) di pojok kanan atas ikon bila ada peminjaman baru.

### 6.2 Sidebar admin
- Latar `--color-sidebar`. Item: ikon + label, tinggi 44px, radius 8px, jarak 4px.
- Tidak aktif: teks `--color-sidebar-text`. Hover: latar `rgba(255,255,255,0.08)`.
- **Aktif:** latar `--color-primary`, teks putih.
- Urutan: Dashboard, Profil Sekolah, Kategori, Ruangan, Data Inventaris, Data Peminjaman, Daftar Akun, Laporan.
- Tablet/ponsel: jadi laci yang dibuka dari tombol menu di navbar.

### 6.3 Tombol

| Varian | Tampilan | Dipakai untuk |
|---|---|---|
| Primary | Latar teal, teks putih | Simpan, Ajukan, Masuk, Unduh |
| Secondary | Latar putih, garis teal, teks teal | Detail, Edit, Kembali |
| Ghost | Tanpa latar, teks teal | Batal, aksi tersier |
| Danger | Latar `#DC2626`, teks putih | Hapus (di alert konfirmasi) |

Tinggi 40px (ponsel 44px), radius 8px, label kata kerja 1-3 kata ("Ajukan pinjaman", "Simpan"). Satu tombol Primary per area. Saat proses berjalan: spinner di dalam tombol dan tombol tidak bisa ditekan ulang. Nonaktif: latar `#E5E7EB`, teks `#9CA3AF`.

### 6.4 Form
- Label di atas input, 13px/600. Tanda wajib: `*` merah.
- Input tinggi 40px, garis `--color-border`, radius 8px, latar putih; fokus: garis teal + `--focus-ring`.
- Error: garis `#DC2626`, pesan 13px merah di bawah input ("Nama peminjam minimal 3 karakter").
- Input password punya tombol tampil/sembunyikan.
- Upload gambar: kotak putus-putus, pratinjau muncul langsung, tampilkan batas format (JPG/PNG/WebP) dan ukuran.
- **Counter jumlah** (pop-up pinjam): tombol − dan + di kiri/kanan angka, batas atas = stok tersedia.
- **Daterangepicker:** kalender putih, rentang terpilih memakai `--color-primary-tint`, ujung rentang `--color-primary`.

### 6.5 Card

**Card umum:** latar putih, garis `--color-border`, radius 12px, padding 16-24px, `--shadow-card`.

**Card katalog (peminjam), per kategori:**
1. Gambar rasio 4:3 di atas (radius atas 12px).
2. Nama kategori (16/600), ruangan (13px, secondary).
3. Badge stok: "Stok 7" (hijau) atau "Habis" (merah).
4. Dua tombol sejajar: **Detail** (Secondary) dan **Pinjam** (Primary). Saat stok 0, Pinjam tampil nonaktif dengan teks "Habis".
- Kode barang **tidak** tampil di card.

**Card riwayat (peminjam):** nama peminjam, kategori, jumlah unit, tanggal kembali, badge status. Baris baru diajukan diberi latar `--color-accent-tint` dan garis kiri 3px `--color-accent` selama beberapa detik atau sampai dibuka.

**Card statistik (dashboard admin):** ikon dalam lingkaran `--color-primary-tint`, angka besar 28/600, label 13px secondary.

### 6.6 Badge status
Radius 6px, padding 2px 8px, 12px/600, titik 6px di kiri. Warna sesuai tabel 2.3. Label baku: **Tersedia, Menunggu, Dipinjam, Selesai, Terlambat, Ditolak, Baik, Rusak**.

### 6.7 Tabel (admin)
- Header latar `--color-primary-soft`, teks 14/600, garis bawah `--color-border`.
- Baris tinggi min. 52px, garis pemisah tipis, hover `#F8FBFA`.
- Kolom aksi rata kanan, ikon tombol 36px: **Detail** (mata, teal), **Edit** (pensil, teal), **Hapus** (tempat sampah, merah).
- Gambar di tabel: kotak 40×40px, radius 6px.
- **Highlight dari notifikasi:** baris memakai `--color-accent-tint` + garis kiri amber.
- Di atas tabel: baris filter (search, dropdown, daterangepicker) lalu tombol **Tambah** di kanan.
- Di bawah tabel: info "Menampilkan 1-15 dari N data" dan pagination.
- Ponsel: tabel dalam wadah yang bisa digeser horizontal.
- Kosong: ikon + "Belum ada data" + tombol tambah bila relevan.

### 6.8 Pagination
Tombol angka 36×36px, radius 8px; halaman aktif latar teal teks putih; sebelumnya/berikutnya berupa ikon panah. Jumlah per halaman: peminjam riwayat 6, inventaris 15, kategori 10, ruangan 5, peminjaman 15.

### 6.9 Pop-up (modal)
- Overlay `rgba(19,78,74,0.5)`, kotak putih radius 12px, lebar maks. 480px (form pinjam 560px), `--shadow-popup`.
- Header: judul 20/600 + tombol tutup (X). Isi: form. Footer: **Batal** (Ghost) di kiri, aksi utama (Primary) di kanan.
- Ponsel: pop-up hampir layar penuh, tombol footer selebar penuh.
- Dipakai untuk: form pinjam, tambah/edit Kategori, Ruangan, akun, profil admin.
- **Halaman baru (bukan pop-up):** Tambah, Edit, dan Detail Inventaris.

### 6.10 Alert keputusan (konfirmasi, sukses, error)
Kotak tengah layar, lebar maks. 400px, ikon 48px di atas, judul, satu kalimat isi, dua tombol.

| Kasus | Ikon | Judul | Tombol |
|---|---|---|---|
| Ajukan pinjaman | alert-triangle amber | "Ajukan pinjaman?" | Batal / **Ajukan** |
| Simpan perubahan | alert-triangle amber | "Simpan perubahan?" | Batal / **Simpan** |
| Hapus data | trash merah | "Hapus data ini?" | Batal / **Hapus** (Danger) |
| Logout | log-out amber | "Keluar dari akun?" | Batal / **Keluar** |
| Berhasil | check-circle hijau | "Data tersimpan" | otomatis tertutup 2 detik |
| Login gagal | x-circle merah | "Username atau password salah" | Tutup |
| Hapus ditolak | info biru | "Unit sedang dipinjam" + alasan | Tutup |

Tombol yang aman (Batal) ada di kiri; tombol aksi di kanan. Untuk Hapus, fokus awal ada di **Batal**.

### 6.11 Notifikasi (lonceng admin)
Popover lebar 320px di bawah lonceng. Tiap item: nama peminjam (600), nama barang, waktu relatif ("2 menit lalu"), ikon detail (panah). Klik ikon detail menuju **Data Peminjaman** dengan baris ter-highlight. Kosong: "Tidak ada peminjaman baru".

### 6.12 Footer
Latar `--color-sidebar`, teks `--color-sidebar-text`, judul kolom putih. Isi otomatis dari Profil Sekolah: nama sekolah, alamat, email, telepon, ikon media sosial (Lucide/brand, 20px, hover putih). Media sosial kosong tidak ditampilkan. Ponsel: tumpuk satu kolom. Baris hak cipta 12px di bawah.

### 6.13 Diagram (dashboard)
Diagram status barang (donat atau batang) memakai warna solid status pada 2.3. Selalu sertakan legenda berlabel dan angka, tidak mengandalkan warna saja.

---

## 7. Penerapan per Halaman

### Login (kedua role)
Latar `--color-sidebar` penuh. Card putih 400px di tengah: logo sekolah (72px), nama sekolah, nama sistem, input username dan password, tombol **Masuk** selebar penuh. Ponsel: card selebar layar dengan margin 16px.

### Home peminjam
1. Navbar (6.1).
2. **Hero:** foto sekolah dengan lapisan gelap `rgba(19,78,74,0.55)`, nama sekolah (display, putih), sapaan akun.
3. **Card profil sekolah:** logo, NPSN, alamat, telepon, email.
4. **Card denah:** gambar denah + tombol **Unduh denah** (Primary).
5. Footer (6.12).

Data kosong: teks "Belum tersedia" dalam warna muted.

### Katalog dan pinjam (peminjam)
Grid card katalog (6.5), search di atas. Pop-up pinjam: gambar, kategori, ruangan, stok, counter, **nama peminjam***, tanggal & jam pinjam, tanggal kembali, Batal / **Ajukan pinjaman**.

### Riwayat peminjam
Filter (search, status, daterangepicker) di atas, card riwayat (6.5), pagination 6.

### Layout admin
Navbar + sidebar + konten (judul halaman 24/600 dengan breadcrumb kecil di atasnya) + footer.

### Dashboard admin
Baris tiga card statistik, diagram status barang, tabel barang terbaru.

### Profil Sekolah (admin)
Form read-only (input berlatar `#F3F7F6`, tanpa garis fokus) dengan tombol **Edit** di kanan atas. Mode edit: input terbuka berlatar putih, muncul **Batal** dan **Simpan**. Pratinjau gambar logo, foto, denah.

### Inventaris (admin)
Tabel (6.7). Halaman Tambah/Edit dibagi dua card: **Informasi utama** dan **Detail aset**; tombol Batal/Simpan menempel di bawah (sticky) pada layar kecil. Detail: gambar besar di kiri, data dalam daftar dua kolom di kanan.

### Laporan (admin)
1. **Halaman daftar:** filter tanggal, tabel ringkasan, tombol **Download** dengan pilihan format.
2. **Halaman pratinjau:** area abu `#E5E7EB`, kertas putih landscape di tengah dengan bayangan, bilah atas berisi **Kembali** dan **Unduh**.
3. **Isi kertas: hitam-putih murni**, tanpa warna tema, sesuai format KIB B: kop (logo kiri, judul tengah), blok identitas, tabel 16 kolom dengan header dua tingkat dan baris nomor 1-16, font serif/sans standar 10-11px, garis tabel 1px hitam, sel kosong "-", harga format `480,00`, header tabel diulang tiap halaman.

---

## 8. Gerak

- Durasi: 150ms (hover, fokus), 200ms (pop-up, dropdown).
- Easing: `ease-out`. Hormati `prefers-reduced-motion` (matikan animasi).
- Pop-up muncul dengan fade + naik 8px; alert sukses tertutup otomatis 2 detik.
- Tidak ada animasi dekoratif.

---

## 9. Aksesibilitas

- Semua input punya label; ikon tombol punya `aria-label` (mis. "Hapus barang").
- Fokus terlihat jelas (`--focus-ring`); pop-up mengunci fokus dan bisa ditutup dengan Esc.
- Status selalu berupa teks + warna; diagram punya legenda dan angka.
- Gambar (logo, denah, barang) punya teks alternatif.
- Kontras minimal AA (lihat 2.5).

---

## 10. Bahasa Antarmuka

- Bahasa Indonesia, kalimat pendek, kata kerja di awal tombol: "Simpan", "Ajukan pinjaman", "Unduh denah".
- Pesan error menjelaskan masalah dan solusi: "Kode barang sudah dipakai. Gunakan kode lain."
- Tidak memakai tanda seru berlebihan; alert sukses cukup singkat: "Data tersimpan".
- Sebut "Akun Guru" untuk akun bersama; nama guru hanya muncul dari isian form pinjam.

---

## 11. Berkas Token (CSS)

```css
:root {
  --color-primary: #0F766E;
  --color-primary-hover: #115E59;
  --color-primary-tint: #CCFBF1;
  --color-primary-soft: #F0FDFA;
  --color-sidebar: #134E4A;
  --color-sidebar-text: #CCFBF1;
  --color-accent: #F59E0B;
  --color-accent-tint: #FEF3C7;

  --color-bg: #F3F7F6;
  --color-surface: #FFFFFF;
  --color-border: #D9E4E2;
  --color-border-strong: #B7C9C6;
  --color-text: #1F2937;
  --color-text-secondary: #4B5563;
  --color-text-muted: #6B7280;
  --color-text-on-primary: #FFFFFF;
  --color-text-on-accent: #1F2937;

  --status-success-bg: #DCFCE7; --status-success-text: #166534; --status-success: #16A34A;
  --status-warning-bg: #FEF9C3; --status-warning-text: #854D0E; --status-warning: #EAB308;
  --status-info-bg:    #DBEAFE; --status-info-text:    #1E40AF; --status-info:    #2563EB;
  --status-danger-bg:  #FEE2E2; --status-danger-text:  #991B1B; --status-danger:  #DC2626;

  --font-sans: "Inter", system-ui, -apple-system, "Segoe UI", sans-serif;

  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-full: 999px;

  --shadow-card: 0 1px 2px rgba(19, 78, 74, 0.06);
  --shadow-popup: 0 12px 32px rgba(19, 78, 74, 0.18);
  --focus-ring: 0 0 0 3px rgba(15, 118, 110, 0.35);

  --sidebar-width: 256px;
  --navbar-height: 64px;
}
```

---

## 12. Checklist Konsistensi

- [ ] Hanya satu tombol Primary per area.
- [ ] Hapus selalu lewat alert Danger dengan fokus awal di Batal.
- [ ] Amber hanya untuk notifikasi dan highlight baris.
- [ ] Status selalu berlabel teks.
- [ ] Kode barang tidak tampil di card katalog.
- [ ] Kertas laporan hitam-putih dan tata letak sama antara pratinjau dan file unduhan.
- [ ] Semua warna dan ukuran memakai token pada bagian 11.
