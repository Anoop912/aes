<?php

namespace App\Exceptions;

class CryptoException
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function encrypt(string $plainText, string $key): string
{
    if (empty(trim($plainText))) {
        throw new CryptoException('Plain text cannot be empty.');
    }

    if (empty($key)) {
        throw new CryptoException('Encryption key is required.');
    }

    if (strlen($key) !== 32) {
        throw new CryptoException(
            'AES-256 requires a 32-byte key.'
        );
    }

    $nonce = random_bytes(self::NONCE_SIZE);

    $tag = '';

    $encrypted = openssl_encrypt(
        $plainText,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $nonce,
        $tag
    );

    if ($encrypted === false) {
        throw new CryptoException(
            'Encryption failed.'
        );
    }

    return base64_encode(
        $nonce . $tag . $encrypted
    );
}
}
