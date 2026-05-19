# Realtime Chat App

Aplikasi chat sederhana dengan Laravel. User bisa daftar, login, buat room chat, dan kirim pesan.

## Fitur

- Login & register
- Buat room chat (private / group)
- Kirim & baca pesan
- Lihat status user online/offline

## Kebutuhan

- Laragon (PHP 8.2+, MySQL)
- Composer

## Cara Install

1. Letakkan project di `D:\laragon\www\project_realtime_chat`
2. Buka **Terminal Laragon**
3. Jalankan:

```bash
cd D:\laragon\www\project_realtime_chat
composer install
copy .env.example .env
php artisan key:generate
```

4. Double-click **`setup.bat`** (buat database + migrasi + data demo)

## Cara Menjalankan

1. Start **Apache** & **MySQL** di Laragon
2. Buka: **http://project_realtime_chat.test**

Atau double-click **`serve.bat`**, lalu buka: **http://127.0.0.1:8000**


## Cara Pakai

1. Login
2. Klik **+ Buat Room Baru**
3. Pilih member, lalu buat room
4. Klik room di sidebar
5. Ketik pesan → **Kirim**

## Database

Nama database: `realtime_chatapp`

Tabel utama:
- `users` — data akun
- `chat_rooms` — ruang chat
- `chat_room_user` — member room
- `messages` — isi pesan

## Masalah Umum

| Error | Solusi |
|-------|--------|
| `php` tidak dikenali | Pakai Terminal Laragon, bukan CMD biasa |
| Database error | Jalankan `setup.bat` |
| Halaman tidak muncul | Pastikan Apache & MySQL sudah Start |

## Perintah Penting

```bash
php artisan migrate        # Buat tabel database
php artisan db:seed        # Isi data demo
php artisan serve          # Jalankan server lokal
```
