<?php
include 'layout/header.php';
require_once 'config.php'; 

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$authToken = $_SESSION['auth_token'];

// Token App untuk scraping, ditaruh di sini agar tidak terlihat oleh pengguna
$appAccessToken = '1308899767242947|HVu-8GkDtyPmpAR2SQOAx2BT2bg';

?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Smartlink Generator</h2>
    <div class="mx-auto bg-white p-8 shadow-lg">
        <div id="status-message" class="hidden mb-4 p-4 text-center rounded-lg"></div>

        <form id="generator-form" enctype="multipart/form-data" class="space-y-6">
            <div class="border border-gray-300 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="offer" class="block text-sm font-medium text-gray-700">Offers (Opsional)</label>
                        <select id="offer" name="offer" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="shared_domain" class="block text-sm font-medium text-gray-700">Domain</label>
                        <select id="shared_domain" name="shared_domain" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="redirect_type" class="block text-sm font-medium text-gray-700">Redirect</label>
                        <select id="redirect_type" name="redirect_type" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Smartlink Type</label>
                        <select id="type" name="type" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="generation_mode" class="block text-sm font-medium text-gray-700">Mode</label>
                        <select id="generation_mode" name="generation_mode" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div id="shortener-choice-container" class="hidden">
                        <label for="shortener_choice" class="block text-sm font-medium text-gray-700">Shortner</label>
                        <select id="shortener_choice" name="shortener_choice" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                </div>
            </div>

            <div class="border border-gray-300 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Meta Tags (Opsional)</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="og_image_file" class="block text-sm font-medium text-gray-700">Unggah Gambar OG (Open Graph)</label>
                        <input type="file" id="og_image_file" name="og_image_file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                    </div>
                    <div>
                        <label for="favicon_file" class="block text-sm font-medium text-gray-700">Unggah Favicon</label>
                        <input type="file" id="favicon_file" name="favicon_file" accept=".ico, .png, .jpg, .jpeg, .gif, .svg" class="mt-1 block w-full text-sm text-gray-500">
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out">
                Generate Smartlink
            </button>
        </form>

        <div id="result-section" class="hidden mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Hasil</h3>
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-700">URL Final:</p>
                    <a id="final-url-link" href="#" target="_blank" class="text-blue-600 font-semibold break-all hover:underline"></a>
                </div>
                <div id="first-shortened-url-container" class="hidden bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-700">Url Smartlink/Shortlink:</p>
                    <a id="first-shortened-url-link" href="#" target="_blank" class="text-blue-600 font-semibold break-all hover:underline"></a>
                </div>
            </div>
            <div id="scraping-log" class="hidden mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                <h4 class="text-md font-semibold text-gray-800 mb-2">Log Scraping</h4>
                <div id="log-content">
                    <p id="log-message" class="text-gray-600"></p>
                    <pre id="log-details" class="bg-gray-100 p-2 text-sm text-gray-700 rounded overflow-auto mt-2"></pre>
                    <button id="scrape-again-button" class="hidden mt-4 w-full py-2 px-4 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 transition duration-300 ease-in-out">
                        Scrape Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const AUTH_TOKEN = '<?php echo $authToken; ?>';
    const FACEBOOK_ACCESS_TOKEN = '<?php echo $appAccessToken; ?>';

    const form = document.getElementById('generator-form');
    const statusMessage = document.getElementById('status-message');
    const resultSection = document.getElementById('result-section');
    const finalUrlLink = document.getElementById('final-url-link');
    const firstShortenedUrlContainer = document.getElementById('first-shortened-url-container');
    const firstShortenedUrlLink = document.getElementById('first-shortened-url-link');
    const shortenerChoiceContainer = document.getElementById('shortener-choice-container');
    const scrapingLog = document.getElementById('scraping-log');
    const logMessage = document.getElementById('log-message');
    const logDetails = document.getElementById('log-details');
    const scrapeAgainButton = document.getElementById('scrape-again-button');

    function showStatus(message, type) {
        statusMessage.innerHTML = message;
        statusMessage.className = 'mb-4 p-4 text-center rounded-lg';
        if (type === 'success') {
            statusMessage.classList.add('bg-green-100', 'text-green-700');
        } else if (type === 'error') {
            statusMessage.classList.add('bg-red-100', 'text-red-700');
        } else if (type === 'info') {
            statusMessage.classList.add('bg-blue-100', 'text-blue-700');
        }
        statusMessage.classList.remove('hidden');
    }

    async function fetchFormData() {
        try {
            showStatus('Memuat data formulir...', 'info');
            const response = await fetch(`${API_URL}/generator-data`, {
                headers: { 'Authorization': `Bearer ${AUTH_TOKEN}` }
            });
            const data = await response.json();
            
            if (!response.ok) {
                let errorMessage = data.message || 'Gagal mengambil data formulir.';
                if (response.status === 403) {
                    errorMessage = 'Unauthorized action. Mohon login ulang.';
                }
                throw new Error(errorMessage);
            }
            
            populateSelect('offer', data.offers, 'Offers', 'id', 'name');
            populateSelect('shared_domain', data.domains, 'Domain', null, null);
            populateSelect('redirect_type', data.redirect_types, 'Redirect', null, null);
            
            const typeSelect = document.getElementById('type');
            const typeMapping = { 'render': 'Render Halaman', 'redirect': 'Redirect Langsung' };
            typeSelect.innerHTML = '';
            const defaultTypeOption = document.createElement('option');
            defaultTypeOption.textContent = 'Smartlink';
            defaultTypeOption.value = '';
            typeSelect.appendChild(defaultTypeOption);
            if (data.types) {
                data.types.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.textContent = typeMapping[type] || type;
                    typeSelect.appendChild(option);
                });
            }

            const generationModeSelect = document.getElementById('generation_mode');
            const generationModeMapping = { 'smartlink_external_self': 'Double Shortener', 'smartlink_self': 'Single Shortener' };
            generationModeSelect.innerHTML = '';
            const defaultModeOption = document.createElement('option');
            defaultModeOption.textContent = 'Mode';
            defaultModeOption.value = '';
            generationModeSelect.appendChild(defaultModeOption);
            if (data.generation_modes) {
                data.generation_modes.forEach(mode => {
                    const option = document.createElement('option');
                    option.value = mode;
                    option.textContent = generationModeMapping[mode] || mode;
                    generationModeSelect.appendChild(option);
                });
            }
            generationModeSelect.addEventListener('change', (e) => {
                shortenerChoiceContainer.style.display = e.target.value === 'smartlink_external_self' ? 'block' : 'none';
            });
            generationModeSelect.dispatchEvent(new Event('change'));
            populateSelect('shortener_choice', data.shortener_choices, 'Shortner', null, null);
            showStatus('Data formulir berhasil dimuat.', 'success');
            setTimeout(() => { statusMessage.classList.add('hidden'); }, 3000);
        } catch (error) {
            console.error('Kesalahan saat mengambil data formulir:', error);
            showStatus(`Gagal mengambil data formulir: ${error.message}`, 'error');
        }
    }

    function populateSelect(selectId, data, placeholder, valueKey, textKey) {
        const select = document.getElementById(selectId);
        select.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.textContent = placeholder;
        defaultOption.value = '';
        select.appendChild(defaultOption);
        if (data) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = valueKey && textKey ? item[valueKey] : item;
                option.textContent = valueKey && textKey ? item[textKey] : item;
                select.appendChild(option);
            });
        }
    }

    async function performScraping(url) {
        showStatus('Memulai scraping Graph API...', 'info');
        scrapingLog.classList.remove('hidden');
        logMessage.textContent = 'Mengirim permintaan scraping...';
        logDetails.textContent = '...';
        scrapeAgainButton.classList.add('hidden');

        try {
            const scrapingData = new FormData();
            scrapingData.append('url', url);
            scrapingData.append('access_token', FACEBOOK_ACCESS_TOKEN);

            const scrapeResponse = await fetch('scrape_proxy.php', {
                method: 'POST',
                body: scrapingData
            });

            const scrapeResult = await scrapeResponse.json();

            if (scrapeResult.error) {
                logMessage.textContent = '❌ Scraping Gagal!';
                logDetails.textContent = JSON.stringify(scrapeResult.error, null, 2);
                scrapeAgainButton.classList.remove('hidden');
                showStatus('Scraping Gagal: ' + scrapeResult.error.message, 'error');
            } else {
                logMessage.textContent = '✅ Scraping Berhasil!';
                logDetails.textContent = JSON.stringify(scrapeResult, null, 2);
                showStatus('Scraping berhasil! Log meta tags ada di bawah.', 'success');
            }
        } catch (scrapeError) {
            logMessage.textContent = '❌ Kesalahan saat memicu scraping.';
            logDetails.textContent = scrapeError.message;
            scrapeAgainButton.classList.remove('hidden');
            console.error('Kesalahan saat memicu scraping:', scrapeError);
            showStatus('Gagal memicu scraping: ' + scrapeError.message, 'error');
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        showStatus('Sedang memproses...', 'info');
        resultSection.classList.add('hidden');
        scrapingLog.classList.add('hidden');

        const formData = new FormData(form);
        const generationMode = formData.get('generation_mode');
        if (generationMode !== 'smartlink_external_self') {
            formData.delete('shortener_choice');
        }
        
        try {
            const response = await fetch(`${API_URL}/generate-smartlink`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${AUTH_TOKEN}`
                }
            });

            const data = await response.json();
            if (!response.ok) {
                let errorMessage = data.message || 'Terjadi kesalahan tidak terduga.';
                if (data.errors) {
                    errorMessage += '<br>' + Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(errorMessage);
            }
            
            showStatus('URL berhasil dibuat!', 'success');
            resultSection.classList.remove('hidden');
            finalUrlLink.href = data.final_shared_url;
            finalUrlLink.textContent = data.final_shared_url;

            if (data.smartlink_url_after_first_shortening) {
                firstShortenedUrlContainer.classList.remove('hidden');
                firstShortenedUrlLink.href = data.smartlink_url_after_first_shortening;
                firstShortenedUrlLink.textContent = data.smartlink_url_after_first_shortening;
            } else {
                firstShortenedUrlContainer.classList.add('hidden');
            }

            await performScraping(data.final_shared_url);
            
        } catch (error) {
            console.error('Kesalahan saat membuat URL:', error);
            showStatus(`Gagal membuat URL: ${error.message}`, 'error');
            resultSection.classList.add('hidden');
        }
    });

    scrapeAgainButton.addEventListener('click', () => {
        const urlToScrape = finalUrlLink.href;
        if (urlToScrape && urlToScrape !== '#') {
            performScraping(urlToScrape);
        } else {
            showStatus('URL tidak ditemukan untuk scraping ulang.', 'error');
        }
    });

    fetchFormData();
</script>

<?php
include 'layout/footer.php';
?>