Dokumentasi API Lengkap

Dokumentasi ini adalah panduan komprehensif untuk mengintegrasikan aplikasi Anda dengan layanan backend Apextrack. Semua endpoint dirancang untuk efisiensi dan kemudahan penggunaan.

Basis URL

Semua permintaan API harus ditujukan ke URL dasar berikut:

https://www3.apextrack.site/api

Autentikasi Menggunakan Sanctum Bearer Token

Sebagian besar endpoint memerlukan autentikasi. Setelah berhasil login, Anda akan menerima token yang harus disertakan di header permintaan untuk semua rute yang dilindungi.

Format Header Autentikasi:

Authorization: Bearer <token_yang_diterima_saat_login>


1. Modul Akun

1.1. Registrasi Akun Baru

Endpoint ini digunakan untuk mendaftarkan pengguna baru.

URL: /register

Metode: POST

Autentikasi: Tidak diperlukan.

Body Permintaan: application/json

Parameter

Tipe

Wajib

Deskripsi

name

string

Ya

Nama pengguna.

email

string

Ya

Alamat email yang valid.

password

string

Ya

Kata sandi (minimal 8 karakter).

password_confirmation

string

Ya

Konfirmasi kata sandi.

Contoh Permintaan (cURL):

curl --location 'https://www3.apextrack.site/api/register' \
--header 'Content-Type: application/json' \
--data '{
    "name": "nama_pengguna",
    "email": "email_anda@contoh.com",
    "password": "password123",
    "password_confirmation": "password123"
}'


Contoh Respons Sukses (Status: 201 Created):

{
  "message": "Pendaftaran berhasil, silakan verifikasi email Anda."
}


Contoh Respons Gagal (Status: 422 Unprocessable Entity):

{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ],
    "password": [
      "The password confirmation does not match."
    ]
  }
}


1.2. Login Pengguna

Mengotentikasi pengguna dan mengembalikan token akses untuk sesi.

URL: /login

Metode: POST

Autentikasi: Tidak diperlukan.

Body Permintaan: application/json

Parameter

Tipe

Wajib

Deskripsi

email

string

Ya

Alamat email pengguna.

password

string

Ya

Kata sandi pengguna.

device_name

string

Ya

Nama perangkat (misalnya, "mobile_app" atau "web_browser").

Contoh Permintaan (cURL):

curl --location 'https://www3.apextrack.site/api/login' \
--header 'Content-Type: application/json' \
--data '{
    "email": "email_anda@contoh.com",
    "password": "password123",
    "device_name": "mobile_app"
}'


Contoh Respons Sukses (Status: 200 OK):

{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}


1.3. Logout

Menghapus token autentikasi pengguna saat ini.

URL: /logout

Metode: POST

Autentikasi: Wajib (Bearer Token).

Contoh Permintaan (cURL):

curl --location 'https://www3.apextrack.site/api/logout' \
--header 'Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'


Contoh Respons Sukses (Status: 200 OK):

{
  "message": "Berhasil logout."
}


2. Modul Verifikasi Email

Endpoint ini digunakan untuk memverifikasi alamat email pengguna.

2.1. Verifikasi Kode

URL: /email/verify

Metode: POST

Autentikasi: Wajib (Bearer Token).

Body Permintaan: application/json

Parameter

Tipe

Wajib

Deskripsi

code

string

Ya

Kode verifikasi 6 digit yang dikirim ke email.

Contoh Respons Sukses (Status: 200 OK):

{
  "message": "Email Anda berhasil diverifikasi!",
  "redirect": "/dashboard"
}


Contoh Respons Gagal (Status: 422 Unprocessable Entity):

{
  "message": "Kode verifikasi tidak valid atau sudah kadaluarsa."
}


2.2. Kirim Ulang Kode

URL: /email/resend

Metode: POST

Autentikasi: Wajib (Bearer Token).

Contoh Respons Sukses (Status: 200 OK):

{
  "message": "Kode verifikasi baru telah dikirim ke email Anda."
}


Contoh Respons Gagal (Status: 429 Too Many Requests):

{
  "message": "Harap tunggu 1 menit sebelum mencoba mengirim ulang kode.",
  "retry_after": 60
}


3. Modul Dashboard & Laporan

3.1. Mendapatkan Data Pengguna

Mengambil detail profil pengguna yang terautentikasi.

URL: /user

Metode: GET

Autentikasi: Wajib (Bearer Token).

Contoh Respons Sukses (Status: 200 OK):

{
  "id": 1,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "email_verified_at": "2024-08-17T08:05:00.000000Z",
  "account_status": "active"
}


3.2. Mendapatkan Semua Statistik Dashboard

Endpoint tunggal ini mengembalikan semua data dashboard yang diperlukan (ringkasan, laporan, klik, dan leads terbaru) dalam satu respons yang efisien.

URL: /dashboard-data

Metode: GET

Autentikasi: Wajib (Bearer Token).

Contoh Respons Sukses (Status: 200 OK):

{
  "summary": {
    "liveClicksLast30Seconds": 15,
    "clicksToday": 1500,
    "totalLeads": 250,
    "totalPayout": 1250.75
  },
  "report": [
    {
      "sub_id": "user123",
      "clicks": 500,
      "leads": 100,
      "approved_leads": 80,
      "total_payout": 400.50,
      "cr": 16.0
    }
  ],
  "recent_clicks": [
    {
      "clickid": "c_20240817080000",
      "country_code": "ID",
      "device_type": "mobile",
      "created_at": "2024-08-17T08:00:00.000000Z"
    }
  ],
  "recent_leads": [
    {
      "status": "approved",
      "payout": 5.00,
      "created_at": "2024-08-17T08:05:00.000000Z",
      "sub_id": "user123",
      "country_code": "ID"
    }
  ]
}


4. Modul Notifikasi Pembayaran & Postback (Rute Publik)

Rute-rute ini tidak memerlukan otentikasi Bearer Token karena digunakan untuk komunikasi antar server.

4.2. URL Postback Konversi

Endpoint untuk menerima postback konversi dari layanan lain.

URL: /postback/conversion

Metode: GET atau POST

Autentikasi: Tidak diperlukan.