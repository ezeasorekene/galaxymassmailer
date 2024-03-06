<?php

namespace ezeasorekene\App\Core\System;

class Encryption
{
    /**
     * Encrypt raw text using a custom key or random key
     * @param string $raw_text The raw text to encrypt
     * @param string $key The key to encrypt the raw text. Leave empty to use a random key
     * @return string $ciphertext The encrypted text
     */
    public static function encrypt($raw_text, $theKey = null)
    {
        empty($theKey) ? $key = openssl_random_pseudo_bytes(32) : $key = $theKey;
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($raw_text, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    /**
     * Decrypt encrypted text using a custom key or default key
     * @param string $encrypted_text The encrypted text to decrypt
     * @param string $key The key to encrypt the raw text. Leave empty to use a random key
     * @return string $original_plaintext The decrypted plain text
     */
    public static function decrypt($encrypted_text, $theKey = null)
    {
        $key = $theKey ?? null;
        $c = base64_decode($encrypted_text);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        if (hash_equals($hmac, $calcmac)) { // timing attack safe comparison
            return trim(trim($original_plaintext) . "\n");
        } else {
            return false;
        }
    }
}