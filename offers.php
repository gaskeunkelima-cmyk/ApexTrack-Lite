<?php
include 'layout/header.php';

require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['auth_token'];

/**
 * Mengambil data dari endpoint API yang diberikan menggunakan cURL.
 *
 * @param string $endpoint URL endpoint API.
 * @param string $token Token otorisasi.
 * @return array Data yang didekodekan dari respons API.
 * @throws Exception jika permintaan API gagal.
 */
function fetchData($endpoint, $token)
{
    if (!$token) {
        throw new Exception('Autentikasi token tidak ditemukan. Silakan login kembali.');
    }

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        "Authorization: Bearer {$token}"
    ]);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Kesalahan saat mengambil data dari {$endpoint}: " . $curlError);
    }
    
    if ($httpStatus === 401) {
        session_destroy();
        header('Location: login.php?error=' . urlencode('Token Anda tidak valid atau kedaluwarsa. Silakan login kembali.'));
        exit();
    }
    
    if ($httpStatus !== 200) {
        $responseData = json_decode($response, true);
        $errorMessage = $responseData['message'] ?? "Gagal memuat data. Status: {$httpStatus}.";
        throw new Exception("Gagal memuat data dari {$endpoint}. Respons: {$errorMessage}");
    }
    
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respons API tidak valid: " . json_last_error_msg());
    }

    return $decodedResponse['data'] ?? [];
}

$offers = [];
$error = null;

try {
    $offers = fetchData(BASE_API_URL . '/offers', $token);
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Penawaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
            padding: 1.5rem 2.5rem;
        }
        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .table-container {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .table-cell {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }
        .table-header {
            background-color: #f9fafb;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }
        .status-badge {
            display: inline-flex;
            font-size: 0.75rem;
            line-height: 1rem;
            font-weight: 600;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Manajemen Penawaran</h2>

    <div id="message-container" class="mb-4 hidden">
        <div id="message-box" class="px-4 py-3 rounded relative" role="alert">
            <strong id="message-title" class="font-bold"></strong>
            <span id="message-text" class="block sm:inline"></span>
        </div>
    </div>
    
    <div class="mb-6 flex justify-end">
        <button id="open-add-modal" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors">
            Tambah Penawaran Baru
        </button>
    </div>

    <div class="card p-6 rounded-lg shadow-md bg-white">
        <div class="flex items-center text-gray-900 mb-4">
            <h3 class="text-xl font-semibold">Daftar Penawaran</h3>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="table-container overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300" id="offers-table">
                <thead>
                    <tr class="table-header">
                        <th class="table-cell">Nama</th>
                        <th class="table-cell">URL</th>
                        <th class="table-cell">Status</th>
                        <th class="table-cell">Negara</th>
                        <th class="table-cell">Perangkat</th>
                        <th class="table-cell">Proxy</th>
                        <th class="table-cell">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($offers)): ?>
                        <?php foreach ($offers as $offer): ?>
                            <tr data-id="<?php echo htmlspecialchars($offer['id'] ?? ''); ?>">
                                <td class="table-cell offer-name"><?php echo htmlspecialchars($offer['name'] ?? 'N/A'); ?></td>
                                <td class="table-cell offer-url"><a href="<?php echo htmlspecialchars($offer['url'] ?? '#'); ?>" target="_blank" class="text-blue-600 hover:underline">Link</a></td>
                                <td class="table-cell offer-status">
                                    <span class="status-badge 
                                        <?php 
                                            switch($offer['status']) {
                                                case 'active': echo 'bg-green-100 text-green-800'; break;
                                                case 'paused': echo 'bg-red-100 text-red-800'; break;
                                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800'; break;
                                            }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars(ucfirst($offer['status'] ?? 'N/A')); ?>
                                    </span>
                                </td>
                                <td class="table-cell offer-country"><?php echo htmlspecialchars($offer['country'] ?? 'N/A'); ?></td>
                                <td class="table-cell offer-device"><?php echo htmlspecialchars($offer['device'] ?? 'N/A'); ?></td>
                                <td class="table-cell offer-proxy">
                                    <?php echo ($offer['can_show_to_proxy'] ?? false) ? 'Ya' : 'Tidak'; ?>
                                </td>
                                <td class="table-cell flex gap-2">
                                    <button onclick='openEditModal(<?php echo htmlspecialchars(json_encode($offer)); ?>)' class="text-blue-600 hover:text-blue-900">Edit</button>
                                    <button onclick='confirmDelete(<?php echo htmlspecialchars(json_encode($offer['id'])); ?>)' class="text-red-600 hover:text-red-900">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="table-cell text-center text-gray-500 py-4">
                                <?php echo $error ? 'Gagal memuat data.' : 'Tidak ada penawaran yang ditemukan.'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="offer-modal" class="modal">
    <div class="modal-content card max-w-lg w-full">
        <h3 id="modal-title" class="text-xl font-semibold mb-4 text-gray-900">Tambah Penawaran Baru</h3>
        <form id="offer-form">
            <input type="hidden" id="offer-id" name="id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Penawaran</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Promo Ramadhan 2025" required>
                </div>
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                    <input type="url" name="url" id="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="https://www.contoh.com" required>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                    <input type="text" name="country" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: US, ID, Global" required>
                </div>
                <div>
                    <label for="device" class="block text-sm font-medium text-gray-700">Perangkat</label>
                    <input type="text" name="device" id="device" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Mobile, Desktop, All">
                </div>
                <div class="flex items-center">
                    <input id="can_show_to_proxy" name="can_show_to_proxy" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="can_show_to_proxy" class="ml-2 block text-sm text-gray-900">Tampilkan ke Proxy</label>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <button type="button" id="close-modal" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" id="submit-btn" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Penawaran
                </button>
            </div>
        </form>
    </div>
</div>

<div id="delete-modal" class="modal">
    <div class="modal-content card max-w-sm text-center">
        <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus penawaran ini?</p>
        <div class="flex justify-center gap-4">
            <button id="cancel-delete" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded hover:bg-gray-400">Batal</button>
            <button id="confirm-delete" class="bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700">Hapus</button>
        </div>
    </div>
</div>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const TOKEN = '<?php echo $token; ?>';
    
    const offersTableBody = document.querySelector('#offers-table tbody');
    const messageContainer = document.getElementById('message-container');
    const messageBox = document.getElementById('message-box');
    const messageTitle = document.getElementById('message-title');
    const messageText = document.getElementById('message-text');

    const offerModal = document.getElementById('offer-modal');
    const modalTitle = document.getElementById('modal-title');
    const offerForm = document.getElementById('offer-form');
    const offerIdInput = document.getElementById('offer-id');
    const submitBtn = document.getElementById('submit-btn');

    function showMessage(type, title, message) {
        messageContainer.classList.remove('hidden');
        messageBox.classList.remove('bg-green-100', 'border-green-400', 'text-green-700', 'bg-red-100', 'border-red-400', 'text-red-700');
        if (type === 'success') {
            messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
        } else {
            messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
        }
        messageTitle.innerText = title;
        messageText.innerText = message;
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }

    async function refreshOffersTable() {
        try {
            const response = await fetch(`${API_URL}/offers`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                }
            });
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Gagal memuat data terbaru.');
            }

            offersTableBody.innerHTML = '';
            const offers = data.data;

            if (offers.length === 0) {
                offersTableBody.innerHTML = `<tr><td colspan="7" class="table-cell text-center text-gray-500 py-4">Tidak ada penawaran yang ditemukan.</td></tr>`;
                return;
            }

            offers.forEach(offer => {
                const newRow = document.createElement('tr');
                newRow.dataset.id = offer.id;
                newRow.innerHTML = `
                    <td class="table-cell offer-name">${offer.name}</td>
                    <td class="table-cell offer-url"><a href="${offer.url}" target="_blank" class="text-blue-600 hover:underline">Link</a></td>
                    <td class="table-cell offer-status">
                        <span class="status-badge ${getBadgeClass(offer.status)}">
                            ${offer.status.charAt(0).toUpperCase() + offer.status.slice(1)}
                        </span>
                    </td>
                    <td class="table-cell offer-country">${offer.country}</td>
                    <td class="table-cell offer-device">${offer.device || 'N/A'}</td>
                    <td class="table-cell offer-proxy">
                        ${offer.can_show_to_proxy ? 'Ya' : 'Tidak'}
                    </td>
                    <td class="table-cell flex gap-2">
                        <button onclick='openEditModal(${JSON.stringify(offer)})' class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button onclick='confirmDelete(${offer.id})' class="text-red-600 hover:text-red-900">Hapus</button>
                    </td>
                `;
                offersTableBody.appendChild(newRow);
            });
        } catch (error) {
            showMessage('error', 'Error', error.message);
        }
    }

    function getBadgeClass(status) {
        switch(status) {
            case 'active': return 'bg-green-100 text-green-800';
            case 'paused': return 'bg-red-100 text-red-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    document.getElementById('open-add-modal').addEventListener('click', () => {
        modalTitle.innerText = 'Tambah Penawaran Baru';
        submitBtn.innerText = 'Simpan Penawaran';
        offerIdInput.value = ''; 
        offerForm.reset();
        offerModal.style.display = 'flex';
    });
    
    document.getElementById('close-modal').addEventListener('click', () => {
        offerModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === offerModal) {
            offerModal.style.display = 'none';
        }
    });

    function openEditModal(offer) {
        modalTitle.innerText = 'Edit Penawaran';
        submitBtn.innerText = 'Perbarui Penawaran';
        offerIdInput.value = offer.id;
        offerForm.querySelector('#name').value = offer.name;
        offerForm.querySelector('#url').value = offer.url;
        offerForm.querySelector('#status').value = offer.status;
        offerForm.querySelector('#country').value = offer.country;
        offerForm.querySelector('#device').value = offer.device || '';
        offerForm.querySelector('#can_show_to_proxy').checked = offer.can_show_to_proxy;
        
        offerModal.style.display = 'flex';
    }

    offerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const isEditing = offerIdInput.value !== '';
        const method = isEditing ? 'PUT' : 'POST';
        const url = isEditing ? `${API_URL}/offers/${offerIdInput.value}` : `${API_URL}/offers`;
        
        const formData = new FormData(offerForm);
        const offerData = Object.fromEntries(formData.entries());
        offerData.can_show_to_proxy = offerForm.elements.can_show_to_proxy.checked ? 1 : 0;
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                },
                body: JSON.stringify(offerData)
            });

            const data = await response.json();

            if (response.ok) {
                showMessage('success', 'Berhasil', data.message);
                offerModal.style.display = 'none';
                refreshOffersTable();
            } else {
                let errorMessage = data.message || (isEditing ? 'Gagal memperbarui penawaran.' : 'Gagal menyimpan penawaran.');
                if (data.errors) {
                    errorMessage += ': ' + Object.values(data.errors).flat().join(' ');
                }
                showMessage('error', 'Error', errorMessage);
            }
        } catch (error) {
            showMessage('error', 'Error', 'Terjadi kesalahan jaringan.');
        }
    });

    function confirmDelete(id) {
        document.getElementById('delete-modal').style.display = 'flex';
        document.getElementById('confirm-delete').onclick = () => deleteOffer(id);
    }

    async function deleteOffer(id) {
        document.getElementById('delete-modal').style.display = 'none';
        try {
            const response = await fetch(`${API_URL}/offers/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                }
            });
            const data = await response.json();

            if (response.ok) {
                showMessage('success', 'Berhasil', data.message);
                refreshOffersTable();
            } else {
                showMessage('error', 'Error', data.message || 'Gagal menghapus penawaran.');
            }
        } catch (error) {
            showMessage('error', 'Error', 'Terjadi kesalahan jaringan.');
        }
    }

    document.getElementById('cancel-delete').addEventListener('click', () => {
        document.getElementById('delete-modal').style.display = 'none';
    });

    refreshOffersTable();
</script>

<?php
include 'layout/footer.php';
?>