<?php
namespace Modules\Enrollments\Services;
//C:\xampp\htdocs\Olamaa_institute\backend\Modules\Enrollments\Services
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class QrEncryptionService
{
    /**
     * Encrypt student ID deterministically
     */
    public static function encryptStudentId(int $studentId): string
    {
        $key = config('app.qr_encryption_key'); // 32-byte key
        $data = (string) $studentId;

        // Use AES-256-ECB (not recommended for large data, but OK for short IDs)
        // OR better: use SIV mode if available, but for simplicity:
        $encrypted = openssl_encrypt($data, 'AES-256-ECB', $key, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }

    /**
     * Decrypt to get student ID
     */
    public static function decryptStudentId(string $encryptedData): int
    {
        $key = config('app.qr_encryption_key');
        $decoded = base64_decode($encryptedData);
        $decrypted = openssl_decrypt($decoded, 'AES-256-ECB', $key, OPENSSL_RAW_DATA);

        if ($decrypted === false) {
            throw new RuntimeException('Invalid QR code');
        }

        return (int) $decrypted;
    }
}