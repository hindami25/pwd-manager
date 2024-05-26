<?php
function register_user($username, $password) {
    global $pdo;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $master_key = bin2hex(random_bytes(16));
    $master_key_encrypted = openssl_encrypt($master_key, 'aes-256-cbc', $password_hash, 0, str_repeat('0', 16));
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, master_key_encrypted) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $password_hash, $master_key_encrypted]);
}

function login_user($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}

function encrypt_password($password, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_password($encrypted_password, $key) {
    $data = base64_decode($encrypted_password);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
}


function generate_password($length = 16) {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $numbers = '0123456789';
    $specialChars = '!@#$%^&*()-_=+<>?';

    // Ensure the password meets the criteria by including at least one character from each set
    $allChars = $uppercase . $lowercase . $numbers . $specialChars;
    $password = $uppercase[rand(0, strlen($uppercase) - 1)]
              . $lowercase[rand(0, strlen($lowercase) - 1)]
              . $numbers[rand(0, strlen($numbers) - 1)]
              . $specialChars[rand(0, strlen($specialChars) - 1)];

    // Fill the rest of the password length with random characters from all sets
    for ($i = 4; $i < $length; $i++) {
        $password .= $allChars[rand(0, strlen($allChars) - 1)];
    }

    // Shuffle the password to avoid predictable patterns
    $password = str_shuffle($password);

    return $password;
}

?>
