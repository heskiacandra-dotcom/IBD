<!-- PWA Meta & Icons for Filament CMS -->
<meta name="theme-color" content="#2563EB">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Smart RT CMS">
<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon-512x512.svg') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

<!-- Mobile UX & Touch Optimization Styles for CMS -->
<style>
  /* Ensure smooth horizontal scroll on mobile for Filament tables */
  @media (max-width: 768px) {
    .fi-ta-content {
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch;
    }
    
    /* Touch target comfort */
    .fi-btn, .fi-icon-btn {
      min-height: 38px;
    }

    /* Prevent auto zoom on iOS inputs */
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="number"],
    select,
    textarea {
      font-size: 16px !important;
    }

    /* Responsive modal adjustments */
    .fi-modal-window {
      max-width: 95vw !important;
      margin: 10px auto !important;
    }

    /* Compact stats widget on mobile */
    .fi-wi-stats-overview-stat {
      padding: 14px !important;
    }
  }
</style>
