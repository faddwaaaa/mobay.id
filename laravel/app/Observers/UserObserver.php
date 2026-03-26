<?php

namespace App\Observers;

use App\Models\User;
use App\Services\StorageService;

/**
 * UserObserver - Mengamati event pada User model
 * 
 * Handles:
 * - Update storage_limit saat subscription plan berubah
 * - Initialize storage untuk user baru
 */
class UserObserver
{
    /**
     * Handle the User "created" event.
     * Initialize storage untuk user baru
     */
    public function created(User $user): void
    {
        // Set storage limit berdasarkan subscription plan default (free)
        StorageService::updateStorageLimit($user);
    }

    /**
     * Handle the User "updated" event.
     * Update storage limit jika subscription plan berubah
     */
    public function updated(User $user): void
    {
        // Jika subscription_plan berubah, update storage limit
        if ($user->isDirty('subscription_plan')) {
            StorageService::updateStorageLimit($user);
        }
    }

    /**
     * Handle the User "deleting" event.
     * Optional: cleanup file saat user account dihapus
     */
    public function deleting(User $user): void
    {
        // Ini bisa digunakan untuk cleanup file user
        // Tapi untuk sekarang kita biarkan cascading delete dari database saja
    }
}
