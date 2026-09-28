# Flow Sistem Inventaris & Peminjaman Sekolah
**SMPN 3 Babakancikao**

Dokumen ini merangkum seluruh hasil brainstorming. Tanda status:
- ✅ **Diputuskan**: sudah disepakati.
- 💡 **Usulan**: diajukan, belum ditolak, perlu konfirmasi akhir.

---

## 0. Keputusan Kunci (Ringkasan)

| # | Keputusan | Status |
|---|---|---|
| 1 | Home & footer peminjam tidak punya data sendiri; membaca data Profil Sekolah yang diatur admin | ✅ |
| 2 | Denah sekolah hanya boleh **gambar** (JPG/PNG/WebP), tampil sebagai pratinjau dan bisa diunduh | ✅ |
| 3 | **Satu akun peminjam bersama** dipakai semua guru; nama peminjam diisi manual di form pinjam | ✅ |
| 4 | **Footer otomatis mengikuti Profil Sekolah** (tidak ada pengaturan footer terpisah) | ✅ |
| 5 | CRUD Data Inventaris berupa **halaman baru** (Tambah, Edit, Detail); Hapus hanya alert keputusan | ✅ |
| 6 | **Kategori = jenis barang** (Laptop, Kursi, Proyektor); **Inventaris = unit** dari kategori tersebut | ✅ |
| 7 | **Sistem memilih otomatis** unit tersedia saat peminjaman | ✅ |
| 8 | Laporan mengikuti format **KIB B**; tombol Download membuka **halaman pratinjau** dulu | ✅ |
| 9 | **Kode Barang diinput manual admin**, **berbeda untuk setiap unit** (wajib unik). Contoh kode di foto hanya ilustrasi, bukan patokan format | ✅ |
| 10 | Media sosial ditambahkan sebagai field di Profil Sekolah (sumber data footer) | 💡 |
| 11 | Status peminjaman: Menunggu → Dipinjam → Selesai (+ Ditolak, + badge Terlambat) | 💡 |
| 12 | Palet warna: rekomendasi Opsi A (Biru Institusi) | 💡 |

---

## 1. Konsep Data

```
Kategori (jenis barang)  1 ─── N  Inventaris (unit fisik)
Ruangan                  1 ─── N  Inventaris
Inventaris (unit)        N ─── N  Peminjaman   (lewat detail unit yang terambil)
Profil Sekolah  ──►  Home peminjam, Footer (semua halaman), Navbar, Login, Kop Laporan
```

- **Kategori**: nama jenis barang, contoh *Laptop*. Jumlah barang dihitung otomatis dari jumlah unit.
- **Inventaris**: satu baris = satu unit fisik dengan kode barang sendiri.
- **Stok tersedia** (di peminjam) = jumlah unit berstatus *tersedia* dalam satu kategori.
- **Akun peminjam** hanya satu; identitas orang ada di **nama yang diketik pada form pinjam**, disimpan di setiap transaksi.

---

## 2. Role Peminjam

### 2.1 Login
1. Halaman menampilkan logo sekolah, nama sekolah, nama sistem (dari Profil Sekolah).
2. Isi username dan password, tekan **Masuk**.
3. Salah username/password: muncul alert error.
4. Benar: masuk ke **Home**.

### 2.2 Navbar & Footer (semua halaman peminjam)
- **Navbar**: logo sekolah di kiri; ikon burger dropdown berisi nama akun (mis. "Akun Guru") dan **Logout**.
- **Footer**: nama sekolah, alamat, email, no. telepon, ikon media sosial. Semua data **otomatis dari Profil Sekolah**. Ikon media sosial yang kosong tidak ditampilkan.

### 2.3 Home (read-only, cerminan Profil Sekolah)
1. **Hero**: gambar/foto sekolah dan nama sekolah.
2. **Card profil sekolah**: logo, nama, NPSN, alamat, no. telepon, email.
3. **Card denah**: pratinjau gambar denah + tombol **Unduh Denah**.
4. Data kosong: bagian disembunyikan atau diberi teks "Belum tersedia".
5. Perubahan oleh admin langsung tampil setelah admin menekan Simpan.

### 2.4 Data Inventaris & Peminjaman (katalog)
1. Menampilkan **card per kategori** (bukan per unit): gambar, nama kategori, ruangan, stok tersedia.
2. Kategori tanpa stok tersedia ditandai "Habis" dan tombol pinjam nonaktif.
3. Unit berstatus rusak tidak dihitung dalam stok. 💡
4. Tiap card punya tombol **Detail** dan **Pinjam**.
5. Klik salah satunya membuka **pop-up**: gambar, nama kategori, ruangan, stok tersedia, **counter jumlah**, **nama peminjam (wajib, min. 3 karakter)**, tanggal & jam peminjaman, tanggal pengembalian, tombol **Batal** dan **Ajukan Pinjaman**.
6. Validasi: jumlah tidak boleh melebihi stok; tanggal kembali tidak boleh sebelum tanggal pinjam.
7. Tekan **Ajukan** → alert keputusan (Ya/Tidak) → alert sukses → otomatis pindah ke **Riwayat Peminjaman** dengan data yang baru diajukan di-highlight.
8. **Pemilihan unit otomatis**: sistem mengambil unit berstatus tersedia sesuai jumlah yang diminta, urut kode terkecil, lalu mencatatnya di transaksi.

### 2.5 Riwayat Peminjaman
- Tampil dalam bentuk **card**: nama peminjam, nama kategori, jumlah unit, status, tanggal pengembalian.
- **Search bar** (termasuk pencarian nama peminjam, penting karena akun bersama), **filter status**, **daterangepicker**.
- **Pagination 6**.
- Status: Menunggu, Dipinjam, Selesai (+ Ditolak, Terlambat 💡).

### 2.6 Logout
Dropdown profil navbar → klik Logout → alert konfirmasi (Yakin / Tidak).

---

## 3. Role Admin

### 3.1 Login
Sama dengan peminjam: username, password, tombol Masuk, alert jika salah, tampilan logo + nama sekolah + nama sistem.

### 3.2 Layout
- **Sidebar**: Dashboard → Profil Sekolah → Kategori → Ruangan → Data Inventaris → Data Peminjaman → Daftar Akun → Laporan.
- **Navbar**: logo sekolah (kiri) + SMPN 3 BABAKANCIKAO, **ikon notifikasi**, **ikon burger** (Profil, Logout).
- **Footer**: otomatis dari Profil Sekolah (admin tidak mengedit footer secara terpisah).

### 3.3 Profil Admin
1. Ikon burger navbar → dropdown → **Profil**.
2. Halaman profil: gambar, username, password (disamarkan), tombol **Edit**.
3. Edit membuka **pop-up**: ubah gambar, username, password, tombol **Simpan**.
4. Alert keputusan → alert berhasil.

### 3.4 Profil Sekolah (sumber data Home dan Footer)
**Bidang**
- Nama sekolah, NPSN (8 digit angka), alamat lengkap, no. telepon, email
- Logo sekolah, foto/gambar sekolah, **denah sekolah (gambar saja)**
- **Media sosial** (Instagram, YouTube, Facebook, TikTok; boleh kosong) 💡
- **Identitas laporan KIB** 💡: Provinsi, Kab./Kota (default Jawa Barat / Purwakarta, dapat diedit), Bidang, Unit Organisasi, Sub Unit Organisasi, UPB, No. Kode Lokasi

**Alur**
1. Halaman terbuka **read-only** (field terkunci, gambar tampil sebagai pratinjau).
2. Klik **Edit** → field terbuka, muncul tombol **Batal** dan **Simpan**.
3. Simpan → alert keputusan → alert sukses → kembali ke mode baca.
4. Batal → semua isian kembali ke nilai tersimpan terakhir.

**Aturan upload gambar (usulan)**: format JPG/PNG/WebP; maks ±2 MB untuk logo dan foto, ±5 MB untuk denah; pratinjau muncul langsung setelah memilih file; format lain ditolak dengan pesan jelas.

**Validasi**: nama sekolah dan alamat wajib; NPSN angka 8 digit; email berformat valid; telepon hanya angka, `+`, `-`.

### 3.5 Dashboard
- **Card statistik**: total barang (= total unit), total kategori, total peminjaman.
- **Diagram** status barang.
- **Tabel barang terbaru**.

### 3.6 Kategori
- Tabel: kode kategori, nama kategori, jumlah barang (otomatis dari unit), keterangan, aksi (edit, hapus).
- **Tambah**: pop-up isi nama kategori (dan keterangan).
- **Edit**: pop-up data lama, tombol Simpan dan Batal, alert persetujuan, alert sukses.
- **Hapus**: alert keputusan. Kategori yang masih punya unit tidak boleh dihapus. 💡
- Search bar, **pagination 10**.

### 3.7 Ruangan
- Tabel: kode, nama ruangan, penanggung jawab, jumlah barang di ruangan, keterangan, aksi (edit, hapus).
- **Tambah**: pop-up nama ruangan, penanggung jawab, keterangan.
- **Edit**: pop-up data lama, Batal dan Simpan, alert keputusan, alert sukses.
- **Hapus**: alert keputusan + alert sukses. Ruangan yang masih berisi unit tidak boleh dihapus. 💡
- Search bar, **pagination 5**.

> **Urutan wajib**: Kategori dan Ruangan harus ada sebelum unit inventaris bisa dibuat.

### 3.8 Data Inventaris (unit barang)
**Tabel**: kode barang, gambar, nama, kategori, ruangan, status, aksi (detail, edit, hapus).
Di atas tabel: filter kategori, filter ruangan, search (kode, nama, nomor register), daterangepicker. **Pagination 15**.

**Halaman baru (bukan pop-up)**
1. **Tambah Barang**, form dua bagian:
   - *Informasi utama*: gambar, **kode barang (manual)**, nama, kategori (dropdown), ruangan (dropdown), status.
   - *Detail aset*: nomor register, merk/type, ukuran/CC, bahan, tahun pembelian, nomor (pabrik, rangka, mesin, polisi, BPKB), asal usul, harga (ribuan Rp), keterangan.
2. **Edit Barang**: form sama, terisi data lama, alert persetujuan sebelum simpan.
3. **Detail Barang**: tampilan baca dengan gambar besar dan semua data; tombol Edit dan Kembali.
4. **Hapus**: hanya **alert keputusan** di tabel. Unit yang sedang dipinjam ditolak penghapusannya. 💡

**Aturan kode barang** ✅
- Diinput manual oleh admin, **wajib diisi dan harus unik per unit**.
- Format bebas (contoh di foto laporan bukan patokan baku); disarankan konsisten antar unit.
- Bila kode sudah dipakai, form menolak dengan pesan jelas.
- Kode ditampilkan di tabel admin, detail, data peminjaman, dan laporan; di katalog peminjam hanya muncul di pop-up detail, tidak di card.

**Field opsional**: nomor rangka, mesin, polisi, BPKB, ukuran/CC, bahan, dan lainnya boleh kosong. Di laporan otomatis tampil "-".

**Status barang** (mis. Baik, Rusak, Dipinjam): barang rusak tidak muncul di katalog peminjam. 💡

### 3.9 Data Peminjaman
- Tabel: kode peminjaman, nama peminjam (yang diketik di form), nama kategori, unit yang terambil (kode + nama), tanggal & jam peminjaman, tanggal pengembalian, aksi (edit).
- **Edit**: admin mengubah status (Selesai). Saat Selesai, unit kembali berstatus tersedia dan stok bertambah otomatis.
- Search bar, daterangepicker, filter status, **pagination 15**.
- Baris dari notifikasi di-highlight (hover).

**Alur status** 💡
`Dipinjam → Selesai`, ; peminjaman melewati tanggal kembali diberi badge merah **Terlambat**. Unit ditandai dipinjam saat peminjaman disetujui.

### 3.10 Notifikasi Navbar
1. Peminjam mengajukan → ikon lonceng menampilkan penanda.
2. Pop-up berisi: nama peminjam, nama barang, ikon detail.
3. Klik ikon detail → menuju halaman **Data Peminjaman**, baris terkait di-highlight.

### 3.11 Daftar Akun
- Tabel menampilkan daftar akun peminjam: username dan password (disamarkan ••••).
- Aksi  **edit**: pop-up ubah username dan password → alert keputusan → alert sukses.
- Aksi **tambah**: pop-up tambah username dan password → alert keputusan → alert sukses.

### 3.12 Laporan (Format KIB B)
**Flow**
1. Admin membuka menu **Laporan**, memilih rentang tanggal (daterangepicker); tabel ringkasan tampil.
2. Klik **Download**, pilih format: PDF, Excel, Word, atau Gambar.
3. File **tidak langsung terunduh**. Sistem membuka **halaman pratinjau** yang tampil seperti dokumen cetak (logo dan tata letak tetap).
4. Di pratinjau ada tombol **Unduh** dan **Kembali**.
5. Klik Unduh → file terunduh dengan tata letak sama seperti pratinjau.

**Kop laporan**
- Logo (kiri) + judul tengah:
  - PEMERINTAH KABUPATEN PURWAKARTA
  - REKAPITULASI KARTU INVENTARIS BARANG (KIB) B
  - PERALATAN DAN MESIN
- Blok identitas: Provinsi, Kab./Kota, Bidang, Unit Organisasi, Sub Unit Organisasi, UPB, No. Kode Lokasi (dari Profil Sekolah).

**Tabel (16 kolom, header dua tingkat, sesuai foto, tidak boleh berbeda)**

| Kolom | Isi |
|---|---|
| 1 | No |
| 2 | Kode Barang 1.3. |
| 3 | Jenis Barang / Nama Barang |
| 4 | Nomor Register |
| 5 | Merk/Type |
| 6 | Ukuran/CC |
| 7 | Bahan |
| 8 | Tahun Pembelian |
| 9-13 | **Nomor** (header gabungan): Pabrik, Rangka, Mesin, Polisi, BPKB |
| 14 | Asal Usul |
| 15 | Harga (ribuan Rp) |
| 16 | Keterangan |

- Di bawah header ada **baris nomor kolom 1-16**.
- Field kosong ditulis **"-"**; harga berformat Indonesia (mis. `480,00`).
- Kolom Asal Usul dan Keterangan bisa berisi teks panjang, sel harus **wrap**.
- Orientasi **landscape**; header tabel **diulang** di tiap halaman.
- PDF dan Gambar: tata letak identik dengan pratinjau. Excel dan Word: header gabungan dan logo tetap dibuat, dengan variasi kecil.

### 3.13 Logout
Dropdown profil navbar → Logout → alert konfirmasi (Yakin / Tidak).

---

## 4. Alur Antar-Role (End-to-End)

```
ADMIN                                         PEMINJAM (akun bersama)
─────                                         ───────────────────────
1. Isi Profil Sekolah (+ media sosial,
   denah, identitas KIB)  ───────────────►  Home & Footer terisi otomatis
2. Buat Kategori → Ruangan
3. Input unit Inventaris (kode manual
   unik, detail aset)     ───────────────►  Katalog per kategori + stok
4. Atur username/password akun bersama ──►  Login
                                            5. Pilih kategori, isi jumlah,
                                               nama peminjam, tanggal → Ajukan
6. Notifikasi lonceng  ◄────────────────── Sistem pilih unit otomatis
7. Buka Data Peminjaman, proses
8. Tandai Selesai → stok kembali ────────►  Riwayat status = Selesai
9. Laporan KIB B → pratinjau → unduh
```

---

## 5. Ringkasan Pagination

| Halaman | Per halaman |
|---|---|
| Riwayat peminjam | 6 |
| Data inventaris | 15 |
| Kategori | 10 |
| Ruangan | 5 |
| Data peminjaman | 15 |

---


