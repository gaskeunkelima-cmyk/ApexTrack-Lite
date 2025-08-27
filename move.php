<?php
// update.php
$zipUrl = 'https://github.com/apextrack/ApexTrack-Lite/archive/refs/heads/master.zip';
$zipFile = 'update.zip';
$tempDir = 'update_temp/';

if (!class_exists('ZipArchive')) {
    header('Location: settings.php?update_status=fail&reason=ZipArchive_missing');
    exit;
}

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

$zipContent = @file_get_contents($zipUrl);
if ($zipContent === false) {
    header('Location: settings.php?update_status=fail&reason=Download_failed');
    exit;
}

if (file_put_contents($zipFile, $zipContent) === false) {
    header('Location: settings.php?update_status=fail&reason=Save_failed');
    exit;
}

$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    if (!$zip->extractTo($tempDir)) {
        $zip->close();
        unlink($zipFile);
        header('Location: settings.php?update_status=fail&reason=Extraction_failed');
        exit;
    }
    $zip->close();
    unlink($zipFile);
    
    header('Location: move.php');
    exit;
} else {
    unlink($zipFile);
    header('Location: settings.php?update_status=fail&reason=Zip_corrupted');
    exit;
}
?>