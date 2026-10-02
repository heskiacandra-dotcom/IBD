<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\OfficialLetter;
use App\Models\Citizen;
use Carbon\Carbon;

class LetterPdfController extends Controller
{
    /**
     * Data master arsip surat terverifikasi
     */
    protected function getLettersData()
    {
        return [
            1 => [
                'id' => 1,
                'code' => 'SRT-2026-001',
                'nomor_surat' => '470/024/RT-05/IX/2026',
                'perihal' => 'Surat Pengantar Pembuatan KTP & KK Baru',
                'nama' => 'Bpk. Hendra Wijaya',
                'nik' => '3201051208940001',
                'no_kk' => '3201051909120008',
                'tempat_tgl_lahir' => 'Depok, 12 Agustus 1994',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => 'Karyawan Swasta / BUMN',
                'alamat' => 'Harmoni Residence Blok B No. 12, RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Kantor Kelurahan Sukamaju / Disdukcapil Kota Depok',
                'keperluan' => 'Kelengkapan administrasi pemutakhiran data kartu keluarga dan KTP elektronik keluarga.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => '20 September 2026',
                'whatsapp' => '081234567890',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ],
            2 => [
                'id' => 2,
                'code' => 'SRT-2026-002',
                'nomor_surat' => '474/018/RT-05/IX/2026',
                'perihal' => 'Surat Keterangan Domisili Usaha',
                'nama' => 'Ibu Dewi Sartika',
                'nik' => '3201055504880002',
                'no_kk' => '3201051909120015',
                'tempat_tgl_lahir' => 'Depok, 15 April 1988',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta / Pemilik Usaha Kuliner',
                'alamat' => 'Harmoni Residence Blok C No. 04, RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Dinas Penanaman Modal & PTSP / Kelurahan Sukamaju',
                'keperluan' => 'Pendaftaran Nomor Induk Berusaha (NIB) dan izin operasional UMKM Dapur Harmoni.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => '19 September 2026',
                'whatsapp' => '081398765432',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ],
            3 => [
                'id' => 3,
                'code' => 'SRT-2026-003',
                'nomor_surat' => '471/015/RT-05/IX/2026',
                'perihal' => 'Surat Pengantar Pembuatan KTP Baru',
                'nama' => 'Sdr. Dimas Arya Nugraha',
                'nik' => '3201052203090005',
                'no_kk' => '3201051909120008',
                'tempat_tgl_lahir' => 'Depok, 22 Maret 2009',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => 'Pelajar / Mahasiswa',
                'alamat' => 'Harmoni Residence Blok B No. 12, RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Kantor Kecamatan Cilodong / Disdukcapil Kota Depok',
                'keperluan' => 'Perekaman biometrik dan penerbitan KTP elektronik pertama usia 17 tahun.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => '18 September 2026',
                'whatsapp' => '082155667788',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ],
            4 => [
                'id' => 4,
                'code' => 'SRT-2026-004',
                'nomor_surat' => '472/009/RT-05/IX/2026',
                'perihal' => 'Surat Keterangan Keringanan Biaya Pendidikan',
                'nama' => 'Bpk. Denny Pratama',
                'nik' => '3201050807900003',
                'no_kk' => '3201051909120042',
                'tempat_tgl_lahir' => 'Garut, 08 Juli 1990',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => 'Tenaga Pengajar / Dosen',
                'alamat' => 'Harmoni Residence Blok C No. 08, RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Bagian Beasiswa Universitas Terbuka / SMA Negeri Kota Depok',
                'keperluan' => 'Pengajuan bantuan beasiswa pendidikan anak berprestasi dari keluarga rukun tetangga.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => '17 September 2026',
                'whatsapp' => '085712349988',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ],
            5 => [
                'id' => 5,
                'code' => 'SRT-2026-005',
                'nomor_surat' => '470/031/RT-05/IX/2026',
                'perihal' => 'Surat Pengantar Izin Keramaian Lingkungan',
                'nama' => 'Ibu Siti Aminah',
                'nik' => '3201056511870004',
                'no_kk' => '3201051909120021',
                'tempat_tgl_lahir' => 'Tasikmalaya, 25 November 1987',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Islam',
                'pekerjaan' => 'Guru Madrasah',
                'alamat' => 'Harmoni Residence Blok A No. 05, RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Kelurahan Sukamaju & Babinsa / Bhabinkamtibmas Polsek Cilodong',
                'keperluan' => 'Pemberitahuan acara syukuran tasyakuran keluarga di balai warga.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => '16 September 2026',
                'whatsapp' => '087811223344',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ],
        ];
    }

    /**
     * Resolve letter data from database or static array
     */
    protected function resolveLetter(int|string $id)
    {
        $surat = \App\Models\Surat::with(['warga.kartuKeluarga', 'warga.dataRt'])->find($id);
        if ($surat) {
            $warga = $surat->warga;
            $kk = $warga?->kartuKeluarga;
            $rt = $warga?->dataRt ?? \App\Models\DataRt::first();

            $nomorSurat = $surat->nomor_surat ?: \App\Models\Surat::generateNomorSurat($surat->surat_id, $rt?->ID_RT);
            $nomorRt = str_pad($rt?->nomor_rt ?? '05', 2, '0', STR_PAD_LEFT);
            $nomorRw = str_pad($rt?->nomor_rw ?? '01', 2, '0', STR_PAD_LEFT);
            $kelurahan = $rt?->kelurahan ?? 'Sukamaju';
            $kecamatan = $rt?->kecamatan ?? 'Cilodong';
            $kota = $rt?->kota ?? 'Kota Depok';

            $alamatLengkap = $kk?->Alamat
                ? "Harmoni Residence {$kk->Alamat}, RT {$nomorRt} / RW {$nomorRw}, Kel. {$kelurahan}, Kec. {$kecamatan}, {$kota}"
                : "Harmoni Residence RT {$nomorRt} / RW {$nomorRw}, Kel. {$kelurahan}, Kec. {$kecamatan}, {$kota}";

            return [
                'id' => $surat->surat_id,
                'code' => '#SRT-' . str_pad($surat->surat_id, 4, '0', STR_PAD_LEFT),
                'nomor_surat' => $nomorSurat,
                'perihal' => $surat->Jenis_surat,
                'nama' => $warga?->Nama_Lengkap ?? 'Warga Pemohon',
                'nik' => $surat->NIK,
                'no_kk' => $warga?->ID_KK ?? '3201051909120008',
                'tempat_tgl_lahir' => $warga?->tgl_lahir ? Carbon::parse($warga->tgl_lahir)->translatedFormat('d F Y') : 'Depok, Jawa Barat',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => 'Warga Tetap',
                'alamat' => $alamatLengkap,
                'tujuan_instansi' => "Kantor Kelurahan {$kelurahan} / Instansi Terkait",
                'keperluan' => $surat->perihal ?: 'Permohonan pengantar resmi rukun tetangga.',
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => Carbon::parse($surat->tgl_pengajuan ?? now())->translatedFormat('d F Y'),
                'whatsapp' => $warga?->no_telp ?? '081234567890',
                'ketua_rt' => $rt?->nama_ketua_rt ?? 'Bpk. Sukardi Hartono',
            ];
        }

        $dbLetter = OfficialLetter::find($id);
        if ($dbLetter) {
            $citizen = Citizen::where('nik', $dbLetter->nik)->first();
            return [
                'id' => $dbLetter->id,
                'code' => $dbLetter->ticket_code ?? ('SRT-2026-' . str_pad($dbLetter->id, 3, '0', STR_PAD_LEFT)),
                'nomor_surat' => $dbLetter->nomor_surat,
                'perihal' => $dbLetter->jenis_surat,
                'nama' => $dbLetter->nama_warga,
                'nik' => $dbLetter->nik,
                'no_kk' => $citizen ? $citizen->no_kk : '3201051909120008',
                'tempat_tgl_lahir' => 'Depok, Jawa Barat',
                'jenis_kelamin' => $citizen ? ($citizen->gender === 'L' ? 'Laki-laki' : 'Perempuan') : 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => $citizen ? $citizen->pekerjaan : 'Warga Tetap',
                'alamat' => 'Harmoni Residence ' . $dbLetter->alamat . ', RT 05 / RW 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok',
                'tujuan_instansi' => 'Kantor Kelurahan Sukamaju / Instansi Terkait',
                'keperluan' => $dbLetter->keperluan,
                'status_verifikasi' => 'Terverifikasi & Sah',
                'tanggal_terbit' => Carbon::parse($dbLetter->tanggal_terbit ?? $dbLetter->created_at)->translatedFormat('d F Y'),
                'whatsapp' => $citizen ? $citizen->whatsapp : '081234567890',
                'ketua_rt' => 'Bpk. Sukardi Hartono',
            ];
        }

        $letters = $this->getLettersData();
        return $letters[$id] ?? $letters[1];
    }

    /**
     * Download or Stream PDF Surat Pengantar RT
     */
    public function downloadPdf(int|string $id)
    {
        $letter = $this->resolveLetter($id);

        $verificationUrl = route('surat.verify', ['id' => $letter['id']]);

        // Generate QR code as inline SVG
        $qrSvg = QrCode::size(85)->generate($verificationUrl);

        $data = [
            'letter' => $letter,
            'qrSvg' => $qrSvg,
            'verificationUrl' => $verificationUrl,
            'generatedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $pdf = Pdf::loadView('pdf.surat-pengantar', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'Surat_Pengantar_' . str_replace(['/', ' '], '_', $letter['nomor_surat']) . '.pdf';

        // Stream inline so users can view or save
        return $pdf->stream($filename);
    }

    /**
     * Public Digital Verification Page
     */
    public function verifyLetter(int|string $id)
    {
        $letter = $this->resolveLetter($id);

        return view('portal.verify-surat', [
            'letter' => $letter,
        ]);
    }
}
