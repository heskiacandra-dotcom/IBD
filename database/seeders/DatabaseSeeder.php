<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RtUnit;
use App\Models\Citizen;
use App\Models\ApprovalTicket;
use App\Models\OfficialLetter;
use App\Models\CashMutation;
use App\Models\Aspiration;
use App\Models\AssetItem;
use App\Models\AssetLoan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Pengurus & Warga)
        $users = [
            [
                'name' => 'Bima Perkasa (Super Admin)',
                'email' => 'superadmin@ruangwarga.id',
                'password' => Hash::make('Rahasia123!'),
            ],
            [
                'name' => 'Bpk. Sukardi Hartono (Ketua RT 05)',
                'email' => 'ketua@ruangwarga.id',
                'password' => Hash::make('Rahasia123!'),
            ],
            [
                'name' => 'Bpk. Rahmat Hendrawan (Bendahara)',
                'email' => 'bendahara@ruangwarga.id',
                'password' => Hash::make('Rahasia123!'),
            ],
            [
                'name' => 'Ibu Ratna Paramitha (Sekretaris)',
                'email' => 'sekretaris@ruangwarga.id',
                'password' => Hash::make('Rahasia123!'),
            ],
            [
                'name' => 'Bpk. Hendra Wijaya (Warga)',
                'email' => 'hendra@ruangwarga.id',
                'password' => Hash::make('WargaHarmoni2026!'),
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 2. Seed RT Unit
        $rt05 = RtUnit::updateOrCreate(
            ['kode_rt' => 'RT 05 / RW 01'],
            [
                'nama_kawasan' => 'Harmoni Residence',
                'kelurahan' => 'Sukamaju',
                'kecamatan' => 'Cilodong',
                'kota' => 'Kota Depok',
                'ketua_nama' => 'Bpk. Sukardi Hartono',
                'ketua_kontak' => '0812-9876-5432',
                'status' => 'aktif',
            ]
        );

        // Tambah unit RT lain untuk tampilan Super Admin
        RtUnit::updateOrCreate(
            ['kode_rt' => 'RT 01 / RW 02'],
            [
                'nama_kawasan' => 'Griya Asri Pratama',
                'kelurahan' => 'Mekarjaya',
                'kecamatan' => 'Sukmajaya',
                'kota' => 'Kota Depok',
                'ketua_nama' => 'Bpk. Bambang Pamungkas',
                'ketua_kontak' => '0813-1122-3344',
                'status' => 'aktif',
            ]
        );

        RtUnit::updateOrCreate(
            ['kode_rt' => 'RT 03 / RW 04'],
            [
                'nama_kawasan' => 'Taman Pesona Boulevard',
                'kelurahan' => 'Baktijaya',
                'kecamatan' => 'Sukmajaya',
                'kota' => 'Kota Depok',
                'ketua_nama' => 'Ibu Sri Mulyani',
                'ketua_kontak' => '0857-9988-7711',
                'status' => 'aktif',
            ]
        );

        // 3. Seed Warga (Citizens)
        $citizens = [
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201051208940001', // Sesuai default SSO Login Warga
                'no_kk' => '3201051909120008',
                'nama' => 'Bpk. Hendra Wijaya',
                'alamat' => 'Blok B No. 12',
                'whatsapp' => '0812-3456-7890',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Karyawan Swasta / BUMN',
                'gender' => 'L',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201055504880002',
                'no_kk' => '3201051909120015',
                'nama' => 'Ibu Dewi Sartika',
                'alamat' => 'Blok C No. 04',
                'whatsapp' => '0813-9876-5432',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Wiraswasta Kuliner',
                'gender' => 'P',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201050807900003',
                'no_kk' => '3201051909120042',
                'nama' => 'Bpk. Denny Pratama',
                'alamat' => 'Blok C No. 08',
                'whatsapp' => '0857-1234-9988',
                'status_domisili' => 'kontrak',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Tenaga Pengajar / Dosen',
                'gender' => 'L',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201056511870004',
                'no_kk' => '3201051909120021',
                'nama' => 'Ibu Siti Aminah',
                'alamat' => 'Blok A No. 05',
                'whatsapp' => '0878-1122-3344',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => false,
                'pekerjaan' => 'Guru Madrasah',
                'gender' => 'P',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201052203090005',
                'no_kk' => '3201051909120008',
                'nama' => 'Dimas Arya Nugraha',
                'alamat' => 'Blok B No. 12',
                'whatsapp' => '0821-5566-7788',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => false,
                'pekerjaan' => 'Pelajar SMA',
                'gender' => 'L',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201051402920006',
                'no_kk' => '3201051909120055',
                'nama' => 'Bpk. Ridwan Santoso',
                'alamat' => 'Blok D No. 02',
                'whatsapp' => '0819-9988-7766',
                'status_domisili' => 'kontrak',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Konsultan IT',
                'gender' => 'L',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201054809830007',
                'no_kk' => '3201051909120063',
                'nama' => 'Ibu Euis Komala',
                'alamat' => 'Blok A No. 11',
                'whatsapp' => '0812-8899-0011',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Aparatur Sipil Negara (ASN)',
                'gender' => 'P',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201052906950008',
                'no_kk' => '3201051909120078',
                'nama' => 'Bpk. Fajar Ramadhan',
                'alamat' => 'Blok C No. 15',
                'whatsapp' => '0822-1144-7788',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Arsitek Desain',
                'gender' => 'L',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'nik' => '3201053001850009',
                'no_kk' => '3201051909120099',
                'nama' => 'Bpk. Taryo (Teknisi RT)',
                'alamat' => 'Pos Ronda Utama',
                'whatsapp' => '0852-7788-9900',
                'status_domisili' => 'tetap',
                'is_kepala_keluarga' => true,
                'pekerjaan' => 'Teknisi Listrik & Fasum',
                'gender' => 'L',
            ],
        ];

        foreach ($citizens as $c) {
            Citizen::updateOrCreate(['nik' => $c['nik']], $c);
        }

        // 4. Seed Approval Tickets (Jembatan Warga <-> Pengurus)
        $tickets = [
            [
                'ticket_code' => '#APP-2026-001',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 1,
                'applicant' => 'Bpk. Hendra Wijaya',
                'house' => 'Blok B No. 12',
                'nik' => '3201051208940001',
                'phone' => '0812-3456-7890',
                'tabel_sumber' => 'SURAT',
                'tab' => 'surat',
                'perihal' => 'Surat Pengantar Pembuatan KTP & KK Baru',
                'nominal' => '-',
                'status' => 'pending',
                'detail_lengkap' => 'Permohonan surat pengantar untuk perpanjangan identitas KTP elektronik dan pemutakhiran anggota keluarga yang baru lahir.',
                'preview_type' => 'document',
                'preview_title' => 'Draft Surat Pengantar RT No. 470/05/IX/2026',
                'preview_meta' => 'Dilengkapi Barcode Digital Sign Ketua RT 05',
                'created_at' => Carbon::parse('2026-09-20 09:30:00'),
            ],
            [
                'ticket_code' => '#APP-2026-002',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 2,
                'applicant' => 'Ibu Dewi Sartika',
                'house' => 'Blok C No. 04',
                'nik' => '3201987654320001',
                'phone' => '0821-4455-6677',
                'tabel_sumber' => 'PEMASUKAN',
                'tab' => 'keuangan',
                'perihal' => 'Iuran Keamanan & Sampah Periode September 2026',
                'nominal' => 'Rp 75.000',
                'status' => 'pending',
                'detail_lengkap' => 'Konfirmasi pembayaran iuran bulanan via QRIS Standar Bank Mandiri (Ref: #TX-99210-QRIS).',
                'preview_type' => 'receipt',
                'preview_title' => 'Struk Pembayaran QRIS Nasional (NMID: ID1029384756)',
                'preview_meta' => 'Nominal: Rp 75.000 • Status Bank: BERHASIL',
                'created_at' => Carbon::parse('2026-09-20 09:15:00'),
            ],
            [
                'ticket_code' => '#APP-2026-003',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 5,
                'applicant' => 'Bpk. Taryo (Teknisi RT)',
                'house' => 'Pos Ronda Utama',
                'nik' => '3201556677880004',
                'phone' => '0813-9988-7766',
                'tabel_sumber' => 'PENGELUARAN',
                'tab' => 'keuangan',
                'perihal' => 'Klaim Penggantian Lampu LED PJU Gang Mawar (3 Titik)',
                'nominal' => 'Rp 345.000',
                'status' => 'pending',
                'detail_lengkap' => 'Pembelian 3 unit bohlam LED Philips 24W outdoor dan kabel instalasi tahan panas untuk lampu penerangan gang Blok C.',
                'preview_type' => 'receipt',
                'preview_title' => 'Nota Toko Listrik Terang Abadi No. INV-88219',
                'preview_meta' => 'Total: Rp 345.000 • Stempel Lunas',
                'created_at' => Carbon::parse('2026-09-19 16:45:00'),
            ],
            [
                'ticket_code' => '#APP-2026-004',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 6,
                'applicant' => 'Ibu Nurhayati (Kader PKK)',
                'house' => 'Blok D No. 07',
                'nik' => '3201889900110009',
                'phone' => '0819-2233-4455',
                'tabel_sumber' => 'KEGIATAN',
                'tab' => 'kegiatan',
                'perihal' => 'Pengajuan Anggaran Pelatihan Hidroponik Ibu-Ibu RT',
                'nominal' => 'Rp 650.000',
                'status' => 'pending',
                'detail_lengkap' => 'Program pemberdayaan warga mandiri pangan: pengadaan bibit sayur, netpot, nutrisi AB Mix dan modul instalasi pipa hidroponik di taman samping balai warga.',
                'preview_type' => 'event',
                'preview_title' => 'Proposal Singkat PKK RT 05 No. 03/PKK/IX/2026',
                'preview_meta' => 'Rencana Pelaksanaan: Minggu, 4 Oktober 2026',
                'created_at' => Carbon::parse('2026-09-18 14:00:00'),
            ],
            [
                'ticket_code' => '#APP-2026-005',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 3,
                'applicant' => 'Bpk. Denny Pratama',
                'house' => 'Blok C No. 08',
                'nik' => '3201556677880004',
                'phone' => '0813-9988-7766',
                'tabel_sumber' => 'ASET',
                'tab' => 'aset',
                'perihal' => 'Peminjaman Tenda Lipat 3x3m & 20 Kursi Plastik RT',
                'nominal' => '-',
                'status' => 'pending',
                'detail_lengkap' => 'Peminjaman perlengkapan inventaris balai warga untuk keperluan syukuran aqiqah anak di halaman rumah.',
                'preview_type' => 'asset',
                'preview_title' => 'Formulir Inventaris Aset RT 05 Kode ASET-TND-02',
                'preview_meta' => 'Jadwal Pakai: 26-27 September 2026 (2 Hari)',
                'created_at' => Carbon::parse('2026-09-17 11:20:00'),
            ],
        ];

        foreach ($tickets as $t) {
            ApprovalTicket::updateOrCreate(['ticket_code' => $t['ticket_code']], $t);
        }

        // 5. Seed Official Letters (Persuratan Resmi RT)
        $letters = [
            [
                'nomor_surat' => '470/05/IX/2026',
                'ticket_code' => '#APP-2026-001',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 1,
                'nama_warga' => 'Bpk. Hendra Wijaya',
                'nik' => '3201051208940001',
                'alamat' => 'Blok B No. 12',
                'jenis_surat' => 'Surat Pengantar Pembuatan KTP & KK Baru',
                'keperluan' => 'Permohonan surat pengantar untuk pemutakhiran kartu keluarga dan KTP-el ke Kantor Kelurahan Sukamaju.',
                'status' => 'Selesai',
                'qr_token' => 'QR-RW05-470-2026',
                'tanggal_terbit' => Carbon::parse('2026-09-20'),
            ],
            [
                'nomor_surat' => '470/024/RT-05/IX/2026',
                'ticket_code' => '#APP-2026-008',
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 1,
                'nama_warga' => 'Bpk. Hendra Wijaya',
                'nik' => '3201051208940001',
                'alamat' => 'Blok B No. 12',
                'jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
                'keperluan' => 'Kelengkapan administrasi perpanjangan SKCK untuk keperluan melamar pekerjaan.',
                'status' => 'Selesai',
                'qr_token' => 'QR-RW05-470-024-2026',
                'tanggal_terbit' => Carbon::parse('2026-09-19'),
            ],
            [
                'nomor_surat' => '471/05/IX/2026',
                'ticket_code' => null,
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 2,
                'nama_warga' => 'Ibu Dewi Sartika',
                'nik' => '3201987654320001',
                'alamat' => 'Blok C No. 04',
                'jenis_surat' => 'Surat Keterangan Domisili Tempat Tinggal',
                'keperluan' => 'Kelengkapan administrasi pendaftaran sekolah anak dan perbankan.',
                'status' => 'Siap Ambil',
                'qr_token' => 'QR-RW05-471-2026',
                'tanggal_terbit' => Carbon::parse('2026-09-18'),
            ],
            [
                'nomor_surat' => '472/05/IX/2026',
                'ticket_code' => null,
                'rt_unit_id' => $rt05->id,
                'citizen_id' => 3,
                'nama_warga' => 'Bpk. Denny Pratama',
                'nik' => '3201556677880004',
                'alamat' => 'Blok C No. 08',
                'jenis_surat' => 'Surat Keterangan Usaha (SKU) Mikro Lingkungan',
                'keperluan' => 'Pengajuan KUR Mikro Usaha Kuliner Rumahan.',
                'status' => 'Selesai',
                'qr_token' => 'QR-RW05-472-2026',
                'tanggal_terbit' => Carbon::parse('2026-09-15'),
            ],
        ];

        foreach ($letters as $l) {
            OfficialLetter::updateOrCreate(['nomor_surat' => $l['nomor_surat']], $l);
        }

        // 6. Seed Cash Mutations (Buku Kas & Keuangan)
        $mutations = [
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Penerimaan Iuran Warga Batch 1 (45 KK)',
                'nominal' => 3375000,
                'nama_pihak' => 'Warga RT 05',
                'alamat_blok' => 'Semua Blok',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-05'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Penerimaan Iuran Warga Batch 2 (32 KK)',
                'nominal' => 2400000,
                'nama_pihak' => 'Warga RT 05',
                'alamat_blok' => 'Blok A & B',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-12'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Penerimaan Iuran Warga Batch 3 (15 KK via QRIS)',
                'nominal' => 1125000,
                'nama_pihak' => 'Warga RT 05',
                'alamat_blok' => 'Blok C & D',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-19'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pengeluaran',
                'kategori' => 'Keamanan',
                'perihal' => 'Honor Petugas Keamanan (3 Sekuriti Pos Ronda)',
                'nominal' => 4162500,
                'nama_pihak' => 'Bpk. Agus, Bpk. Supri & Tim',
                'alamat_blok' => 'Pos Ronda',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-02'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pengeluaran',
                'kategori' => 'Kebersihan',
                'perihal' => 'Retribusi Angkut Sampah DLH & Kebersihan Selokan',
                'nominal' => 2775000,
                'nama_pihak' => 'Petugas Truk Sampah DLH',
                'alamat_blok' => 'Wilayah RT 05',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-04'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => null,
                'tipe' => 'pengeluaran',
                'kategori' => 'Fasum & Listrik',
                'perihal' => 'Token Listrik Balai Warga & Pompa Air Bersih',
                'nominal' => 450000,
                'nama_pihak' => 'PLN Token Balai',
                'alamat_blok' => 'Balai Warga',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-08'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => '#APP-2026-002',
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Iuran Keamanan & Sampah Periode September 2026',
                'nominal' => 75000,
                'nama_pihak' => 'Ibu Dewi Sartika',
                'alamat_blok' => 'Blok C No. 04',
                'periode' => 'September 2026',
                'status' => 'pending',
                'tanggal' => Carbon::parse('2026-09-20'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => '#TX-HW-0726',
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Iuran Keamanan & Kebersihan Periode Juli 2026',
                'nominal' => 75000,
                'nama_pihak' => 'Bpk. Hendra Wijaya',
                'alamat_blok' => 'Blok B No. 12',
                'periode' => 'Juli 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-07-08'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => '#TX-HW-0826',
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Iuran Keamanan & Kebersihan Periode Agustus 2026',
                'nominal' => 75000,
                'nama_pihak' => 'Bpk. Hendra Wijaya',
                'alamat_blok' => 'Blok B No. 12',
                'periode' => 'Agustus 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-08-06'),
            ],
            [
                'rt_unit_id' => $rt05->id,
                'ticket_code' => '#TX-HW-0926',
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'perihal' => 'Iuran Keamanan & Kebersihan Periode September 2026',
                'nominal' => 75000,
                'nama_pihak' => 'Bpk. Hendra Wijaya',
                'alamat_blok' => 'Blok B No. 12',
                'periode' => 'September 2026',
                'status' => 'approved',
                'tanggal' => Carbon::parse('2026-09-05'),
            ],
        ];

        foreach ($mutations as $m) {
            CashMutation::create($m);
        }

        // 7. Seed Aspirations
        $aspirations = [
            [
                'rt_unit_id' => $rt05->id,
                'kategori' => 'Keamanan',
                'judul' => 'Penambahan Lampu Sorot LED di Ujung Blok D Menghadap Taman',
                'isi' => 'Mohon pertimbangan pengurus RT untuk menambahkan 1 lampu sorot di dekat pohon rindang taman Blok D karena jalan agak gelap saat ronda malam.',
                'nama' => 'Bpk. Denny Pratama',
                'blok' => 'Blok C No. 08',
                'is_anonim' => false,
                'status' => 'Diproses',
                'tanggapan_rt' => 'Terima kasih usulannya Pak Denny. Pembelian lampu sudah diajukan ke bendahara dan akan dipasang teknisi minggu ini.',
            ],
            [
                'rt_unit_id' => $rt05->id,
                'kategori' => 'Kebersihan',
                'judul' => 'Pengadaan Tempat Sampah Terpilah Organik & Anorganik di Balai RT',
                'isi' => 'Saran agar di sekitar pendopo disediakan tempat sampah pilah agar warga dan anak-anak terbiasa memilah sampah sejak dini.',
                'nama' => 'Ibu Nurhayati',
                'blok' => 'Blok D No. 07',
                'is_anonim' => false,
                'status' => 'Menunggu Tanggapan',
                'tanggapan_rt' => null,
            ],
            [
                'rt_unit_id' => $rt05->id,
                'kategori' => 'Fasilitas',
                'judul' => 'Pembersihan Saluran Air Sebelum Musim Hujan Tiba',
                'isi' => 'Mengingat sudah mulai masuk pancaroba, mohon saluran air utama dibersihkan kembali saat kerja bakti.',
                'nama' => 'Warga Anonim',
                'blok' => 'Kerahasiaan Terjaga',
                'is_anonim' => true,
                'status' => 'Diproses',
                'tanggapan_rt' => 'Diagendakan pada kerja bakti serentak Sabtu ini.',
            ],
        ];

        foreach ($aspirations as $a) {
            Aspiration::create($a);
        }

        // 8. Seed Master Inventaris Aset RT
        $assetItems = [
            [
                'kode_aset' => 'AST-TND-01',
                'nama_aset' => 'Tenda Lipat (3x6 Meter)',
                'kategori' => 'Tenda & Terpal',
                'total_stok' => 2,
                'stok_tersedia' => 1,
                'stok_dipinjam' => 1,
                'lokasi_penyimpanan' => 'Gudang Balai Warga RT 05',
                'kondisi' => 'Baik',
                'icon' => '🎪',
                'keterangan' => 'Rangka baja kokoh dengan terpal kanopi anti air warna biru, cocok untuk hajatan dan acara warga.',
            ],
            [
                'kode_aset' => 'AST-KRS-01',
                'nama_aset' => 'Set Kursi Lipat Warga (50 Unit)',
                'kategori' => 'Kursi & Meja',
                'total_stok' => 2, // 2 Set = 100 Unit
                'stok_tersedia' => 1,
                'stok_dipinjam' => 1,
                'lokasi_penyimpanan' => 'Gudang Balai Warga RT 05',
                'kondisi' => 'Baik',
                'icon' => '🪑',
                'keterangan' => 'Kursi lipat besi pipa chrome dengan bantalan busa hitam tebal, kapasitas beban 100 kg.',
            ],
            [
                'kode_aset' => 'AST-SND-01',
                'nama_aset' => 'Wireless Portable Sound System + 2 Mic',
                'kategori' => 'Elektronik & Audio',
                'total_stok' => 1,
                'stok_tersedia' => 0,
                'stok_dipinjam' => 1,
                'lokasi_penyimpanan' => 'Ruang Sekretariat Balai RT',
                'kondisi' => 'Baik',
                'icon' => '🔊',
                'keterangan' => 'Speaker portable troli 12 inch dengan Bluetooth, USB, baterai tahan 6 jam, dan 2 mic wireless UHF.',
            ],
            [
                'kode_aset' => 'AST-PRJ-01',
                'nama_aset' => 'Proyektor Epson & Layar Tripod 84 Inch',
                'kategori' => 'Elektronik & Audio',
                'total_stok' => 1,
                'stok_tersedia' => 1,
                'stok_dipinjam' => 0,
                'lokasi_penyimpanan' => 'Ruang Sekretariat Balai RT',
                'kondisi' => 'Baik',
                'icon' => '📽️',
                'keterangan' => 'Proyektor 3LCD resolusi Full HD dengan koneksi HDMI/VGA + tripod screen ukuran 84 inch.',
            ],
            [
                'kode_aset' => 'AST-RMPT-01',
                'nama_aset' => 'Mesin Pemotong Rumput Dorong Honda',
                'kategori' => 'Kebersihan Lingkungan',
                'total_stok' => 2,
                'stok_tersedia' => 2,
                'stok_dipinjam' => 0,
                'lokasi_penyimpanan' => 'Gudang Kebersihan Samping Pos Ronda',
                'kondisi' => 'Baik',
                'icon' => '🧹',
                'keterangan' => 'Mesin pemotong rumput mesin bensin 4-tak untuk kegiatan kerja bakti lingkungan.',
            ],
            [
                'kode_aset' => 'AST-RND-01',
                'nama_aset' => 'Paket Rompi & Senter Ronda Keamanan (6 Set)',
                'kategori' => 'Keamanan Lingkungan',
                'total_stok' => 6,
                'stok_tersedia' => 6,
                'stok_dipinjam' => 0,
                'lokasi_penyimpanan' => 'Pos Ronda Utama RT 05',
                'kondisi' => 'Baik',
                'icon' => '🔦',
                'keterangan' => 'Rompi spotlight scotlight hijau + senter SWAT rechargeable untuk petugas ronda malam.',
            ],
        ];

        foreach ($assetItems as $ai) {
            AssetItem::updateOrCreate(['kode_aset' => $ai['kode_aset']], $ai);
        }

        // 9. Seed Transaksi Peminjaman & Pengajuan Alat Aset
        $tendaItem = AssetItem::where('kode_aset', 'AST-TND-01')->first();
        $kursiItem = AssetItem::where('kode_aset', 'AST-KRS-01')->first();
        $soundItem = AssetItem::where('kode_aset', 'AST-SND-01')->first();
        $projItem = AssetItem::where('kode_aset', 'AST-PRJ-01')->first();
        $grassItem = AssetItem::where('kode_aset', 'AST-RMPT-01')->first();

        $assetLoans = [
            // A. ALAT SEDANG DIPINJAM (ACTIVE LOANS - status: 'borrowed')
            [
                'kode_pinjam' => '#PINJAM-2026-001',
                'ticket_code' => '#APP-2026-002',
                'asset_item_id' => $tendaItem ? $tendaItem->id : null,
                'nama_alat' => 'Tenda Lipat (3x6 Meter)',
                'kode_aset' => 'AST-TND-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Bpk. Agus Santoso',
                'blok_rumah' => 'Blok A No. 02',
                'kontak' => '0812-3344-5566',
                'tgl_mulai' => Carbon::parse('2026-09-20'),
                'tgl_selesai' => Carbon::parse('2026-09-22'),
                'tujuan' => 'Acara Khitanan Anak & Pengajian Keluarga di halaman rumah.',
                'status' => 'borrowed',
                'kondisi_keluar' => 'Baik & Lengkap (Kaki tenda & terpal biru mulus)',
                'approved_by' => 'Bpk. Sukardi Hartono (Ketua RT)',
                'catatan_petugas' => 'Diserahterimakan pada Sabtu pagi jam 08.00 WIB di Balai RT.',
            ],
            [
                'kode_pinjam' => '#PINJAM-2026-002',
                'ticket_code' => '#APP-2026-003',
                'asset_item_id' => $kursiItem ? $kursiItem->id : null,
                'nama_alat' => 'Set Kursi Lipat Warga (50 Unit)',
                'kode_aset' => 'AST-KRS-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Ibu Siti Rahma',
                'blok_rumah' => 'Blok B No. 05',
                'kontak' => '0857-1122-3344',
                'tgl_mulai' => Carbon::parse('2026-09-21'),
                'tgl_selesai' => Carbon::parse('2026-09-23'),
                'tujuan' => 'Pertemuan Arisan PKK RT 05 & Posyandu Balita RW 01.',
                'status' => 'borrowed',
                'kondisi_keluar' => '50 Unit Kursi Besi Lengkap & Bersih',
                'approved_by' => 'Ibu Ratna Paramitha (Sekretaris)',
                'catatan_petugas' => 'Dipakai bersama di balai dan halaman rumah B-05.',
            ],
            [
                'kode_pinjam' => '#PINJAM-2026-003',
                'ticket_code' => '#APP-2026-006',
                'asset_item_id' => $soundItem ? $soundItem->id : null,
                'nama_alat' => 'Wireless Portable Sound System + 2 Mic',
                'kode_aset' => 'AST-SND-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Ibu Siti Rahma',
                'blok_rumah' => 'Blok B No. 05',
                'kontak' => '0857-1122-3344',
                'tgl_mulai' => Carbon::parse('2026-09-21'),
                'tgl_selesai' => Carbon::parse('2026-09-21'),
                'tujuan' => 'Pengeras suara untuk senam pagi warga lansia dan PKK di Balai RT.',
                'status' => 'borrowed',
                'kondisi_keluar' => '1 Speaker + 2 Mic UHF Wireless + Adaptor Charger',
                'approved_by' => 'Ibu Ratna Paramitha (Sekretaris)',
                'catatan_petugas' => 'Jatuh tempo pengembalian sore ini jam 17.00 WIB.',
            ],

            // B. DAFTAR PENGAJUAN ALAT (PENDING APPROVAL - status: 'pending')
            [
                'kode_pinjam' => '#PINJAM-2026-004',
                'ticket_code' => '#APP-2026-005',
                'asset_item_id' => $tendaItem ? $tendaItem->id : null,
                'nama_alat' => 'Tenda Lipat (3x6 Meter)',
                'kode_aset' => 'AST-TND-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Bpk. Denny Pratama',
                'blok_rumah' => 'Blok C No. 08',
                'kontak' => '0813-9988-7766',
                'tgl_mulai' => Carbon::parse('2026-09-26'),
                'tgl_selesai' => Carbon::parse('2026-09-27'),
                'tujuan' => 'Peminjaman perlengkapan inventaris balai warga untuk keperluan syukuran aqiqah anak di halaman rumah.',
                'status' => 'pending',
                'kondisi_keluar' => 'Baik & Lengkap',
                'approved_by' => null,
                'catatan_petugas' => 'Diajukan melalui Portal Mandiri Warga. Menunggu ACC Ketua RT.',
            ],
            [
                'kode_pinjam' => '#PINJAM-2026-005',
                'ticket_code' => '#APP-2026-007',
                'asset_item_id' => $projItem ? $projItem->id : null,
                'nama_alat' => 'Proyektor Epson & Layar Tripod 84 Inch',
                'kode_aset' => 'AST-PRJ-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Bpk. Hendra Wijaya',
                'blok_rumah' => 'Blok B No. 12',
                'kontak' => '0812-3456-7890',
                'tgl_mulai' => Carbon::parse('2026-09-24'),
                'tgl_selesai' => Carbon::parse('2026-09-24'),
                'tujuan' => 'Nonton bersama (Nobar) pertandingan Timnas Indonesia di Balai RT bersama pemuda dan bapak-bapak warga.',
                'status' => 'pending',
                'kondisi_keluar' => 'Baik & Lengkap',
                'approved_by' => null,
                'catatan_petugas' => 'Kegiatan kepemudaan non-komersial. Bebas biaya peminjaman.',
            ],

            // C. RIWAYAT PENGEMBALIAN (SELESAI - status: 'returned')
            [
                'kode_pinjam' => '#PINJAM-2026-000',
                'ticket_code' => '#APP-2026-000',
                'asset_item_id' => $grassItem ? $grassItem->id : null,
                'nama_alat' => 'Mesin Pemotong Rumput Dorong Honda',
                'kode_aset' => 'AST-RMPT-01',
                'jumlah' => 1,
                'nama_peminjam' => 'Bpk. Bambang Pamungkas',
                'blok_rumah' => 'Blok D No. 03',
                'kontak' => '0813-7788-9900',
                'tgl_mulai' => Carbon::parse('2026-09-14'),
                'tgl_selesai' => Carbon::parse('2026-09-15'),
                'tujuan' => 'Kerja bakti pembersihan rumput liar di bahu jalan Blok D dan taman anak.',
                'status' => 'returned',
                'kondisi_keluar' => 'Baik & Bersih',
                'kondisi_kembali' => 'Selesai & Bersih, pisau potong utuh, tangki bensin terisi normal',
                'tgl_kembali' => Carbon::parse('2026-09-15'),
                'approved_by' => 'Bpk. Sukardi Hartono (Ketua RT)',
                'catatan_petugas' => 'Telah diperiksa kembali oleh pengurus dan disimpan rapi di gudang.',
            ],
        ];

        foreach ($assetLoans as $al) {
            AssetLoan::updateOrCreate(['kode_pinjam' => $al['kode_pinjam']], $al);
        }
    }
}
