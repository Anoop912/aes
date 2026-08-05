<?php

namespace App\Services;

use Exception;

class CryptoService
{
    const NONCE_SIZE = 12;
    const TAG_SIZE = 16;

    private const SEPARATOR = ':';
    private const GCM_TAG_LENGTH = 16;

    /*
    |--------------------------------------------------------------------------
    | AES-256-GCM
    |--------------------------------------------------------------------------
    */

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

        if (strlen($data) < self::NONCE_SIZE + self::TAG_SIZE) {
            throw new Exception('Invalid payload length.');
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

    /*
    |--------------------------------------------------------------------------
    | AES-256-GCM + RSA
    |--------------------------------------------------------------------------
    */

    public function encryptAes256rsa(
        string $plainText,
        string $privateKey,
        string $publicKey
    ): string {

        $dynamicKey = str_replace('-', '', $this->generateUUID());

        $iv = substr($dynamicKey, 0, 16);

        $tag = '';

        $cipherText = openssl_encrypt(
            $plainText,
            'aes-256-gcm',
            $dynamicKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($cipherText === false) {
            throw new Exception('AES encryption failed.');
        }

        $encryptedBody = base64_encode(
            $cipherText . $tag
        );

        $signature = $this->sign(
            $encryptedBody,
            $privateKey
        );

        $encryptedKey = $this->encryptKey(
            $dynamicKey,
            $publicKey
        );

        $payload = implode(
            self::SEPARATOR,
            [
                $encryptedKey,
                $encryptedBody,
                $signature
            ]
        );

        return base64_encode($payload);
    }

    public function decryptAes256rsa(
        string $encText,
        string $privateKey,
        string $publicKey
    ): string {

        $decoded = base64_decode($encText, true);

        if ($decoded === false) {
            throw new Exception('Invalid payload.');
        }

        $parts = explode(self::SEPARATOR, $decoded);

        if (count($parts) !== 3) {
            throw new Exception('Invalid payload format.');
        }

        [$headerKey, $encryptionRequestBody, $digitalSignature] = $parts;

        $encryptedKey = base64_decode($headerKey, true);

        if ($encryptedKey === false) {
            throw new Exception('Invalid encrypted key.');
        }

        $privKey = openssl_pkey_get_private($privateKey);

if (!$privKey) {

    while ($error = openssl_error_string()) {
        dump($error);
    }

    throw new Exception('Unable to load private key.');
}

        if (!openssl_private_decrypt(
            $encryptedKey,
            $aesKey,
            $privKey,
            OPENSSL_PKCS1_OAEP_PADDING
        )) {
            throw new Exception(
                'RSA decryption failed: ' . openssl_error_string()
            );
        }

        $pubKey = openssl_pkey_get_public($publicKey);

        if (!$pubKey) {
            throw new Exception('Invalid public key.');
        }

        $verify = openssl_verify(
            $encryptionRequestBody,
            base64_decode($digitalSignature),
            $pubKey,
            OPENSSL_ALGO_SHA256
        );

        if ($verify !== 1) {

            $errors = '';

            while ($msg = openssl_error_string()) {
                $errors .= $msg . PHP_EOL;
            }

            throw new Exception(
                'Signature verification failed.' . PHP_EOL . $errors
            );
        }

        $cipherPayload = base64_decode(
            $encryptionRequestBody,
            true
        );

        if ($cipherPayload === false) {
            throw new Exception('Invalid encrypted body.');
        }

        $tag = substr(
            $cipherPayload,
            -self::GCM_TAG_LENGTH
        );

        $cipherText = substr(
            $cipherPayload,
            0,
            -self::GCM_TAG_LENGTH
        );

        $iv = substr($aesKey, 0, 16);

        $plainText = openssl_decrypt(
            $cipherText,
            'aes-256-gcm',
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plainText === false) {
            throw new Exception(
                'AES decryption failed: ' . openssl_error_string()
            );
        }

        return $plainText;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    private function sign(
        string $data,
        string $privateKey
    ): string {

        $key = openssl_pkey_get_private($privateKey);

        if (!$key) {
            throw new Exception('Invalid private key.');
        }

        if (!openssl_sign(
            $data,
            $signature,
            $key,
            OPENSSL_ALGO_SHA256
        )) {
            throw new Exception('Signing failed.');
        }

        return base64_encode($signature);
    }

    private function encryptKey(
        string $plainKey,
        string $publicKey
    ): string {

        $key = openssl_pkey_get_public($publicKey);

        if (!$key) {
            throw new Exception('Invalid public key.');
        }

        if (!openssl_public_encrypt(
            $plainKey,
            $encrypted,
            $key,
            OPENSSL_PKCS1_OAEP_PADDING
        )) {
            throw new Exception('RSA encryption failed.');
        }

        return base64_encode($encrypted);
    }

    private function generateUUID(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(
            (ord($data[6]) & 0x0f) | 0x40
        );

        $data[8] = chr(
            (ord($data[8]) & 0x3f) | 0x80
        );

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4)
        );
    }
}