<?php
// update.php
// Ganti dengan URL file ZIP repositori Anda
$zipUrl = 'https://github.com/apextrack/ApexTrack-Lite/archive/refs/heads/master.zip';
$zipFile = 'update.zip';

// Pastikan kelas ZipArchive tersedia
if (!class_exists('ZipArchive')) {
    die("Error: PHP ZipArchive class is not available.\n");
}

// Pastikan direktori bisa ditulis
if (!is_writable('.')) {
    die("Error: The current directory is not writable.\n");
}

echo "Memulai proses pembaruan...\n";

// Mengunduh file ZIP dari GitHub
echo "Mengunduh pembaruan dari GitHub...\n";
$zipContent = @file_get_contents($zipUrl);

if ($zipContent === false) {
    die("Error: Gagal mengunduh file ZIP dari URL: {$zipUrl}\n");
}

if (file_put_contents($zipFile, $zipContent) === false) {
    die("Error: Gagal menyimpan file ZIP di server.\n");
}

echo "Berhasil mengunduh file ZIP.\n";

// Mengekstrak file ZIP
echo "Mengekstrak file...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    $zip->extractTo('.');
    $zip->close();
    
    // Hapus file ZIP setelah diekstrak
    unlink($zipFile);

    echo "Pembaruan berhasil diekstrak!\n";

    // Pindahkan file dari subfolder
    // GitHub menaruh konten di subfolder, jadi kita perlu memindahkannya.
    $sourceDir = 'ApexTrack-Lite-master/';
    $destinationDir = './';

    // Pindahkan semua konten
    $files = scandir($sourceDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        rename($sourceDir . $file, $destinationDir . $file);
    }
    
    // Hapus direktori sementara
    rmdir($sourceDir);
    
    echo "Pembaruan berhasil!\n";
} else {
    echo "Error: Gagal membuka file ZIP yang diunduh.\n";
    // Hapus file yang rusak
    unlink($zipFile);
}
?>