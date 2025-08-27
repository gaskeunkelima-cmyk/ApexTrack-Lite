<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        $errorMessage = 'Email dan kata sandi harus diisi.';
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $baseApiUrl = BASE_API_URL;

        $loginData = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $ch = curl_init("{$baseApiUrl}/auth/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = "Kesalahan cURL: " . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);

            if ($httpStatus === 200 && isset($responseData['token'])) {
                $_SESSION['auth_token'] = $responseData['token'];
                $_SESSION['user_id'] = $responseData['user']['id'];
                $_SESSION['role'] = $responseData['user']['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $errorMessage = $responseData['message'] ?? 'Login gagal. Silakan periksa kredensial Anda.';
            }
        }
        curl_close($ch);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .login-container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; border: none; border-radius: 4px; background-color: #007bff; color: #fff; font-size: 1rem; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .message { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .message.error { color: #dc3545; }
        .message.success { color: #28a745; }
        .link-register { display: block; text-align: center; margin-top: 1rem; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php 
        if (isset($errorMessage)): ?>
            <p class="message error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['message'])): ?>
            <p class="message success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <a href="https://apextrack.site" class="link-register" target="_blank">Belum punya akun? Daftar di sini.</a>
    </div>
</body>
</html>