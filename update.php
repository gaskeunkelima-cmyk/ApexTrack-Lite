<?php
// update.php
$zipUrl = 'https://github.com/apextrack/ApexTrack-Lite/archive/refs/heads/master.zip';
$zipFile = 'update.zip';
$tempDir = 'update_temp/';

if (!class_exists('ZipArchive')) {
    die("Error: PHP ZipArchive class is not available.\n");
}

echo "Memulai proses pembaruan...\n";

// Buat direktori sementara
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

// Unduh file ZIP
echo "Mengunduh pembaruan dari GitHub...\n";
$zipContent = @file_get_contents($zipUrl);
if ($zipContent === false) {
    die("Error: Gagal mengunduh file ZIP.\n");
}

if (file_put_contents($zipFile, $zipContent) === false) {
    die("Error: Gagal menyimpan file ZIP.\n");
}

echo "Berhasil mengunduh file ZIP.\n";

// Mengekstrak file ZIP ke direktori sementara
echo "Mengekstrak file...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    if (!$zip->extractTo($tempDir)) {
        $zip->close();
        unlink($zipFile);
        die("Error: Gagal mengekstrak file ZIP.\n");
    }
    $zip->close();
    
    // Hapus file ZIP setelah diekstrak
    unlink($zipFile);
    
    echo "Pembaruan berhasil diekstrak!\n";
    echo "Memulai proses pemindahan file...\n";
    
    // Alihkan ke skrip pemindahan untuk menyelesaikan pembaruan
    echo "<script>window.location.href = 'move_files.php';</script>";

} else {
    echo "Error: Gagal membuka file ZIP yang diunduh.\n";
    unlink($zipFile);
}
?>