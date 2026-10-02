<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi Akun Warga Baru • RuangWarga</title>
  
  <!-- Modern Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#dbeafe',
              500: '#3b82f6',
              600: '#2563eb', // Royal Blue
              700: '#1d4ed8',
            }
          }
        }
      }
    }
  </script>

  <style>
    /* Pure Light Mode Strict Overrides */
    :root {
      color-scheme: light only;
    }
    body {
      background-color: #F8FAFC !important;
      color: #0F172A !important;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
      animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
  </style>

  @livewireStyles
</head>
<body class="h-full flex flex-col justify-between p-4 sm:p-6 antialiased selection:bg-blue-100 selection:text-blue-900">

  <!-- Top Nav Bar Minimal (Portal Warga & Login Link) -->
  <header class="w-full max-w-5xl mx-auto flex items-center justify-between py-2">
    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1.5 group">
      <svg class="w-4 h-4 text-slate-400 group-hover:-translate-x-1 group-hover:text-blue-600 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
      <span>Kembali ke Halaman Masuk</span>
    </a>

    <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
      <span>Portal Pendaftaran Warga</span>
    </div>
  </header>

  <!-- Centered Register Content -->
  <main class="flex-1 flex items-center justify-center py-6 sm:py-10">
    @livewire('auth.register-warga')
  </main>

  <!-- Bottom Copyright -->
  <footer class="w-full max-w-5xl mx-auto text-center py-3 text-xs text-slate-400">
    <div class="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-4 text-[11px]">
      <a href="{{ url('/') }}" class="hover:underline">Beranda RuangWarga</a>
      <span>•</span>
      <a href="{{ route('login') }}" class="hover:underline">Masuk Akun</a>
      <span>•</span>
      <a href="{{ route('cms.dashboard') }}" class="hover:underline">Panel Pengurus RT</a>
      <span>•</span>
      <span>Privasi & Keamanan Data Warga</span>
    </div>
  </footer>

  @livewireScripts
</body>
</html>
