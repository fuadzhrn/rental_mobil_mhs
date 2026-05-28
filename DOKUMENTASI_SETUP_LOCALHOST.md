# Dokumentasi Setup WebRental dari File RAR ke Localhost

Dokumen ini menjelaskan cara menjalankan project WebRental di komputer lokal, dimulai dari file RAR yang sudah diterima sampai website aktif lewat `php artisan serve`.

## Kebutuhan Awal

Pastikan perangkat Anda sudah memiliki:

- PHP
- Composer
- Node.js dan NPM
- MySQL atau MariaDB
- Web server lokal seperti Laragon

## Langkah Setup

### 1. Ekstrak file RAR

Jika project dikirim dalam bentuk file `.rar`, lakukan ekstrak terlebih dahulu.

Contoh langkah:

- klik kanan file `.rar`
- pilih `Extract Here` atau `Extract to...`
- pindahkan hasil ekstrak ke folder kerja, misalnya folder Laragon `www`

Setelah diekstrak, pastikan folder project berisi file seperti:

- `artisan`
- `composer.json`
- `package.json`
- folder `app`, `routes`, `resources`, `database`

### 2. Install dependency

Jalankan perintah berikut di folder project:

```bash
composer install
npm install
```

### 3. Siapkan file environment

- Duplikat file `.env.example` menjadi `.env`
- Atur konfigurasi database di dalam file `.env`

Contoh konfigurasi database:

```env
DB_DATABASE=webrental
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Jalankan migration dan seeder

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build asset frontend

Jika ingin build sekali:

```bash
npm run build
```

Jika ingin mode development:

```bash
npm run dev
```

### 7. Jalankan project

```bash
php artisan serve
```

### 8. Buka di browser

```text
http://127.0.0.1:8000
```

## Jika Ada Perubahan Tidak Muncul

Jika tampilan belum berubah atau cache masih tersimpan, jalankan:

```bash
php artisan view:clear
php artisan cache:clear
```

## Ringkasan Cepat

Urutan paling cepat untuk setup dari file RAR adalah:

1. ekstrak file `.rar`
2. pindahkan folder project ke lokasi kerja
3. jalankan `composer install`
4. jalankan `npm install`
5. copy `.env.example` ke `.env`
6. isi database di `.env`
7. jalankan `php artisan key:generate`
8. jalankan `php artisan migrate`
9. jalankan `php artisan db:seed`
10. jalankan `npm run build` atau `npm run dev`
11. jalankan `php artisan serve`

Setelah itu website bisa dibuka di localhost.
