<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Keaslian Surat Pengantar RT - RuangWarga</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            primary: {
              50: '#eff6ff',
              100: '#dbeafe',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800 flex flex-col justify-between p-4 sm:p-8">

  <!-- Header Brand -->
  <div class="max-w-xl mx-auto w-full flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-md shadow-primary-500/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div>
        <div class="font-extrabold text-slate-900 leading-none text-base">Ruang<span class="text-primary-600">Warga</span></div>
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validasi Dokumen Publik</span>
      </div>
    </div>
    <a href="{{ url('/') }}" class="text-xs font-bold text-slate-600 hover:text-primary-600 transition-colors">
      ← Portal Warga
    </a>
  </div>

  <!-- Main Card -->
  <div class="max-w-xl mx-auto w-full bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden mb-8">
    <!-- Header Verification Status -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 text-white text-center">
      <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/30 shadow-inner">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <h2 class="text-xl font-black tracking-tight">DOKUMEN ASLI TERVERIFIKASI</h2>
      <p class="text-xs text-emerald-100 mt-1">Tercatat resmi dalam Buku Register Administrasi RT 05 / RW 01 Harmoni Residence</p>
    </div>

    <!-- Details Body -->
    <div class="p-6 sm:p-8 space-y-4 text-sm">
      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nomor Surat Resmi</div>
        <div class="text-base font-extrabold text-slate-900 font-mono mt-0.5">{{ $letter['nomor_surat'] }}</div>
        <div class="text-xs font-bold text-primary-600 mt-0.5">{{ $letter['perihal'] }}</div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="border border-slate-200/80 p-3.5 rounded-2xl bg-slate-50/50">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Pemohon</span>
          <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $letter['nama'] }}</span>
        </div>
        <div class="border border-slate-200/80 p-3.5 rounded-2xl bg-slate-50/50">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">NIK Pemohon</span>
          <span class="font-mono text-xs font-bold text-slate-900 mt-0.5 block">{{ $letter['nik'] }}</span>
        </div>
        <div class="border border-slate-200/80 p-3.5 rounded-2xl bg-slate-50/50">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Penerbitan</span>
          <span class="font-semibold text-slate-900 text-xs mt-0.5 block">{{ $letter['tanggal_terbit'] }}</span>
        </div>
        <div class="border border-slate-200/80 p-3.5 rounded-2xl bg-slate-50/50">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Penandatangan Resmi</span>
          <span class="font-semibold text-slate-900 text-xs mt-0.5 block">{{ $letter['ketua_rt'] }} (Ketua RT)</span>
        </div>
      </div>

      <div class="border border-slate-200/80 p-4 rounded-2xl bg-slate-50/50">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Keperluan / Keterangan</span>
        <span class="text-xs text-slate-700 mt-1 block leading-relaxed font-medium">{{ $letter['keperluan'] }}</span>
      </div>

      <div class="pt-3 flex flex-col sm:flex-row items-center gap-3">
        <a 
          href="{{ route('surat.pdf.download', $letter['id']) }}" 
          target="_blank"
          class="w-full sm:flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs rounded-xl shadow-md transition-all text-center flex items-center justify-center gap-2 cursor-pointer active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Unduh Salinan PDF Resmi</span>
        </a>
        <a 
          href="{{ route('cms.citizens') }}" 
          class="w-full sm:w-auto px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all text-center"
        >
          Kembali ke CMS
        </a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="text-center text-xs text-slate-400">
    Dokumen ini dilindungi tanda tangan & cap digital resmi Rukun Tetangga 05 / RW 01 Harmoni Residence. Platform RuangWarga © 2026.
  </div>

</body>
</html>
