<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Smart RT - Portal Layanan Mandiri & Transparansi Lingkungan RT/RW</title>
  <meta name="description" content="Satu wadah digital untuk kemudahan warga: Pengajuan surat pengantar online, pembayaran iuran kas RT, peminjaman aset, agenda kegiatan, transparansi keuangan, dan kotak aspirasi terbuka.">
  
  <!-- PWA & Mobile Web App Meta Tags -->
  <meta name="theme-color" content="#2563EB">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="RuangWarga">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon-512x512.svg') }}">
  <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  @stack('styles')
</head>
<body class="has-mobile-nav">

  <!-- Sticky Top Navbar -->
  @include('portal.partials.navbar')

  <!-- Main Content -->
  <main>
    @yield('content')
  </main>

  <!-- Site Footer & Emergency Hotlines -->
  @include('portal.partials.footer')

  <!-- Native App-Like Mobile Bottom Navigation Bar -->
  @include('portal.partials.bottom-nav')

  <!-- Interactive Modals -->
  @include('portal.partials.modals')

  <!-- Toast Notification Container -->
  <div class="toast-container" id="toastContainer"></div>

  <!-- Midtrans Snap Payment Gateway JS -->
  <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>

  <!-- PWA Service Worker Registration -->
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then((reg) => {
            console.log('[PWA] Service Worker registered successfully! Scope:', reg.scope);
          })
          .catch((err) => {
            console.warn('[PWA] Service Worker registration failed:', err);
          });
      });
    }
  </script>
  @stack('scripts')
</body>
</html>
