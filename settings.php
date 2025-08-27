<?php

$settingsFile = 'settings.json';
$uploadsDir = 'uploads/';
$allowedFileTypes = ['image/x-icon', 'image/vnd.microsoft.icon'];
$message = '';
$messageType = '';

if (!is_dir($uploadsDir)) {
    if (!mkdir($uploadsDir, 0755, true)) {
        die('Gagal membuat direktori uploads.');
    }
}

$siteName = '';
$faviconUrl = '';
if (file_exists($settingsFile)) {
    $settingsData = file_get_contents($settingsFile);
    $settings = json_decode($settingsData, true);
    if ($settings) {
        $siteName = htmlspecialchars($settings['site_name'] ?? '');
        $faviconUrl = htmlspecialchars($settings['favicon_url'] ?? '');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSiteName = trim($_POST['site_name'] ?? '');

    if (empty($newSiteName)) {
        $message = 'Nama website tidak boleh kosong.';
        $messageType = 'error';
    } else {
        $newFaviconUrl = $faviconUrl;
        $uploadSuccess = true;

        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] == 0) {
            $fileTmpPath = $_FILES['favicon_file']['tmp_name'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($fileInfo, $fileTmpPath);
            finfo_close($fileInfo);
            $fileName = basename($_FILES['favicon_file']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileType, $allowedFileTypes) && $fileExtension === 'ico') {
                $newFileName = uniqid() . '.' . $fileExtension;
                $destPath = $uploadsDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $newFaviconUrl = $destPath;
                    $message = 'Favicon baru berhasil diunggah.';
                    $messageType = 'success';
                } else {
                    $message = 'Gagal memindahkan file yang diunggah.';
                    $messageType = 'error';
                    $uploadSuccess = false;
                }
            } else {
                $message = 'Hanya file .ico yang diperbolehkan.';
                $messageType = 'error';
                $uploadSuccess = false;
            }
        }

        if ($uploadSuccess) {
            $settings = [
                'site_name' => $newSiteName,
                'favicon_url' => $newFaviconUrl
            ];
            $jsonData = json_encode($settings, JSON_PRETTY_PRINT);
            
            if ($jsonData === false) {
                $message = 'Gagal mengonversi data ke JSON.';
                $messageType = 'error';
            } elseif (file_put_contents($settingsFile, $jsonData) === false) {
                $message = 'Gagal menyimpan pengaturan.';
                $messageType = 'error';
            } else {
                $message = 'Pengaturan berhasil disimpan!';
                $messageType = 'success';
                $siteName = htmlspecialchars($newSiteName);
                $faviconUrl = htmlspecialchars($newFaviconUrl);
            }
        }
    }
}

include 'layout/header.php';
?>
<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Pengaturan Website</h2>

        <?php if ($message): ?>
            <div class="text-center p-4 mb-4 rounded-lg
                    <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-lg">
            <div>
                <label for="site_name" class="block text-sm font-medium text-gray-700">Nama Website</label>
                <input type="text" id="site_name" name="site_name" value="<?php echo $siteName; ?>" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label for="favicon_file" class="block text-sm font-medium text-gray-700">Unggah Favicon (.ico)</label>
                <input type="file" id="favicon_file" name="favicon_file" accept=".ico"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-2 text-xs text-gray-500">Maks. ukuran file 2 MB. Hanya format .ico yang didukung.</p>
                <div class="mt-4 flex items-center space-x-2">
                    <p class="text-sm text-gray-500">Favicon saat ini:</p>
                    <img src="<?php echo $faviconUrl; ?>" alt="Favicon Website Saat Ini" class="w-8 h-8 rounded-full shadow">
                </div>
            </div>
            
            <button type="submit"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-300 ease-in-out">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</main>

<?php
include 'layout/footer.php';
?>