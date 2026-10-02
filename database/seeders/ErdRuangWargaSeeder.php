<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\DataRt;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Surat;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use App\Models\Kegiatan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;

class ErdRuangWargaSeeder extends Seeder
{
    /**
     * Seed 13 Tabel ERD Resmi RuangWarga (Verbatim)
     */
    public function run(): void
    {
        // 1. DATA_RT (ID_RT, nomor_rt, nomor_rw, kelurahan, kecamatan, kota, nama_ketua_rt)
        $dataRt1 = DataRt::updateOrCreate(
            ['ID_RT' => 1],
            [
                'nomor_rt' => '05',
                'nomor_rw' => '01',
                'kelurahan' => 'Sukamaju',
                'kecamatan' => 'Cilodong',
                'kota' => 'Kota Depok',
                'nama_ketua_rt' => 'Bpk. Sukardi Hartono',
            ]
        );

        $dataRt2 = DataRt::updateOrCreate(
            ['ID_RT' => 2],
            [
                'nomor_rt' => '01',
                'nomor_rw' => '02',
                'kelurahan' => 'Mekarjaya',
                'kecamatan' => 'Sukmajaya',
                'kota' => 'Kota Depok',
                'nama_ketua_rt' => 'Bpk. Bambang Pamungkas',
            ]
        );

        $dataRt3 = DataRt::updateOrCreate(
            ['ID_RT' => 3],
            [
                'nomor_rt' => '03',
                'nomor_rw' => '04',
                'kelurahan' => 'Baktijaya',
                'kecamatan' => 'Sukmajaya',
                'kota' => 'Kota Depok',
                'nama_ketua_rt' => 'Ibu Sri Mulyani',
            ]
        );

        // 2. ADMIN (Admin_ID, Username, Password_hash, role, ID_RT)
        $admins = [
            [
                'Admin_ID' => 1,
                'Username' => 'superadmin',
                'Password_hash' => Hash::make('Rahasia123!'),
                'role' => 'super_admin',
                'ID_RT' => 1,
            ],
            [
                'Admin_ID' => 2,
                'Username' => 'ketua_rt05',
                'Password_hash' => Hash::make('Rahasia123!'),
                'role' => 'admin_rt',
                'ID_RT' => 1,
            ],
            [
                'Admin_ID' => 3,
                'Username' => 'bendahara_rt05',
                'Password_hash' => Hash::make('Rahasia123!'),
                'role' => 'bendahara',
                'ID_RT' => 1,
            ],
            [
                'Admin_ID' => 4,
                'Username' => 'sekretaris_rt05',
                'Password_hash' => Hash::make('Rahasia123!'),
                'role' => 'sekretaris',
                'ID_RT' => 1,
            ],
        ];

        foreach ($admins as $adm) {
            Admin::updateOrCreate(['Admin_ID' => $adm['Admin_ID']], $adm);
        }

        // 3. KARTU_KELUARGA (ID_KK, Alamat)
        $kkList = [
            [
                'ID_KK' => '3201051909120008',
                'Alamat' => 'Harmoni Residence Blok B No. 12',
            ],
            [
                'ID_KK' => '3201051909120002',
                'Alamat' => 'Harmoni Residence Blok C No. 04',
            ],
            [
                'ID_KK' => '3201051909120003',
                'Alamat' => 'Harmoni Residence Blok C No. 08',
            ],
            [
                'ID_KK' => '3201051909120004',
                'Alamat' => 'Harmoni Residence Blok A No. 02',
            ],
            [
                'ID_KK' => '3201051909120005',
                'Alamat' => 'Harmoni Residence Blok B No. 05',
            ],
            [
                'ID_KK' => '3201051909120006',
                'Alamat' => 'Harmoni Residence Blok D No. 07',
            ],
        ];

        foreach ($kkList as $kk) {
            KartuKeluarga::updateOrCreate(['ID_KK' => $kk['ID_KK']], $kk);
        }

        // 4. WARGA (NIK, ID_KK, ID_RT, Nama_Lengkap, no_telp, Username, Password_hash, tgl_lahir, role)
        $wargaList = [
            [
                'NIK' => '3201051208940001',
                'ID_KK' => '3201051909120008',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Bpk. Hendra Wijaya',
                'no_telp' => '0812-3456-7890',
                'Username' => 'hendra_w',
                'Password_hash' => Hash::make('WargaHarmoni2026!'),
                'tgl_lahir' => '1994-08-12',
                'role' => 'warga',
            ],
            [
                'NIK' => '3201987654320001',
                'ID_KK' => '3201051909120002',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Ibu Dewi Sartika',
                'no_telp' => '0856-1122-3344',
                'Username' => 'dewi_s',
                'Password_hash' => Hash::make('WargaHarmoni2026!'),
                'tgl_lahir' => '1996-03-24',
                'role' => 'warga',
            ],
            [
                'NIK' => '3201556677880004',
                'ID_KK' => '3201051909120003',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Bpk. Denny Pratama',
                'no_telp' => '0813-9988-7766',
                'Username' => 'denny_p',
                'Password_hash' => Hash::make('WargaHarmoni2026!') ,
                'tgl_lahir' => '1990-11-15',
                'role' => 'warga',
            ],
            [
                'NIK' => '3201223344550005',
                'ID_KK' => '3201051909120004',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Bpk. Agus Santoso',
                'no_telp' => '0812-3344-5566',
                'Username' => 'agus_s',
                'Password_hash' => Hash::make('WargaHarmoni2026!'),
                'tgl_lahir' => '1988-06-10',
                'role' => 'warga',
            ],
            [
                'NIK' => '3201778899000007',
                'ID_KK' => '3201051909120005',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Ibu Siti Rahma',
                'no_telp' => '0857-1122-3344',
                'Username' => 'siti_r',
                'Password_hash' => Hash::make('WargaHarmoni2026!'),
                'tgl_lahir' => '1992-09-02',
                'role' => 'warga',
            ],
            [
                'NIK' => '3201889900110009',
                'ID_KK' => '3201051909120006',
                'ID_RT' => 1,
                'Nama_Lengkap' => 'Ibu Nurhayati',
                'no_telp' => '0819-2233-4455',
                'Username' => 'nurhayati',
                'Password_hash' => Hash::make('WargaHarmoni2026!'),
                'tgl_lahir' => '1985-12-19',
                'role' => 'warga',
            ],
        ];

        foreach ($wargaList as $w) {
            Warga::updateOrCreate(['NIK' => $w['NIK']], $w);
        }

        // 5. SURAT (surat_id, NIK, Jenis_surat, perihal, tgl_pengajuan, status)
        $suratList = [
            [
                'surat_id' => 1,
                'NIK' => '3201051208940001',
                'Jenis_surat' => 'Surat Pengantar Pembuatan KTP & KK Baru',
                'perihal' => 'Permohonan surat pengantar untuk pemutakhiran kartu keluarga dan KTP-el ke Kantor Kelurahan Sukamaju.',
                'tgl_pengajuan' => Carbon::parse('2026-09-20'),
                'status' => 'Disetujui',
            ],
            [
                'surat_id' => 2,
                'NIK' => '3201051208940001',
                'Jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
                'perihal' => 'Kelengkapan administrasi perpanjangan SKCK untuk melamar pekerjaan.',
                'tgl_pengajuan' => Carbon::parse('2026-09-19'),
                'status' => 'Disetujui',
            ],
            [
                'surat_id' => 3,
                'NIK' => '3201987654320001',
                'Jenis_surat' => 'Surat Keterangan Domisili Tempat Tinggal',
                'perihal' => 'Kelengkapan administrasi pendaftaran sekolah anak dan perbankan.',
                'tgl_pengajuan' => Carbon::parse('2026-09-18'),
                'status' => 'Disetujui',
            ],
            [
                'surat_id' => 4,
                'NIK' => '3201556677880004',
                'Jenis_surat' => 'Surat Keterangan Usaha (SKU) Mikro Lingkungan',
                'perihal' => 'Pengajuan KUR Mikro Usaha Kuliner Rumahan.',
                'tgl_pengajuan' => Carbon::parse('2026-09-15'),
                'status' => 'Disetujui',
            ],
            [
                'surat_id' => 5,
                'NIK' => '3201223344550005',
                'Jenis_surat' => 'Surat Keterangan Izin Keramaian Lingkungan',
                'perihal' => 'Izin mengadakan syukuran aqiqah di halaman rumah pada Sabtu mendatang.',
                'tgl_pengajuan' => Carbon::parse('2026-09-21'),
                'status' => 'Menunggu',
            ],
        ];

        foreach ($suratList as $s) {
            Surat::updateOrCreate(['surat_id' => $s['surat_id']], $s);
        }

        // 6. ASPIRASI (id_aspirasi, NIK, Isi_Aspirasi)
        $aspirasiList = [
            [
                'id_aspirasi' => 1,
                'NIK' => '3201556677880004',
                'Isi_Aspirasi' => 'Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.',
            ],
            [
                'id_aspirasi' => 2,
                'NIK' => '3201889900110009',
                'Isi_Aspirasi' => 'Saran agar di sekitar pendopo disediakan tempat sampah pilah organik dan anorganik agar warga dan anak-anak terbiasa memilah sampah sejak dini.',
            ],
            [
                'id_aspirasi' => 3,
                'NIK' => '3201051208940001',
                'Isi_Aspirasi' => 'Mengingat sudah mulai masuk masa pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti lingkungan akhir pekan ini.',
            ],
        ];

        foreach ($aspirasiList as $asp) {
            Aspirasi::updateOrCreate(['id_aspirasi' => $asp['id_aspirasi']], $asp);
        }

        // 7. RESPON_ASPIRASI (id_aspirasi, nama_responden, isi_respon, tanggal_respon)
        $responList = [
            [
                'id_aspirasi' => 1,
                'nama_responden' => 'Bpk. Sukardi Hartono (Ketua RT 05)',
                'isi_respon' => 'Terima kasih usulannya Pak Denny. Pembelian lampu sorot sudah disetujui bendahara dan akan dipasang teknisi minggu ini.',
                'tanggal_respon' => Carbon::parse('2026-09-20 10:30:00'),
            ],
            [
                'id_aspirasi' => 3,
                'nama_responden' => 'Ibu Ratna Paramitha (Sekretaris)',
                'isi_respon' => 'Sudah diagendakan dalam jadwal kerja bakti serentak Sabtu pagi ini.',
                'tanggal_respon' => Carbon::parse('2026-09-21 08:45:00'),
            ],
        ];

        foreach ($responList as $resp) {
            ResponAspirasi::updateOrCreate(['id_aspirasi' => $resp['id_aspirasi']], $resp);
        }

        // 8. KEGIATAN (kegiatan_id, ID_RT, nama_kegiatan, tgl_kegiatan, lokasi, deskripsi)
        $kegiatanList = [
            [
                'kegiatan_id' => 1,
                'ID_RT' => 1,
                'nama_kegiatan' => 'Kerja Bakti Massal & Fogging Nyamuk DBD',
                'tgl_kegiatan' => Carbon::parse('2026-09-26'),
                'lokasi' => 'Balai Warga & Lingkungan RT 05',
                'deskripsi' => 'Pelaksanaan Sabtu pukul 07.00 WIB. Pembersihan saluran air dan fogging serentak.',
            ],
            [
                'kegiatan_id' => 2,
                'ID_RT' => 1,
                'nama_kegiatan' => 'Pelatihan Hidroponik PKK Mandiri Pangan',
                'tgl_kegiatan' => Carbon::parse('2026-10-04'),
                'lokasi' => 'Taman Samping Balai RT',
                'deskripsi' => 'Program pemberdayaan warga: budidaya sayur hidroponik pipa PVC dan nutrisi AB Mix.',
            ],
            [
                'kegiatan_id' => 3,
                'ID_RT' => 1,
                'nama_kegiatan' => 'Senam Sehat Lansia & Cek Tensi Gratis',
                'tgl_kegiatan' => Carbon::parse('2026-09-27'),
                'lokasi' => 'Halaman Balai Warga',
                'deskripsi' => 'Senam kebugaran bersama dilanjutkan pemeriksaan tekanan darah dan gula darah berkala.',
            ],
        ];

        foreach ($kegiatanList as $keg) {
            Kegiatan::updateOrCreate(['kegiatan_id' => $keg['kegiatan_id']], $keg);
        }

        // 9. PEMASUKAN (Tagihan_Id, ID_KK, Kategori, ID_RT, nama_tagihan, nominal, tgl_jatuh_tmp, status_tagihan)
        $pemasukanList = [
            [
                'Tagihan_Id' => 1,
                'ID_KK' => '3201051909120008',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan Juli 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-07-10'),
                'status_tagihan' => 'Lunas',
                'bukti_bayar' => 'uploads/bukti/bukti_iuran_1790129381_6ab334e5d2c08.jpg',
            ],
            [
                'Tagihan_Id' => 2,
                'ID_KK' => '3201051909120008',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan Agustus 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-08-10'),
                'status_tagihan' => 'Lunas',
                'bukti_bayar' => 'uploads/bukti/bukti_iuran_1790129399_6ab334f77bb78.jpg',
            ],
            [
                'Tagihan_Id' => 3,
                'ID_KK' => '3201051909120008',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'Lunas',
                'bukti_bayar' => 'uploads/bukti/bukti_iuran_1790129427_6ab335133cbfc.jpg',
            ],
            [
                'Tagihan_Id' => 4,
                'ID_KK' => '3201051909120002',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'pending_approval',
                'bukti_bayar' => 'uploads/bukti/bukti_iuran_1790129459_6ab3353352069.jpg',
            ],
            [
                'Tagihan_Id' => 5,
                'ID_KK' => '3201051909120003',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'Lunas',
                'bukti_bayar' => null,
            ],
            [
                'Tagihan_Id' => 6,
                'ID_KK' => '3201051909120004',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'Lunas',
                'bukti_bayar' => null,
            ],
            [
                'Tagihan_Id' => 7,
                'ID_KK' => '3201051909120005',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'Belum Lunas',
                'bukti_bayar' => null,
            ],
            [
                'Tagihan_Id' => 8,
                'ID_KK' => '3201051909120006',
                'Kategori' => 'Iuran Warga',
                'ID_RT' => 1,
                'nama_tagihan' => 'Iuran Keamanan & Kebersihan September 2026',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => Carbon::parse('2026-09-10'),
                'status_tagihan' => 'Belum Lunas',
                'bukti_bayar' => null,
            ],
        ];

        foreach ($pemasukanList as $pem) {
            Pemasukan::updateOrCreate(['Tagihan_Id' => $pem['Tagihan_Id']], $pem);
        }

        // 10. PENGELUARAN (Pengeluaran_id, ID_RT, Kategori, Keterangan, Jumlah_Bayar, Tgl_Bayar, Metode_Bayar, Bukti_Path)
        $pengeluaranList = [
            [
                'Pengeluaran_id' => 1,
                'ID_RT' => 1,
                'Kategori' => 'Keamanan',
                'Keterangan' => 'Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',
                'Jumlah_Bayar' => 4162500,
                'Tgl_Bayar' => Carbon::parse('2026-09-02'),
                'Metode_Bayar' => 'Transfer Bank',
                'Bukti_Path' => 'bukti/nota_lampu_philips.jpg',
            ],
            [
                'Pengeluaran_id' => 2,
                'ID_RT' => 1,
                'Kategori' => 'Kebersihan',
                'Keterangan' => 'Retribusi Angkut Sampah DLH & Kebersihan Selokan',
                'Jumlah_Bayar' => 2775000,
                'Tgl_Bayar' => Carbon::parse('2026-09-04'),
                'Metode_Bayar' => 'Kas Tunai',
                'Bukti_Path' => 'bukti/nota_lampu_philips.jpg',
            ],
            [
                'Pengeluaran_id' => 3,
                'ID_RT' => 1,
                'Kategori' => 'Fasilitas & Aset',
                'Keterangan' => 'Pembelian Lampu Sorot LED & Kabel Ekstensi Balai',
                'Jumlah_Bayar' => 450000,
                'Tgl_Bayar' => Carbon::parse('2026-09-10'),
                'Metode_Bayar' => 'Kas Tunai',
                'Bukti_Path' => 'bukti/nota_lampu_philips.jpg',
            ],
        ];

        foreach ($pengeluaranList as $peng) {
            Pengeluaran::updateOrCreate(['Pengeluaran_id' => $peng['Pengeluaran_id']], $peng);
        }

        // 11. INVENTARIS (Inventaris_ID, ID_RT, Nama_Barang, Kode_Barang, Jumlah, Lokasi)
        $inventarisList = [
            [
                'Inventaris_ID' => 1,
                'ID_RT' => 1,
                'Nama_Barang' => 'Tenda Lipat (3x6 Meter)',
                'Kode_Barang' => 'AST-TND-01',
                'Jumlah' => 2,
                'Lokasi' => 'Gudang Balai RT 05',
            ],
            [
                'Inventaris_ID' => 2,
                'ID_RT' => 1,
                'Nama_Barang' => 'Set Kursi Lipat Warga (50 Unit)',
                'Kode_Barang' => 'AST-KRS-01',
                'Jumlah' => 100,
                'Lokasi' => 'Gudang Balai RT 05',
            ],
            [
                'Inventaris_ID' => 3,
                'ID_RT' => 1,
                'Nama_Barang' => 'Wireless Portable Sound System + 2 Mic',
                'Kode_Barang' => 'AST-SND-01',
                'Jumlah' => 1,
                'Lokasi' => 'Ruang Sekretariat Balai RT',
            ],
            [
                'Inventaris_ID' => 4,
                'ID_RT' => 1,
                'Nama_Barang' => 'Proyektor Epson & Layar Tripod 84 Inch',
                'Kode_Barang' => 'AST-PRJ-01',
                'Jumlah' => 1,
                'Lokasi' => 'Ruang Sekretariat Balai RT',
            ],
            [
                'Inventaris_ID' => 5,
                'ID_RT' => 1,
                'Nama_Barang' => 'Mesin Pemotong Rumput Dorong Honda',
                'Kode_Barang' => 'AST-RMPT-01',
                'Jumlah' => 2,
                'Lokasi' => 'Gudang Samping Pos Ronda',
            ],
            [
                'Inventaris_ID' => 6,
                'ID_RT' => 1,
                'Nama_Barang' => 'Paket Rompi & Senter Ronda Keamanan (6 Set)',
                'Kode_Barang' => 'AST-RND-01',
                'Jumlah' => 6,
                'Lokasi' => 'Pos Ronda Utama RT 05',
            ],
        ];

        foreach ($inventarisList as $inv) {
            Inventaris::updateOrCreate(['Inventaris_ID' => $inv['Inventaris_ID']], $inv);
        }

        // 12. PEMBELIAN_INVENTARIS (Pembelian_id, inventaris_id, Pengeluaran_id, tgl_pembelian, total_biaya, jumlah_pembelian)
        $pembelianList = [
            [
                'Pembelian_id' => 1,
                'inventaris_id' => 3,
                'Pengeluaran_id' => 3,
                'tgl_pembelian' => Carbon::parse('2026-08-15'),
                'total_biaya' => 2450000,
                'jumlah_pembelian' => 1,
            ],
            [
                'Pembelian_id' => 2,
                'inventaris_id' => 5,
                'Pengeluaran_id' => 3,
                'tgl_pembelian' => Carbon::parse('2026-07-20'),
                'total_biaya' => 3200000,
                'jumlah_pembelian' => 1,
            ],
        ];

        foreach ($pembelianList as $pem) {
            PembelianInventaris::updateOrCreate(['Pembelian_id' => $pem['Pembelian_id']], $pem);
        }

        // 13. APPROVAL (id_approval, Admin_ID, tabel_sumber, id_referensi, status, catatan, tgl_approval)
        $approvalList = [
            [
                'id_approval' => 1,
                'Admin_ID' => 2, // Ketua RT
                'tabel_sumber' => 'SURAT',
                'id_referensi' => '1',
                'status' => 'Disetujui',
                'catatan' => 'Surat pengantar KTP & KK telah diverifikasi dan ditandatangani digital.',
                'tgl_approval' => Carbon::parse('2026-09-20 09:15:00'),
            ],
            [
                'id_approval' => 2,
                'Admin_ID' => 2,
                'tabel_sumber' => 'SURAT',
                'id_referensi' => '2',
                'status' => 'Disetujui',
                'catatan' => 'Surat pengantar SKCK disetujui.',
                'tgl_approval' => Carbon::parse('2026-09-19 14:20:00'),
            ],
            [
                'id_approval' => 3,
                'Admin_ID' => 3, // Bendahara
                'tabel_sumber' => 'PEMASUKAN',
                'id_referensi' => '3',
                'status' => 'Disetujui',
                'catatan' => 'Pembayaran iuran kas via QRIS Rp 75.000 terverifikasi sah masuk rekening kas RT.',
                'tgl_approval' => Carbon::parse('2026-09-05 16:45:00'),
            ],
            [
                'id_approval' => 4,
                'Admin_ID' => 2,
                'tabel_sumber' => 'INVENTARIS',
                'id_referensi' => '1',
                'status' => 'Disetujui',
                'catatan' => 'Peminjaman Tenda Lipat untuk Bpk. Agus Santoso disetujui.',
                'tgl_approval' => Carbon::parse('2026-09-20 08:00:00'),
            ],
            [
                'id_approval' => 5,
                'Admin_ID' => 2,
                'tabel_sumber' => 'INVENTARIS',
                'id_referensi' => '2',
                'status' => 'Menunggu',
                'catatan' => 'Permohonan pinjam Tenda & Kursi oleh Bpk. Denny Pratama untuk acara aqiqah.',
                'tgl_approval' => null,
            ],
            [
                'id_approval' => 6,
                'Admin_ID' => 4, // Sekretaris
                'tabel_sumber' => 'SURAT',
                'id_referensi' => '3',
                'status' => 'Menunggu',
                'catatan' => 'Pengajuan Surat Keterangan Usaha (SKU) untuk pendaftaran UMKM binaan RT.',
                'tgl_approval' => null,
            ],
            [
                'id_approval' => 7,
                'Admin_ID' => 3, // Bendahara
                'tabel_sumber' => 'PEMASUKAN',
                'id_referensi' => '4',
                'status' => 'Menunggu',
                'catatan' => 'Konfirmasi pembayaran iuran bulanan September 2026 warga Blok C No. 08 via QRIS.',
                'tgl_approval' => null,
            ],
            [
                'id_approval' => 8,
                'Admin_ID' => 2, // Ketua RT
                'tabel_sumber' => 'KEGIATAN',
                'id_referensi' => '2',
                'status' => 'Menunggu',
                'catatan' => 'Usulan agenda Senam Kebugaran Lansia dan Cek Tensi Darah berkala di Balai Warga.',
                'tgl_approval' => null,
            ],
            [
                'id_approval' => 9,
                'Admin_ID' => 3, // Bendahara
                'tabel_sumber' => 'PENGELUARAN',
                'id_referensi' => '1',
                'status' => 'Menunggu',
                'catatan' => 'Pengajuan belanja lampu sorot LED 100W lapangan voli balai RT.',
                'tgl_approval' => null,
            ],
        ];

        foreach ($approvalList as $app) {
            Approval::updateOrCreate(['id_approval' => $app['id_approval']], $app);
        }
    }
}
