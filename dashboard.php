<?php
include 'layout/header.php';

require_once 'config.php';

$token = $_SESSION['auth_token'];
$endpoint = '/dashboard';

function fetchData($url, $token)
{
    $ch = curl_init($url);
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
        throw new Exception("Kesalahan saat mengambil data dari {$url}: " . $curlError);
    }
    
    if ($httpStatus === 401) {
        throw new Exception("Unauthorized. Token tidak valid atau kedaluwarsa.");
    }
    
    if ($httpStatus === 404) {
        throw new Exception("Endpoint tidak ditemukan: {$url}.");
    }
    
    if ($httpStatus !== 200) {
        $responseData = json_decode($response, true);
        $errorMessage = $responseData['error'] ?? "Gagal memuat data. Status: {$httpStatus}.";
        throw new Exception("Gagal memuat data dari {$url}. Respons: {$errorMessage}");
    }
    
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respons API tidak valid: " . json_last_error_msg());
    }

    return $decodedResponse;
}

try {
    $dashboardData = fetchData(BASE_API_URL . $endpoint, $token);

    $summaryData = $dashboardData['summary'] ?? [];
    $recentClicks = $dashboardData['recent_clicks'] ?? [];
    $recentLeads = $dashboardData['recent_leads'] ?? [];
    $performanceReport = $dashboardData['performance_report'] ?? [];
    
    $todayClicks = $summaryData['today_clicks'] ?? 0;
    $todayLeads = $summaryData['today_leads'] ?? 0;
    $todayRevenue = $summaryData['today_revenue'] ?? 0;
    $todayEpc = $summaryData['today_epc'] ?? 0;

} catch (Exception $e) {
    session_destroy();
    header('Location: login.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card flex flex-col items-start bg-blue-100 text-blue-800 border-l-4 border-blue-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-chart-line w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Clicks Today</h3>
            </div>
            <div class="text-2xl font-bold">
                <?php echo htmlspecialchars(number_format($todayClicks)); ?>
            </div>
        </div>
        
        <div class="card flex flex-col items-start bg-purple-100 text-purple-800 border-l-4 border-purple-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-user-check w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Conversions Today</h3>
            </div>
            <div class="text-2xl font-bold">
                <?php echo htmlspecialchars(number_format($todayLeads)); ?>
            </div>
        </div>
        
        <div class="card flex flex-col items-start bg-orange-100 text-orange-800 border-l-4 border-orange-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-dollar-sign w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">Revenue Today</h3>
            </div>
            <div class="text-2xl font-bold">
                $<?php echo htmlspecialchars(number_format($todayRevenue, 2)); ?>
            </div>
        </div>
        
        <div class="card flex flex-col items-start bg-green-100 text-green-800 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-arrow-trend-up w-5 h-5 mr-2"></i>
                <h3 class="text-sm font-semibold uppercase">EPC Today</h3>
            </div>
            <div class="text-2xl font-bold">
                $<?php echo htmlspecialchars(number_format($todayEpc, 2)); ?>
            </div>
        </div>
    </div>

    <div class="card p-6 shadow-md bg-white mt-6">
        <div class="flex items-center text-gray-900 mb-4">
            <h3 class="text-xl font-semibold">Recent Clicks</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Offer Name</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">IP Address</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Country</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">OS</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Device</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Redirect Type</th>
                        <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentClicks)): ?>
                        <?php foreach ($recentClicks as $click): ?>
                            <tr>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-user w-4 h-4 mr-2"></i>
                                        <?php echo htmlspecialchars($click['sub_id'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars($click['offer_name'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars($click['ip_address'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                    <div class="flex items-center">
                                        <?php 
                                            $countryCode = strtolower($click['country_code'] ?? 'us'); // Default ke 'us'
                                            // Beberapa kode negara memiliki mapping berbeda di flag-icon-css
                                            // Contoh: UK -> gb, US -> us (sudah sesuai)
                                            if ($countryCode === 'uk') $countryCode = 'gb';
                                            // Tambahkan mapping lain jika diperlukan
                                        ?>
                                        <span class="flag-icon flag-icon-<?php echo htmlspecialchars($countryCode); ?> mr-2"></span>
                                        <?php echo htmlspecialchars($click['country_code'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars($click['os'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                    <div class="flex items-center">
                                        <?php 
                                            $deviceIcon = 'fa-mobile-screen-button';
                                            if (isset($click['device_type'])) {
                                                switch (strtolower($click['device_type'])) {
                                                    case 'desktop':
                                                        $deviceIcon = 'fa-desktop';
                                                        break;
                                                    case 'tablet':
                                                        $deviceIcon = 'fa-tablet';
                                                        break;
                                                    default: // 'mobile' atau lainnya
                                                        $deviceIcon = 'fa-mobile-screen-button';
                                                        break;
                                                }
                                            }
                                        ?>
                                        <i class="fa-solid <?php echo $deviceIcon; ?> w-4 h-4 mr-2"></i>
                                        <?php echo htmlspecialchars($click['device_type'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars($click['redirect_type_used'] ?? 'N/A'); ?></td> 
                                <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($click['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-4 border border-gray-300">Tidak ada klik terbaru hari ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="card p-6 shadow-md bg-white">
            <div class="flex items-center text-gray-900 mb-4">
                <h3 class="text-xl font-semibold">Performance Report</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Hits</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Conversions</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Revenue</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">CR (%)</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">EPC ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($performanceReport)): ?>
                            <?php foreach ($performanceReport as $report): ?>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-user w-4 h-4 mr-2"></i>
                                            <?php echo htmlspecialchars($report['sub_id'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars(number_format($report['hits'] ?? 0)); ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars(number_format($report['conversions'] ?? 0)); ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">$<?php echo htmlspecialchars(number_format($report['revenue'] ?? 0, 2)); ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars(number_format($report['cr'] ?? 0, 2)); ?>%</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">$<?php echo htmlspecialchars(number_format($report['epc'] ?? 0, 2)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-4 border border-gray-300">Tidak ada data untuk laporan kinerja.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card p-6 shadow-md bg-white">
            <div class="flex items-center text-gray-900 mb-4">
                <h3 class="text-xl font-semibold">Recent Conversions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Sub ID</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Revenue</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Country</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Device</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">IP Address</th>
                            <th class="py-2 px-4 text-sm text-gray-500 font-semibold uppercase border border-gray-300">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentLeads)): ?>
                            <?php foreach ($recentLeads as $lead): ?>
                                <tr>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-user w-4 h-4 mr-2"></i>
                                            <?php echo htmlspecialchars($lead['sub_id'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">$<?php echo htmlspecialchars(number_format($lead['payout'] ?? 0, 2)); ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                        <div class="flex items-center">
                                            <?php 
                                                $countryCode = strtolower($lead['country_code'] ?? 'us'); // Default ke 'us'
                                                if ($countryCode === 'uk') $countryCode = 'gb';
                                            ?>
                                            <span class="flag-icon flag-icon-<?php echo htmlspecialchars($countryCode); ?> mr-2"></span>
                                            <?php echo htmlspecialchars($lead['country_code'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300">
                                        <div class="flex items-center">
                                            <?php
                                                $deviceIcon = 'fa-mobile-screen-button';
                                                if (isset($lead['device_type'])) {
                                                    switch (strtolower($lead['device_type'])) {
                                                        case 'desktop':
                                                            $deviceIcon = 'fa-desktop';
                                                            break;
                                                        case 'tablet':
                                                            $deviceIcon = 'fa-tablet';
                                                            break;
                                                        default:
                                                            $deviceIcon = 'fa-mobile-screen-button';
                                                            break;
                                                    }
                                                }
                                            ?>
                                            <i class="fa-solid <?php echo $deviceIcon; ?> w-4 h-4 mr-2"></i>
                                            <?php echo htmlspecialchars($lead['device_type'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars($lead['ip_address'] ?? 'N/A'); ?></td>
                                    <td class="py-3 px-4 text-sm text-gray-700 border border-gray-300"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($lead['created_at']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-4 border border-gray-300">Tidak ada konversi terbaru hari ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>


<?php
include 'layout/footer.php';
?>