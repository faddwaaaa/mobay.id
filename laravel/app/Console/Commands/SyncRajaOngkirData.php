<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class SyncRajaOngkirData extends Command
{
    protected $signature   = 'rajaongkir:sync';
    protected $description = 'Sync data provinsi dan kota dari RajaOngkir ke database lokal';

    public function handle(RajaOngkirService $ongkir): int
    {
        $this->info('Sinkronisasi data RajaOngkir...');

        try {
            $this->info('  → Mengambil data provinsi...');
            $provinces = $ongkir->syncProvincesToDb();
            $this->line("     ✓ {$provinces} provinsi berhasil disimpan");

            $this->info('  → Mengambil data kota/kabupaten...');
            $cities = $ongkir->syncCitiesToDb();
            $this->line("     ✓ {$cities} kota berhasil disimpan");

            $this->newLine();
            $this->info('✅ Sinkronisasi selesai!');
            $this->line('   Autocomplete kota di checkout sekarang menggunakan data lokal.');

        } catch (\Throwable $e) {
            $this->error('❌ Gagal sync: ' . $e->getMessage());
            $this->line('   Pastikan RAJAONGKIR_API_KEY sudah diisi di .env');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}