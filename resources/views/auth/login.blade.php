<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <title>Masuk • Smart RT</title>
  
  <!-- PWA & Mobile Web App Meta Tags -->
  <meta name="theme-color" content="#2563EB">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="Smart RT">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon-512x512.svg') }}">
  <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

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
    /* Sembunyikan icon mata default dari Microsoft Edge / IE agar tidak bertumpuk */
    input::-ms-reveal,
    input::-ms-clear {
      display: none !important;
    }
    [x-cloak] {
      display: none !important;
    }
  </style>

  @livewireStyles
</head>
<body class="min-h-screen bg-[#F8FAFC] flex items-center justify-center p-4 antialiased selection:bg-blue-100 selection:text-blue-900">

  @livewire('auth.login-sso')

  @livewireScripts
</body>
</html>
