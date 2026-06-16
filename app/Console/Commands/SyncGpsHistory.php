<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SyncGpsHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync buffered GPS location data from Redis to MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $redisKey = 'gps_buffer';

        // Atomically pop all current items from the list
        // Using LPOP with count (supported in Redis 6.2+) or a transaction.
        // For compatibility and atomicity without blocking, we can rename the key 
        // to a processing key, then operate on that processing key.

        $processingKey = $redisKey . '_processing_' . time();
        
        // Rename the key. If it doesn't exist, this will throw an exception, so check first.
        if (!Redis::exists($redisKey)) {
            $this->info('No GPS data to sync.');
            return;
        }

        try {
            Redis::rename($redisKey, $processingKey);
        } catch (\Exception $e) {
            $this->info('No GPS data to sync or rename failed.');
            return;
        }

        $items = Redis::lrange($processingKey, 0, -1);
        
        if (empty($items)) {
            Redis::del($processingKey);
            $this->info('No GPS data to sync.');
            return;
        }

        $records = [];
        foreach ($items as $item) {
            $data = json_decode($item, true);
            if ($data) {
                $records[] = $data;
            }
        }

        // Bulk insert to MySQL
        if (count($records) > 0) {
            // Process in chunks to avoid max placeholder limits in MySQL
            $chunks = array_chunk($records, 500);
            foreach ($chunks as $chunk) {
                DB::table('gps_history')->insert($chunk);
            }
            $this->info(count($records) . ' GPS records successfully synced to DB.');
        }

        // Clean up the processing list
        Redis::del($processingKey);
    }
}
