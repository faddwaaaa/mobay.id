<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncRajaOngkirData extends Command
{
    protected $signature   = 'rajaongkir:sync';
    protected $description = 'Seed data kota Indonesia ke database (untuk autocomplete checkout)';

    public function handle(): int
    {
        $this->info('Menyimpan data kota Indonesia ke database...');

        try {
            Artisan::call('db:seed', [
                '--class' => 'RajaongkirCitySeeder',
                '--force' => true,
            ]);

            $this->info('✅ Selesai! Data kota sudah tersedia untuk autocomplete.');
            $this->line('   Autocomplete kota di halaman checkout sekarang bisa digunakan.');

        } catch (\Throwable $e) {
            $this->error('❌ Gagal: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}