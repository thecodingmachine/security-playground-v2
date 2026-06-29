<?php

declare(strict_types=1);

namespace App\Security;

final class CryptoService
{
    public function encrypt(string $value, string $secretKey): string|false
    {
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($value, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);

        if (!is_string($cipher)) {
            return false;
        }

        return base64_encode($iv.$cipher);
    }

    public function decrypt(string $payload, string $secretKey): string|false
    {
        $raw = base64_decode($payload, true);
        if (!is_string($raw) || strlen($raw) <= 16) {
            return false;
        }

        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        return openssl_decrypt($cipher, 'aes-256-cbc', $secretKey, OPENSSL_RAW_DATA, $iv);
    }
}
