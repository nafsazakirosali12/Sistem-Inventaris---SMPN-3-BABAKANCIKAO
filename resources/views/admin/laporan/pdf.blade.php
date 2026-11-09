<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris Barang KIB B</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 2.54cm 2.54cm 2.54cm 2.54cm; /* Margin 2.54 cm pada seluruh sisi */
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* Header instansi */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .title-h1 {
            font-size: 8.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-h2 {
            font-size: 10pt;
            font-weight: bold;
            margin: 1px 0 0 0;
            text-transform: uppercase;
        }

        .title-h3 {
            font-size: 8.5pt;
            font-weight: bold;
            margin: 1px 0 0 0;
            text-transform: uppercase;
        }

        /* Identity Details (Provinsi, Kab/Kota, dll) */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            margin-bottom: 5px;
            font-size: 6.5pt;
        }

        .identity-table td {
            padding: 0.8px 0;
            vertical-align: top;
        }

        .label-id {
            width: 130px;
            font-weight: bold;
        }

        .sep-id {
            width: 10px;
            font-weight: bold;
        }

        /* Main KIB Table */
        .kib-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 6pt;
        }

        .kib-table th, .kib-table td {
            border: 1px solid #000;
            padding: 2px 2px;
            vertical-align: middle;
            word-break: break-word;
        }

        /* Mencegah pengulangan judul kolom saat berpindah halaman pada format PDF */
        .kib-table thead {
            display: table-row-group !important;
        }

        .kib-table tr {
            page-break-inside: avoid;
        }

        .kib-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .kib-table tr.col-num td {
            background-color: #f9f9f9;
            font-weight: bold;
            text-align: center;
            font-size: 5.5pt;
            padding: 1px 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .total-row td {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 6.5pt;
            border-top: 2px solid #000;
        }

        /* Dual Signatures Block */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
            font-size: 7pt;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }

        .sign-space {
            height: 40px;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Header / Identitas Instansi (sesuai kop.jpeg: Logo di kiri, judul di tengah, lalu identitas) -->
    <table class="header-table">
        <tr>
            <td style="width: 60px; text-align: left;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" style="max-height: 50px; max-width: 55px;" alt="Logo">
                @endif
            </td>
            <td style="text-align: center; padding-right: 60px;">
                <div class="title-h1">PEMERINTAH {{ strtoupper($schoolProfile->kabupaten_kota ?: 'KABUPATEN PURWAKARTA') }}</div>
                <div class="title-h2">REKAPITULASI INVENTARIS BARANG (KIB) B</div>
                <div class="title-h3">PERALATAN DAN MESIN</div>
            </td>
        </tr>
    </table>

    <!-- Profil Identitas Organisasi (Majang ke Bawah, Satu Kolom Rapi) -->
    <table class="identity-table">
        <tr>
            <td class="label-id">PROVINSI</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->provinsi ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">KAB./KOTA</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->kabupaten_kota ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">BIDANG</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->bidang ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">UNIT ORGANISASI</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->unit_organisasi ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">SUB UNIT ORGANISASI</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->sub_unit_organisasi ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">UPB</td>
            <td class="sep-id">:</td>
            <td>{{ strtoupper($schoolProfile->upb ?: '-') }}</td>
        </tr>
        <tr>
            <td class="label-id">NO. KODE LOKASI</td>
            <td class="sep-id">:</td>
            <td><b>{{ $schoolProfile->no_kode_lokasi ?: '-' }}</b></td>
        </tr>
    </table>

    <!-- Tabel Data Inventaris (16 Kolom persis kop.jpeg) -->
    <table class="kib-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 3%;">No.</th>
                <th rowspan="2" style="width: 8%;">Kode Barang</th>
                <th rowspan="2" style="width: 18%;">Jenis Barang / Nama Barang</th>
                <th rowspan="2" style="width: 4.5%;">Nomor Registrasi</th>
                <th rowspan="2" style="width: 8%;">Merk / Type</th>
                <th rowspan="2" style="width: 4.5%;">Ukuran / CC</th>
                <th rowspan="2" style="width: 4.5%;">Bahan</th>
                <th rowspan="2" style="width: 4.5%;">Tahun Pembelian</th>
                <th colspan="5" style="padding: 1px; width: 20%;">Nomor</th>
                <th rowspan="2" style="width: 7%;">Asal Usul Cara Perolehan</th>
                <th rowspan="2" style="width: 9%;">Harga (Ribuan Rp)</th>
                <th rowspan="2" style="width: 9%;">Keterangan</th>
            </tr>
            <tr>
                <th style="width: 4%;">Pabrik</th>
                <th style="width: 4%;">Rangka</th>
                <th style="width: 4%;">Mesin</th>
                <th style="width: 4%;">Polisi</th>
                <th style="width: 4%;">BPKB</th>
            </tr>
            <tr class="col-num">
                @for($col = 1; $col <= 16; $col++)
                    <td>{{ $col }}</td>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inv)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $inv->kode_barang ?: '-' }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $inv->nama_barang ?: '-' }}</div>
                        @if($inv->category)
                            <div style="font-size: 5pt; color: #444;">Kelompok: {{ $inv->category->nama_kategori }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $inv->nomor_register ?: '-' }}</td>
                    <td>{{ $inv->merk_type ?: '-' }}</td>
                    <td class="text-center">{{ $inv->ukuran_cc ?: '-' }}</td>
                    <td class="text-center">{{ $inv->bahan ?: '-' }}</td>
                    <td class="text-center">{{ $inv->tahun_pembelian ?: '-' }}</td>

                    <td class="text-center">{{ $inv->nomor_pabrik ?: '-' }}</td>
                    <td class="text-center">{{ $inv->nomor_rangka ?: '-' }}</td>
                    <td class="text-center">{{ $inv->nomor_mesin ?: '-' }}</td>
                    <td class="text-center">{{ $inv->nomor_polisi ?: '-' }}</td>
                    <td class="text-center">{{ $inv->nomor_bpkb ?: '-' }}</td>

                    <td>{{ $inv->asal_usul ?: '-' }}</td>
                    <td class="text-right">Rp {{ number_format($inv->harga ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $inv->keterangan ?? $inv->deskripsi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding: 10px;">Tidak ada data inventaris.</td>
                </tr>
            @endforelse

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="14" class="text-right font-bold" style="padding-right: 6px;">
                    JUMLAH HARGA KESELURUHAN :
                </td>
                <td class="text-right font-bold">
                    Rp {{ number_format($totalHarga, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Bagian Pengesahan / Mengetahui (sesuai bawah.jpeg) -->
    <table class="signature-table">
        <tr>
            <!-- Kolom Kiri: Mengetahui / Kepala Sekolah -->
            <td>
                <div>Mengetahui,</div>
                <div style="font-weight: bold;">Kepala Sekolah</div>
                <div class="sign-space"></div>
                <div class="sign-name">
                    ( {{ $schoolProfile->nama_kepala_sekolah ?: 'Drs. H. Ahmad Dahlan, M.Pd' }} )
                </div>
                <div>NIP. {{ $schoolProfile->nip_kepala_sekolah ?: '19720315 199802 1 002' }}</div>
            </td>

            <!-- Kolom Kanan: Pembuat Laporan -->
            <td>
                @php
                    $lokasi = $schoolProfile->kabupaten_kota
                        ? str_replace(['Kabupaten ', 'Kota '], '', $schoolProfile->kabupaten_kota)
                        : 'Babakancikao';
                @endphp
                <div>{{ $lokasi }}, {{ $generatedDate }}</div>
                <div style="font-weight: bold;">Pembuat Laporan</div>
                <div class="sign-space"></div>
                <div class="sign-name">
                    ( {{ $schoolProfile->nama_pembuat ?: 'Admin Sarpras' }} )
                </div>
                <div>NIP. {{ $schoolProfile->nip_pembuat ?: '19800101 200501 1 001' }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
