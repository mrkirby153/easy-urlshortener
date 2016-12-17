<?php

namespace App\Console\Commands;

use App\Click;
use App\ShortenedUrl;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClickSeed extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shortener:seed {url} {amount} {batch_size=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inserts a bunch of clicks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $amount = $this->argument('amount');
        $batchSize = $this->argument('batch_size');
        $this->info("Generating " . $amount . " fake clicks");
        $shortenedUrl = ShortenedUrl::whereId($this->argument('url'))->firstOrFail();
        $totalCount = 0;
        while ($totalCount < $amount) {
            $data = [];
            $left = $amount - $totalCount;
            $this->info("$left clicks remaining");
            $it = min($batchSize, $left);
            for ($i = 0; $i < $it; $i++) {
                $totalCount++;
                array_push($data, ['id' => $this->generateId(30), 'url' => $shortenedUrl->id, 'user_agent' => 'CONSOLE']);
                if($amount > 1000){
                    if($i % 1000 == 0){
                        $this->info("Generating URL $i/$it");
                    }
                }
            }
            $this->info("Saving to database");
            $collection = collect($data);
            $chunks = $collection->chunk(300);
            $chunks = $chunks->toArray();

            $this->info("Saving in " . sizeof($chunks) . " chunks");
            foreach ($chunks as $chunk) {
                Click::insert($chunk);
            }
            $this->info("Chunks saved!");
        }
        $this->info("Saved!");
    }

    private function generateId($size) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $url = "";
        $tries = 0;
        do {
            for ($i = 0; $i < $size; $i++) {
                $url .= $chars[rand(0, strlen($chars) - 1)];
            }
            $tries += 1;
        } while (Click::whereId($url)->first() != null && $tries < 1000);
        if ($tries >= 1000) {
            return "E:MAX_TRIES_EXCEEDED";
        }
        return $url;
    }
}
