<?php

namespace App\Console\Commands;

use App\Models\RajaongkirCity;
use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

/**
 * Sync data kelurahan dari api.co.id ke database lokal
 * Jalankan sekali: php artisan rajaongkir:sync
 */
class SyncRajaOngkirData extends Command
{
    protected $signature   = 'rajaongkir:sync {--limit=50 : Jumlah hasil per keyword}';
    protected $description = 'Sync data kelurahan Indonesia dari api.co.id ke database lokal';

    // Keyword untuk scrape kelurahan populer se-Indonesia
    protected array $keywords = [
        // Jawa
        'jakarta','bandung','surabaya','semarang','yogyakarta','solo','malang',
        'bekasi','depok','tangerang','bogor','cimahi','tasikmalaya','cirebon',
        'sukabumi','cianjur','garut','majalengka','subang','purwakarta','karawang',
        'indramayu','kuningan','ciamis','banjar','pangandaran','magelang','salatiga',
        'pekalongan','tegal','purwokerto','kebumen','wonosobo','purworejo','cilacap',
        'klaten','boyolali','sukoharjo','karanganyar','wonogiri','sragen','kudus',
        'jepara','demak','kendal','batang','brebes','blora','rembang','pati',
        'grobogan','banjarnegara','purbalingga','banyumas','pemalang','temanggung',
        'sidoarjo','gresik','mojokerto','pasuruan','probolinggo','blitar','kediri',
        'madiun','jember','banyuwangi','lumajang','bondowoso','situbondo','jombang',
        'nganjuk','tulungagung','trenggalek','pacitan','ponorogo','ngawi','magetan',
        'bojonegoro','lamongan','tuban','bangkalan','sampang','pamekasan','sumenep',
        // Sumatera
        'medan','binjai','pematangsiantar','tebing tinggi','padang','bukittinggi',
        'padangpanjang','payakumbuh','solok','pekanbaru','dumai','batam','jambi',
        'palembang','lubuklinggau','prabumulih','bengkulu','bandar lampung','metro',
        'banda aceh','langsa','lhokseumawe',
        // Kalimantan
        'pontianak','singkawang','banjarmasin','banjarbaru','samarinda','balikpapan',
        'bontang','tarakan','palangka raya',
        // Sulawesi
        'makassar','palopo','parepare','manado','bitung','kotamobagu','tomohon',
        'palu','kendari','gorontalo','mamuju',
        // Bali & NTB/NTT
        'denpasar','badung','gianyar','tabanan','buleleng','karangasem',
        'mataram','bima','kupang',
        // Maluku & Papua
        'ambon','ternate','jayapura','sorong','manokwari',
    ];

    public function handle(RajaOngkirService $ongkir): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Sync data kelurahan dari api.co.id (limit {$limit}/keyword)...");
        $this->newLine();

        $saved = 0;
        $seen  = [];
        $bar   = $this->output->createProgressBar(count($this->keywords));
        $bar->start();

        foreach ($this->keywords as $keyword) {
            try {
                $villages = $ongkir->searchVillages($keyword, $limit);

                foreach ($villages as $v) {
                    $code = $v['village_code'] ?? null;
                    if (!$code || isset($seen[$code])) continue;
                    $seen[$code] = true;

                    RajaongkirCity::updateOrCreate(
                        ['village_code' => $code],
                        [
                            'village_name'  => $v['village_name']  ?? '',
                            'district_name' => $v['district_name'] ?? '',
                            'city_name'     => $v['city_name']     ?? '',
                            'province'      => $v['province_name'] ?? '',
                        ]
                    );
                    $saved++;
                }

                usleep(150000); // 0.15 detik jeda agar tidak rate limit
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("  Skip '{$keyword}': " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai! {$saved} kelurahan berhasil disimpan ke database.");
        $this->line("   Autocomplete checkout sekarang menggunakan data lokal (tanpa hit API).");

        return Command::SUCCESS;
    }
}