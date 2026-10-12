#!/bin/bash
set -e

echo "==> Memulai inisialisasi Smart RT di Render..."

# 1. Sesuaikan Port Apache dengan variabel $PORT dari Render (biasanya port dinamis atau 10000)
if [ -n "$PORT" ]; then
    echo "==> Mengatur Apache untuk mendengarkan port: $PORT"
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# 2. Buat symlink storage ke public jika belum ada
echo "==> Menghubungkan storage symlink..."
php artisan storage:link --force || true

# 3. Optimasi Cache Laravel untuk performa produksi
echo "==> Melakukan caching konfigurasi dan view..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 4. Jalankan Migrasi Database secara otomatis jika variabel RUN_MIGRATIONS diatur ke true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "==> Menjalankan migrasi database..."
    php artisan migrate --force || true

    if [ "$RUN_SEEDER" = "true" ]; then
        echo "==> Menjalankan database seeder awal..."
        php artisan db:seed --class=ErdRuangWargaSeeder --force || true
    fi
fi

echo "==> Smart RT siap berjalan! Menjalankan Apache server..."
exec apache2-foreground
