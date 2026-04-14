<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\StorageService;

class InitializeUserStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize storage_used dan storage_limit untuk semua existing users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Initializing storage for existing users...');

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Set storage limit berdasarkan subscription plan
            StorageService::updateStorageLimit($user);

            // Initialize storage_used jika masih 0
            if ($user->storage_used == 0) {
                // Bisa di-improve untuk calculate actual storage dari files yang sudah ada
                // Tapi untuk sekarang set ke 0
                $user->update(['storage_used' => 0]);
            }

            $count++;
            $this->line("✓ User: {$user->email} - Plan: {$user->subscription_plan} - Limit: " . StorageService::formatBytes($user->storage_limit));
        }

        $this->info("✓ Initialized storage for {$count} users");

        return Command::SUCCESS;
    }
}
