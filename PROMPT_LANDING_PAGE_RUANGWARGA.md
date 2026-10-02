# Spesifikasi UI & Prompt Landing Page / Portal Warga: "RuangWarga"
**Karakteristik Desain:**
- Target Pengguna: Warga Lingkungan (Akses Non-CMS / Public Citizen Portal)
- Tema: Pure Light Mode (Modern, Bersih, Hangat, Aksesibel)
- Tone of Voice: Komunikatif, Informatif, Transparan, Guyub & Solutif

---

## 1. Arsitektur Informasi & Komponen Halaman (Landing Page Warga)

### A. Navigation Header (Sticky Top Navbar)
- **Brand Logo & Name:** Icon Komunitas/Rumah + Text "RuangWarga"
- **Nav Links:**
  - Beranda
  - Layanan Mandiri (Surat, Iuran, Peminjaman Aset)
  - Kalender Kegiatan Lingkungan
  - Suara Warga (Aspirasi)
  - Transparansi Kas
- **CTA Buttons:**
  - Tombol "Masuk Akun Warga" (Secondary Button - Outlined Blue)
  - Tombol "Portal Pengurus RT (CMS)" (Small Subtle Link ke Login CMS Pengurus)

---

### B. Hero Section (Sambutan & Quick Service Hub)
- **Headline:** "Satu Wadah Digital untuk Kemudahan Warga dan Kebaikan Bersama"
- **Subheadline:** "Ajukan surat pengantar, bayar iuran bulanan, pinjam perlengkapan warga, dan pantau kegiatan lingkungan tanpa perlu antre di rumah pengurus."
- **Quick Action Grid (4 Kartu Layanan Cepat / Fast Action Cards):**
  1. 📄 **Pengajuan Surat Pengantar** (Surat Domisili, KTP, SKCK) $
ightarrow$ CTA: "Buat Surat"
  2. 💳 **Bayar Iuran Bulanan** (Cek tagihan & upload bukti bayar) $
ightarrow$ CTA: "Bayar Iuran"
  3. 📦 **Peminjaman Aset/Alat** (Tenda, kursi, sound system warga) $
ightarrow$ CTA: "Pinjam Alat"
  4. 📢 **Sampaikan Aspirasi** (Kritik, saran, & aduan fasilitas) $
ightarrow$ CTA: "Kirim Aspirasi"

---

### C. Live Announcement & Agenda Banner (Pengumuman & Kegiatan)
- **Agenda Mendatang (Upcoming Events):**
  - Card Event: Contoh "Kerja Bakti Bersama & Fogging", "Senam Sehat Minggu Pagi", "Rapat Koordinasi RT".
  - Informasi: Tanggal, Jam, Lokasi, dan Status Penyelenggara.
  - Button "Ajukan Usulan Kegiatan Warga" (Membuka modal pengajuan kegiatan oleh warga).

---

### D. Transparansi Kas & Finansial RT (Ringkasan Kas Masuk & Keluar)
- Widget ringkas untuk keterbukaan publik (Read-only data dari Laporan Keuangan):
  - Total Saldo Kas Terakhir
  - Pemasukan Kas Bulan Berjalan
  - Pengeluaran Operasional Terakhir
  - Catatan: *"Dikelola dan diverifikasi oleh Bendahara RT"*

---

### E. Aspirasi Warga & Tanggapan Terbuka
- Feed daftar aspirasi yang sudah direspons oleh Ketua RT:
  - Kartu aduan/saran anonim/bernama $
ightarrow$ Tanggapan resmi dari Ketua RT dengan badge status `Dijawab / Ditindaklanjuti`.

---

### F. Footer
- Kontak darurat lingkungan (Nomor Ketua RT, Babinsa, Bhabinkamtibmas, Damkar).
- Alamat Sekretariat RT / Balai Warga.
- Copyright RuangWarga 2026.


---

## 2. Prompt Siap Pakai untuk Google Stitch / AI UI Tool

```text
Design a modern, friendly, and clean Citizen Landing Page / Self-Service Portal for a neighborhood management system called "RuangWarga" in Pure Light Mode (Strictly no dark mode, no heavy dark cards).

Design Guidelines & Style:
- Background: Ultra-clean soft off-white (#F8FAFC), card containers pure white (#FFFFFF) with delicate soft shadows and crisp borders (#E2E8F0).
- Accent Colors: Vibrant Friendly Blue (#2563EB), Emerald Green (#10B981 for financial/approved badges), Warm Amber (#F59E0B for notifications).
- Typography: Clear and highly readable sans-serif (Plus Jakarta Sans / Inter).
- Layout: 12-column desktop responsive grid, spacious padding, rounded corners (16px).

Page Structure:
1. Top Navigation Bar:
   - Logo: RuangWarga with a modern geometric house/community badge.
   - Links: Beranda, Layanan Warga, Kegiatan Lingkungan, Transparansi Kas, Aspirasi.
   - Action: "Masuk Akun Warga" (Solid primary button) & subtle icon link "Akses CMS Pengurus".

2. Hero Section:
   - Engaging welcoming title: "Layanan Mandiri Warga RT, Cepat & Terbuka".
   - Subtitle explaining that citizens can request letters, pay fees, borrow neighborhood equipment, and submit suggestions online.
   - 4 Quick-Action Feature Cards with soft pastel icons:
     * Card 1: "Pengajuan Surat Online" (Layanan surat pengantar domisili/keterangan)
     * Card 2: "Pembayaran Iuran Kas RT" (Cek tagihan & konfirmasi transfer)
     * Card 3: "Peminjaman Inventaris RT" (Pinjam tenda, kursi, proyektor, sound)
     * Card 4: "Kotak Aspirasi Warga" (Sampaikan kritik, saran, & usulan lingkungan)

3. Live Community Section (Kegiatan & Pengumuman):
   - Clean calendar cards displaying upcoming community activities (e.g. Kerja Bakti, Rapat Warga, Posyandu).
   - A distinct button: "Ajukan Kegiatan Warga".

4. Transparency Kas Widget:
   - Minimalist financial summary banner showing Total Saldo Kas RT, Pemasukan Terkumpul, and Pengeluaran Bulan Ini (read-only for citizens).

5. Suara Warga & Respons Pengurus:
   - Feed of recent public suggestions with official response cards from Ketua RT.

6. Modern Footer:
   - Emergency contact numbers (Ketua RT, Keamanan, Pos Ronda, Ambulans), Balai Pertemuan address, and copyright.

```