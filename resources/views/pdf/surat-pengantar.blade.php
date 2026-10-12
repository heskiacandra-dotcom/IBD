<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Surat Pengantar RT - {{ $letter['nomor_surat'] }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 1.8cm 2cm 2cm 2cm;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 12pt;
      line-height: 1.45;
      color: #111827;
      margin: 0;
      padding: 0;
    }
    .kop-surat {
      text-align: center;
      border-bottom: 3px double #000;
      padding-bottom: 8px;
      margin-bottom: 20px;
      position: relative;
    }
    .kop-title-sub {
      font-size: 11pt;
      font-weight: normal;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
    }
    .kop-title-main {
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 2px 0;
    }
    .kop-address {
      font-size: 9.5pt;
      font-style: italic;
      color: #374151;
      margin-top: 2px;
    }
    .judul-surat {
      text-align: center;
      margin-bottom: 22px;
    }
    .judul-text {
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      text-decoration: underline;
      letter-spacing: 0.8px;
      margin: 0;
    }
    .nomor-surat {
      font-size: 11pt;
      margin-top: 3px;
    }
    .paragraf-pembuka {
      text-align: justify;
      text-indent: 30px;
      margin-bottom: 12px;
    }
    .tabel-biodata {
      width: 100%;
      margin: 0 0 16px 20px;
      border-collapse: collapse;
      font-size: 11.5pt;
    }
    .tabel-biodata td {
      padding: 3px 0;
      vertical-align: top;
    }
    .col-label {
      width: 180px;
    }
    .col-sep {
      width: 15px;
      text-align: center;
    }
    .col-val {
      font-weight: 500;
    }
    .tabel-penutup {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }
    .qr-box {
      border: 1px solid #9ca3af;
      padding: 8px;
      background: #f9fafb;
      display: inline-block;
      text-align: center;
      font-size: 8pt;
      color: #4b5563;
      border-radius: 4px;
    }
    .qr-box svg {
      width: 80px;
      height: 80px;
      display: block;
      margin: 0 auto 4px auto;
    }
    .ttd-box {
      text-align: center;
      width: 260px;
      font-size: 11pt;
    }
    .ttd-space {
      height: 65px;
      position: relative;
    }
    .stempel-mockup {
      position: absolute;
      top: 5px;
      left: 30px;
      color: #2563eb;
      font-weight: bold;
      font-size: 9pt;
      border: 2px dashed #3b82f6;
      border-radius: 50%;
      width: 75px;
      height: 75px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.75;
      transform: rotate(-12deg);
    }
    .ttd-nama {
      font-weight: bold;
      text-decoration: underline;
    }
    .footer-audit {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      border-top: 1px solid #e5e7eb;
      padding-top: 6px;
      font-size: 8pt;
      color: #6b7280;
      display: flex;
      justify-content: space-between;
    }
  </style>
</head>
<body>

  <!-- KOP SURAT RESMI RT 05 -->
  <div class="kop-surat">
    <div class="kop-title-sub">RUKUN TETANGGA 05 / RUKUN WARGA 01</div>
    <div class="kop-title-sub">KELURAHAN SUKAMAJU - KECAMATAN CILODONG</div>
    <div class="kop-title-main">HARMONI RESIDENCE - KOTA DEPOK</div>
    <div class="kop-address">Sekretariat: Balai Pertemuan Warga Blok A, Jl. Harmoni Residence No. 01, Telp/WA: 0812-9876-5432</div>
  </div>

  <!-- JUDUL SURAT -->
  <div class="judul-surat">
    <div class="judul-text">SURAT PENGANTAR / KETERANGAN RT</div>
    <div class="nomor-surat">Nomor: <strong>{{ $letter['nomor_surat'] }}</strong></div>
  </div>

  <!-- ISI SURAT -->
  <p class="paragraf-pembuka">
    Yang bertanda tangan di bawah ini Ketua Rukun Tetangga (RT) 05 / Rukun Warga (RW) 01 Kelurahan Sukamaju, Kecamatan Cilodong, Kota Depok, dengan ini menerangkan dengan sesungguhnya bahwa:
  </p>

  <table class="tabel-biodata">
    <tr>
      <td class="col-label">Nama Lengkap</td>
      <td class="col-sep">:</td>
      <td class="col-val"><strong>{{ $letter['nama'] }}</strong></td>
    </tr>
    <tr>
      <td class="col-label">Nomor Induk Kependudukan (NIK)</td>
      <td class="col-sep">:</td>
      <td class="col-val"><code>{{ $letter['nik'] }}</code></td>
    </tr>
    <tr>
      <td class="col-label">Nomor Kartu Keluarga (KK)</td>
      <td class="col-sep">:</td>
      <td class="col-val">{{ $letter['no_kk'] }}</td>
    </tr>
    <tr>
      <td class="col-label">Tempat, Tanggal Lahir</td>
      <td class="col-sep">:</td>
      <td class="col-val">{{ $letter['tempat_tgl_lahir'] }}</td>
    </tr>
    <tr>
      <td class="col-label">Jenis Kelamin / Agama</td>
      <td class="col-sep">:</td>
      <td class="col-val">{{ $letter['jenis_kelamin'] }} / {{ $letter['agama'] }}</td>
    </tr>
    <tr>
      <td class="col-label">Pekerjaan</td>
      <td class="col-sep">:</td>
      <td class="col-val">{{ $letter['pekerjaan'] }}</td>
    </tr>
    <tr>
      <td class="col-label">Alamat / Tempat Tinggal</td>
      <td class="col-sep">:</td>
      <td class="col-val">{{ $letter['alamat'] }}</td>
    </tr>
  </table>

  <p class="paragraf-pembuka">
    Orang tersebut di atas adalah benar-benar warga penduduk yang bertempat tinggal di wilayah RT 05 / RW 01 Harmoni Residence Kelurahan Sukamaju, dan sepanjang pengetahuan kami berkelakuan baik serta belum pernah terlibat dalam perkara kepolisian maupun tindak pidana.
  </p>

  <p class="paragraf-pembuka">
    Surat pengantar ini kami berikan dan terbitkan untuk keperluan:
    <br>
    <strong>"{{ $letter['keperluan'] }}"</strong>
    <br>
    Ditujukan kepada: <em>{{ $letter['tujuan_instansi'] }}</em>.
  </p>

  <p class="paragraf-pembuka">
    Demikian Surat Pengantar ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
  </p>

  <!-- BAGIAN TANDA TANGAN & QR CODE -->
  <table class="tabel-penutup">
    <tr>
      <td style="vertical-align: bottom; width: 45%;">
        <!-- QR CODE VALIDATION DIGITAL -->
        <div class="qr-box">
          {!! $qrSvg !!}
          <div style="font-weight: bold; margin-top: 2px;">VERIFIKASI RESMI DIGITAL</div>
          <div>Scan untuk validasi keaslian dokumen</div>
          <div style="font-size: 7pt; color: #9ca3af; margin-top: 1px;">ID: {{ $letter['code'] }}</div>
        </div>
      </td>
      <td style="vertical-align: top; width: 55%; text-align: right;">
        <div class="ttd-box" style="display: inline-block;">
          <div>Kota Depok, {{ $letter['tanggal_terbit'] }}</div>
          <div style="font-weight: bold; margin-top: 2px;">Ketua RT 05 / RW 01</div>
          
          <div class="ttd-space">
            <!-- Digital Signature Placeholder / Stamp -->
            <div style="padding-top: 15px; font-family: 'Brush Script MT', cursive, sans-serif; font-size: 18pt; color: #1e3a8a;">
              Sukardi
            </div>
          </div>

          <div class="ttd-nama">{{ $letter['ketua_rt'] }}</div>
          <div style="font-size: 9.5pt; color: #4b5563;">Ketua Rukun Tetangga</div>
        </div>
      </td>
    </tr>
  </table>

  <!-- FOOTER AUDIT LOG -->
  <div class="footer-audit">
    <span>Dokumen resmi diterbitkan melalui Platform <strong>Smart RT CMS</strong></span>
    <span>Tanggal Cetak: {{ $generatedAt }}</span>
  </div>

</body>
</html>
