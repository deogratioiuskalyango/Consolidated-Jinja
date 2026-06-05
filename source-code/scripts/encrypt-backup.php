<?php

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php encrypt-backup.php input.zip output.zip.enc" . PHP_EOL);
    exit(1);
}

$input = $argv[1];
$output = $argv[2];
$passphrase = getenv('BACKUP_ENCRYPTION_PASSPHRASE');

if ($passphrase === false || trim($passphrase) === '') {
    fwrite(STDERR, "BACKUP_ENCRYPTION_PASSPHRASE is required." . PHP_EOL);
    exit(1);
}

if (!is_file($input)) {
    fwrite(STDERR, "Input file not found: {$input}" . PHP_EOL);
    exit(1);
}

$plain = file_get_contents($input);
if ($plain === false) {
    fwrite(STDERR, "Unable to read input file." . PHP_EOL);
    exit(1);
}

$salt = random_bytes(16);
$iv = random_bytes(12);
$key = hash_pbkdf2('sha256', $passphrase, $salt, 250000, 32, true);
$tag = '';
$cipher = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

if ($cipher === false) {
    fwrite(STDERR, "Encryption failed." . PHP_EOL);
    exit(1);
}

$payload = json_encode([
    'version' => 1,
    'cipher' => 'aes-256-gcm',
    'kdf' => 'pbkdf2-sha256',
    'iterations' => 250000,
    'salt' => base64_encode($salt),
    'iv' => base64_encode($iv),
    'tag' => base64_encode($tag),
    'data' => base64_encode($cipher),
], JSON_PRETTY_PRINT);

if ($payload === false || file_put_contents($output, $payload) === false) {
    fwrite(STDERR, "Unable to write encrypted output file." . PHP_EOL);
    exit(1);
}

echo "Encrypted backup written to {$output}" . PHP_EOL;
