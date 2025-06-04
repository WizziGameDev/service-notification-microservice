#!/bin/bash

# Cek Docker daemon aktif
echo "🔍 Checking Docker daemon..."
until docker info >/dev/null 2>&1; do
  echo "⌛ Waiting for Docker daemon to start..."
  sleep 3
done

echo "✅ Docker daemon is running."

# Jalankan docker-compose
echo "🚀 Starting Docker Compose for Notification..."
docker-compose up -d --build

# Tunggu MySQL ready
echo "⏳ Waiting for MySQL (mysql-notification) to be healthy..."
until docker exec mysql-notification mysqladmin ping -h localhost --silent; do
    echo "⌛ Waiting for MySQL to be ready..."
    sleep 5
done

# Tunggu Laravel ready (optional, untuk seed jika perlu)
sleep 10

# Tambahkan baris berikut jika kamu perlu seed ulang (opsional)
# echo "🌱 Running Laravel seed..."
# docker exec service-notification php artisan migrate:fresh --seed

echo "🎉 Notification service, MySQL, and worker are running (queue:work is auto-started in docker-compose)!"
