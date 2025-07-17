<?php

namespace App\Services;

class TotpService
{
    private const DIGITS = 6;
    private const WINDOW = 30;
    private const TOLERANCE = 1;

    public function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public function generateCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = intval($timestamp / self::WINDOW);

        $binarySecret = $this->base32Decode($secret);
        $counterBytes = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $counterBytes, $binarySecret, true);

        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % pow(10, self::DIGITS);

        return str_pad($code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        $timestamp = time();
        $timeSlice = intval($timestamp / self::WINDOW);

        for ($i = -self::TOLERANCE; $i <= self::TOLERANCE; $i++) {
            $calculatedCode = $this->generateCodeForTimeSlice($secret, $timeSlice + $i);
            if ($calculatedCode === $code) {
                return true;
            }
        }

        return false;
    }

    public function getQrCodeUrl(string $secret, string $issuer, string $email): string
    {
        $label = rawurlencode($issuer . ':' . $email);
        return "otpauth://totp/{$label}?secret={$secret}&issuer=" . rawurlencode($issuer);
    }

    private function generateCodeForTimeSlice(string $secret, int $timeSlice): string
    {
        $binarySecret = $this->base32Decode($secret);
        $counterBytes = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $counterBytes, $binarySecret, true);

        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % pow(10, self::DIGITS);

        return str_pad($code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);

        $binaryString = '';
        for ($i = 0; $i < strlen($secret); $i++) {
            $char = $secret[$i];
            $pos = strpos($base32chars, $char);
            if ($pos !== false) {
                $binaryString .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
            }
        }

        $result = '';
        for ($i = 0; $i < strlen($binaryString); $i += 8) {
            $byte = substr($binaryString, $i, 8);
            if (strlen($byte) === 8) {
                $result .= chr(bindec($byte));
            }
        }

        return $result;
    }
}
