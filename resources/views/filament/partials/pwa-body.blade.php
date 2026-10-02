<!-- PWA Service Worker Registration for Filament CMS -->
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js')
        .then(function (reg) {
          console.log('[PWA CMS] Service Worker registered. Scope:', reg.scope);
        })
        .catch(function (err) {
          console.warn('[PWA CMS] Service Worker registration failed:', err);
        });
    });
  }
</script>
