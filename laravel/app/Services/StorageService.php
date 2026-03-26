<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * StorageService - Mengelola dan memvalidasi kapasitas penyimpanan user
 * 
 * Kapasitas per tipe subscription:
 * - Free: 20 MB (20971520 bytes)
 * - Pro: 1 GB (1073741824 bytes)
 */
class StorageService
{
    // Storage limits dalam bytes
    const FREE_STORAGE_LIMIT = 20971520;   // 20 MB
    const PRO_STORAGE_LIMIT = 1073741824;  // 1 GB

    /**
     * Dapatkan storage limit berdasarkan subscription plan
     */
    public static function getStorageLimit(User $user): int
    {
        if ($user->isPro()) {
            return self::PRO_STORAGE_LIMIT;
        }
        return self::FREE_STORAGE_LIMIT;
    }

    /**
     * Update storage limit user berdasarkan subscription plan
     */
    public static function updateStorageLimit(User $user): void
    {
        $newLimit = self::getStorageLimit($user);
        $user->update(['storage_limit' => $newLimit]);
    }

    /**
     * Hitung sisa storage yang tersedia (bytes)
     */
    public static function getAvailableStorage(User $user): int
    {
        $available = $user->storage_limit - $user->storage_used;
        return max(0, $available);
    }

    /**
     * Hitung persentase storage yang digunakan
     */
    public static function getStoragePercentage(User $user): float
    {
        if ($user->storage_limit == 0) {
            return 0;
        }
        return round(($user->storage_used / $user->storage_limit) * 100, 2);
    }

    /**
     * Format bytes ke format yang readable (KB, MB, GB)
     */
    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Validasi apakah user bisa upload file dengan ukuran tertentu
     * 
     * @return array ['can_upload' => bool, 'message' => string, 'status' => 'success|warning|error']
     */
    public static function validateUpload(User $user, int $fileSize): array
    {
        $available = self::getAvailableStorage($user);
        $availableFormatted = self::formatBytes($available);
        $fileSizeFormatted = self::formatBytes($fileSize);
        $usagePercentage = self::getStoragePercentage($user);

        // Jika user free dan storage penuh
        if (!$user->isPro() && $available < $fileSize) {
            return [
                'can_upload' => false,
                'status' => 'error',
                'message' => "Penyimpanan Anda penuh! Upgrade ke Pro untuk mendapatkan 1 GB penyimpanan. " .
                    "Saat ini: " . self::formatBytes($user->storage_used) . " / " . self::formatBytes($user->storage_limit),
                'available' => $available,
                'usage_percentage' => $usagePercentage,
            ];
        }

        // Jika user pro dan storage penuh
        if ($user->isPro() && $available < $fileSize) {
            return [
                'can_upload' => false,
                'status' => 'warning',
                'message' => "Penyimpanan Anda hampir penuh! Gunakan ruang dengan bijak. " .
                    "Saat ini: " . self::formatBytes($user->storage_used) . " / " . self::formatBytes($user->storage_limit),
                'available' => $available,
                'usage_percentage' => $usagePercentage,
            ];
        }

        // Jika user free dan storage hampir penuh (>80%)
        if (!$user->isPro() && $usagePercentage > 80) {
            return [
                'can_upload' => true,
                'status' => 'warning',
                'message' => "⚠️ Penyimpanan Anda hampir penuh ({$usagePercentage}%). " .
                    "Sisa: {$availableFormatted}. Pertimbangkan upgrade ke Pro.",
                'available' => $available,
                'usage_percentage' => $usagePercentage,
            ];
        }

        // Jika user pro dan storage >80% penuh
        if ($user->isPro() && $usagePercentage > 80) {
            return [
                'can_upload' => true,
                'status' => 'warning',
                'message' => "⚠️ Penyimpanan Anda sudah {$usagePercentage}% penuh. " .
                    "Sisa: {$availableFormatted}.",
                'available' => $available,
                'usage_percentage' => $usagePercentage,
            ];
        }

        // Upload diizinkan
        return [
            'can_upload' => true,
            'status' => 'success',
            'message' => "Upload berhasil. Sisa penyimpanan: {$availableFormatted}",
            'available' => $available,
            'usage_percentage' => $usagePercentage,
        ];
    }

    /**
     * Tambahkan storage usage setelah file berhasil diupload
     */
    public static function addStorageUsage(User $user, int $fileSize): void
    {
        $user->increment('storage_used', $fileSize);
    }

    /**
     * Kurangi storage usage ketika file dihapus
     */
    public static function removeStorageUsage(User $user, int $fileSize): void
    {
        $newUsage = max(0, $user->storage_used - $fileSize);
        $user->update(['storage_used' => $newUsage]);
    }

    /**
     * Reset storage usage ke 0 (digunakan saat reset manual atau delete account)
     */
    public static function resetStorageUsage(User $user): void
    {
        $user->update(['storage_used' => 0]);
    }

    /**
     * Dapatkan info storage dalam format array
     */
    public static function getStorageInfo(User $user): array
    {
        return [
            'used' => $user->storage_used,
            'used_formatted' => self::formatBytes($user->storage_used),
            'limit' => $user->storage_limit,
            'limit_formatted' => self::formatBytes($user->storage_limit),
            'available' => self::getAvailableStorage($user),
            'available_formatted' => self::formatBytes(self::getAvailableStorage($user)),
            'percentage' => self::getStoragePercentage($user),
            'plan' => $user->isPro() ? 'Pro' : 'Free',
        ];
    }
}
