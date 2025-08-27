Petunjuk Instalasi ApexTrack Lite
Berikut adalah panduan langkah demi langkah untuk menginstal ApexTrack Lite di layanan hosting web Anda.

Persyaratan
Server Web: Apache atau Nginx

PHP: Versi 8.0 atau lebih tinggi

Git: Untuk kloning repositori (opsional, bisa juga dengan mengunggah file ZIP)

Akses SSH atau FTP/File Manager ke server hosting Anda

Langkah 1: Unggah File Proyek
Ada dua cara untuk mengunggah file ke hosting Anda:

Opsi A: Kloning dengan Git (Direkomendasikan)
Akses server Anda melalui SSH dan navigasikan ke direktori web root (biasanya public_html, htdocs, atau www).

Kloning repositori proyek dari GitHub:

Bash

git clone https://github.com/apextrack/ApexTrack-Lite.git .

(Tanda titik . di akhir perintah akan mengkloning file langsung ke dalam folder saat ini, bukan membuat subfolder baru.)

Opsi B: Unggah File ZIP
Unduh repositori ApexTrack Lite sebagai file ZIP dari halaman GitHub-nya.

Ekstrak file ZIP di komputer Anda.

Unggah semua file dan folder ke direktori web root hosting Anda menggunakan FTP (FileZilla, Cyberduck) atau File Manager yang disediakan oleh cPanel/hosting Anda.

Langkah 2: Selesaikan Instalasi
Penting: Aplikasi ini dikonfigurasi untuk terhubung ke API eksternal yang sudah ada. Karena itu, jangan ubah file config.php. File ini sudah berisi URL API yang benar.

Proyek Anda sekarang seharusnya sudah dapat diakses. Buka browser dan kunjungi domain atau sub-domain Anda. Anda akan melihat halaman login yang terhubung ke API eksternal.
