<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProductDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class DeleteProductImages implements ShouldQueue
{
    public function handle(ProductDeleted $event): void
    {
        $product_images = $event->product->images;
        array_push($product_images, $event->product->main_image);

        /** @var string $disk_path */
        $disk_path = config('filament.default_filesystem_disk');
        $disk = Storage::disk($disk_path);

        $disk->get('.');
        foreach ($product_images as $image) {
            if ($disk->exists($image)) {
                $disk->delete($image);
            }
        }
    }
}
