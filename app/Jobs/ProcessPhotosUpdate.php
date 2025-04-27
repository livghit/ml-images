<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessPhotosUpdate implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    private array $photos;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $photos
    ) {
        $this->photos = $photos;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->photos as $photo) {
            try {
                $path = $photo->storeAs('photos', $photo->getClientOriginalName(), 'photos');

                Log::info("Photo {$photo->getClientOriginalName()} stored at: $path");
            } catch (\Exception $e) {
                Log::error("Failed to process photo {$photo->getClientOriginalName()}: " . $e->getMessage());
            }
        }
    }
}
