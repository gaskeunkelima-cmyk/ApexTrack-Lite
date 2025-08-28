<?php

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['error' => ['message' => 'Method Not Allowed']]);
    exit();
}

$urlToScrape = $_POST['url'] ?? null;
$accessToken = $_POST['access_token'] ?? null;

if (empty($urlToScrape) || empty($accessToken)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'URL dan Access Token diperlukan.']]);
    exit();
}

function scrapeWithGraphAPI($url, $token) {
    $apiUrl = "https://graph.facebook.com/v23.0/";
    $postData = [
        'id'     => $url,
        'scrape' => 'true'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => ['message' => "cURL Error: " . $error_msg]];
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

$apiResponse = scrapeWithGraphAPI($urlToScrape, $accessToken);
echo json_encode($apiResponse);
?>