<?php

namespace App\Services;

use Exception;

class CryptoService
{
    const NONCE_SIZE = 12;
    const TAG_SIZE = 16;

    public function encrypt(string $plainText, string $key): string
    {
        if (strlen($key) !== 32) {
            throw new Exception('Key must be exactly 32 bytes.');
        }

        $nonce = random_bytes(self::NONCE_SIZE);

        $tag = '';

        $cipherText = openssl_encrypt(
            $plainText,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        if ($cipherText === false) {
            throw new Exception('Encryption failed.');
        }

        return base64_encode(
            $nonce .
            $tag .
            $cipherText
        );
    }

    public function decrypt(string $payload, string $key): string
    {
        if (strlen($key) !== 32) {
            throw new Exception('Key must be exactly 32 bytes.');
        }

        $data = base64_decode($payload, true);

        if ($data === false) {
            throw new Exception('Invalid payload.');
        }

        $nonce = substr($data, 0, self::NONCE_SIZE);
        $tag = substr($data, self::NONCE_SIZE, self::TAG_SIZE);
        $cipherText = substr(
            $data,
            self::NONCE_SIZE + self::TAG_SIZE
        );

        $plainText = openssl_decrypt(
            $cipherText,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag
        );

        if ($plainText === false) {
            throw new Exception('Decryption failed.');
        }

        return $plainText;
    }
}