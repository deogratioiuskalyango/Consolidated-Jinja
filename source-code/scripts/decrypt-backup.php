<?php

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php decrypt-backup.php input.zip.enc output.zip" . PHP_EOL);
    exit(1);
}

$input = $argv[1];
$output = $argv[2];
$passphrase = getenv('BACKUP_ENCRYPTION_PASSPHRASE');

if ($passphrase === false || trim($passphrase) === '') {
    fwrite(STDERR, "BACKUP_ENCRYPTION_PASSPHRASE is required." . PHP_EOL);
    exit(1);
}

$payload = json_decode(file_get_contents($input), true);
if (!is_array($payload) || ($payload['version'] ?? null) !== 1) {
    fwrite(STDERR, "Invalid encrypted backup format." . PHP_EOL);
    exit(1);
}

$salt = base64_decode($payload['salt'], true);
$iv = base64_decode($payload['iv'], true);
$tag = base64_decode($payload['tag'] ?? '', true);
$cipher = base64_decode($payload['data'], true);

if ($salt === false || $iv === false || $tag === false || $cipher === false) {
    fwrite(STDERR, "Encrypted backup is corrupted." . PHP_EOL);
    exit(1);
}

$iterations = (int) ($payload['iterations'] ?? 250000);
$key = hash_pbkdf2('sha256', $passphrase, $salt, $iterations, 32, true);
$plain = openssl_decrypt($cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

if ($plain === false) {
    fwrite(STDERR, "Decryption failed. Check the passphrase." . PHP_EOL);
    exit(1);
}

if (file_put_contents($output, $plain) === false) {
    fwrite(STDERR, "Unable to write decrypted output file." . PHP_EOL);
    exit(1);
}

echo "Decrypted backup written to {$output}" . PHP_EOL;
