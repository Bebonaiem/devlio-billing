<?php
namespace App\Services;

class TotpService
{
    private const SECRET_LENGTH = 16;

    private const TIME_STEP = 30;

    private const DIGITS = 6;

    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(): string
    {
        $secret = '';
        $chars = self::BASE32_CHARS;

        for ($i = 0; $i < self::SECRET_LENGTH; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }

        return $secret;
    }

    public function getQrCodeUrl(string $secret, string $email, string $appName): string
    {
        $otpauthUri = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&digits=%d&period=%d',
            rawurlencode($appName),
            rawurlencode($email),
            $secret,
            rawurlencode($appName),
            self::DIGITS,
            self::TIME_STEP
        );

        return sprintf(
            'https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=%s',
            rawurlencode($otpauthUri)
        );
    }

    public function verifyCode(string $secret, string $code): bool
    {
        $currentTime = time();
        $timeStep = self::TIME_STEP;

        for ($i = -1; $i <= 1; $i++) {
            $timeCounter = floor(($currentTime + ($i * $timeStep)) / $timeStep);
            $expectedCode = $this->generateCodeFromTime($secret, $timeCounter);

            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    private function generateCodeFromTime(string $secret, int $timeCounter): string
    {
        $timeBytes = pack('N*', 0).pack('N*', $timeCounter);
        $secretBytes = $this->base32Decode($secret);

        $hmac = hash_hmac('sha1', $timeBytes, $secretBytes, true);

        $offset = ord($hmac[strlen($hmac) - 1]) & 0x0F;
        $binary = (
            ((ord($hmac[$offset]) & 0x7F) << 24) |
            ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
            ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
            (ord($hmac[$offset + 3]) & 0xFF)
        );

        $otp = $binary % pow(10, self::DIGITS);

        return str_pad((string) $otp, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $input = strtoupper(rtrim($input, '='));
        $chars = self::BASE32_CHARS;
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0, $len = strlen($input); $i < $len; $i++) {
            $val = strpos($chars, $input[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    public function formatSecret(string $secret): string
    {
        return chunk_split($secret, 4, ' ');
    }
}
