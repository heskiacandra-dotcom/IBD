<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <title>Hubungkan Akun Google • Smart RT</title>
  
  <meta name="theme-color" content="#2563EB">
  <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon-512x512.svg') }}">

  <!-- Modern Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    :root {
      color-scheme: light only;
    }
    body {
      background-color: #F8FAFC !important;
      color: #0F172A !important;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    input::-ms-reveal, input::-ms-clear {
      display: none !important;
    }
  </style>
</head>
<body class="min-h-screen bg-[#F8FAFC] flex items-center justify-center p-4 antialiased selection:bg-blue-100 selection:text-blue-900">

  <div class="max-w-md w-full bg-white rounded-2xl border border-[#E2E8F0] shadow-sm p-8 transition-all">

    <!-- Header Section -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 mb-3 shadow-xs">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
      </div>
      <h1 class="font-bold text-slate-900 text-xl tracking-tight">Hubungkan Akun Google</h1>
      <p class="text-xs text-slate-500 mt-1">Tautkan akun Google Anda ke data warga atau pengurus RT</p>
    </div>

    <!-- Google Profile Badge -->
    <div class="mb-6 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center gap-3">
      @if(!empty($pending['avatar']))
        <img src="{{ $pending['avatar'] }}" alt="{{ $pending['name'] ?? 'Google User' }}" class="w-10 h-10 rounded-full border border-slate-200 shrink-0 object-cover" />
      @else
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
          {{ substr($pending['name'] ?? 'G', 0, 1) }}
        </div>
      @endif
      <div class="min-w-0 flex-1">
        <div class="font-semibold text-xs text-slate-900 truncate">{{ $pending['name'] ?? 'Pengguna Google' }}</div>
        <div class="text-[11px] text-slate-500 truncate flex items-center gap-1.5 mt-0.5">
          <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.26-2.09 3.67-5.17 3.67-9.15z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.28v3.15C3.3 21.36 7.37 24 12 24z"/>
            <path fill="#FBBC05" d="M5.27 14.24A7.18 7.18 0 0 1 4.9 12c0-.78.14-1.54.37-2.24V6.61H1.28A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.28 5.39l3.99-3.15z"/>
            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.3 2.64 1.28 6.61l3.99 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/>
          </svg>
          <span class="truncate">{{ $pending['email'] ?? '-' }}</span>
        </div>
      </div>
      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
        Terverifikasi
      </span>
    </div>

    <!-- Error Alert -->
    @error('identity')
      <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16" stroke-width="2"/>
        </svg>
        <span>{{ $message }}</span>
      </div>
    @enderror

    <!-- Instruction -->
    <p class="text-xs text-slate-600 mb-4 leading-relaxed">
      Untuk menghubungkan akun Google ini secara permanen, masukkan <strong>NIK atau Username</strong> serta kata sandi akun Smart RT Anda sekali saja:
    </p>

    <!-- Verification Form -->
    <form action="{{ route('auth.google.link.post') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="identity" class="block text-xs font-semibold text-slate-700 mb-1.5">
          NIK atau Username
        </label>
        <input 
          id="identity"
          name="identity"
          type="text" 
          value="{{ old('identity') }}"
          placeholder="Contoh: 3201051208940001 atau hendra_w" 
          class="w-full border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 rounded-xl px-3.5 py-2.5 text-sm bg-white text-slate-900 placeholder:text-slate-400 focus:outline-none transition-all shadow-xs"
          required
        />
      </div>

      <div>
        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
          Kata Sandi
        </label>
        <input 
          id="password"
          name="password"
          type="password" 
          placeholder="Masukkan kata sandi akun Anda" 
          class="w-full border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 rounded-xl px-3.5 py-2.5 text-sm bg-white text-slate-900 placeholder:text-slate-400 focus:outline-none transition-all shadow-xs"
          required
        />
      </div>

      <div class="pt-2">
        <button 
          type="submit" 
          class="w-full h-11 px-4 bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white font-semibold text-sm rounded-xl transition-all flex items-center justify-center cursor-pointer shadow-xs"
        >
          Hubungkan & Masuk ke Sistem
        </button>
      </div>

      <!-- Action Options -->
      <div class="text-center pt-3 border-t border-slate-100 space-y-2">
        <p class="text-xs text-slate-500">
          Belum pernah terdaftar sebagai warga? 
          <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar Akun Baru</a>
        </p>
        <div>
          <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
            Kembali ke Halaman Masuk
          </a>
        </div>
      </div>
    </form>

  </div>

</body>
</html>
