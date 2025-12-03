<?php

namespace Database\Seeders;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Category;
use App\Models\Disassembly;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductSparePart;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // For convenience, all categories and products has the same image
        // We hardcode it here and, if it not exists, we create it
        $imageName = 'sample-image.png';
        $this->generateImage(config('custom.product-image-storage'), $imageName);
        $this->generateImage(config('custom.category-image-storage'), $imageName);

        // Create minimal product catalog (fast seeding)
        // 2 categories × 3 products × 2 disassemblies × 3 spare parts = 36 total
        Category::factory(2)
            ->has(
                Product::factory(3)
                    ->has(
                        Disassembly::factory(2)
                            ->has(ProductSparePart::factory(3))
                    )
            )
            ->create();

        // Create customers with addresses and minimal orders
        // 5 customers × 3 addresses × 2 orders × 1 product = fast
        // Disable Order events to prevent notifications during seeding
        Order::withoutEvents(function () {
            for ($counter = 0; $counter < 5; $counter++) {
                $user = User::factory()->customer()->create();

                Address::factory(3)->for($user)->create();

                Order::factory(2)
                    ->for($user)
                    ->has(OrderProduct::factory(1))
                    ->create();
            }
        });

        // Create admin user if not exists
        if (User::where('email', 'fran@gmail.com')->doesntExist()) {
            $admin = User::factory()
                ->admin()
                ->create([
                    'name' => 'Fran',
                    'surname' => 'Rey Castedo',
                    'email' => 'fran@gmail.com',
                ]);

            Address::factory()
                ->for($admin)
                ->create([
                    'address_type' => AddressType::Shipping,
                    'name' => 'Francisco',
                    'surname' => 'Rey Castedo',
                    'email' => 'franreycastedo@gmail.es',
                    'financial_number' => '00000000F',
                    'phone' => '617547428',
                    'address' => 'Lamas de prado 86',
                    'city' => 'Lugo',
                    'state' => 'Galiza',
                    'zip_code' => 27004,
                    'country' => 'España',
                ]);
        }
    }

    private function generateImage(string $path, string $imageName): void
    {
        $filePath = $path.'/'.$imageName;

        if (Storage::disk('public')->exists($filePath)) {
            return;
        }

        // Create a simple placeholder image using GD library
        $image = $this->createPlaceholderImage();

        // Save to temporary location
        $tempFile = tmpfile();
        imagepng($image, stream_get_meta_data($tempFile)['uri']);
        imagedestroy($image);

        // Put to Storage
        $imageContent = file_get_contents(stream_get_meta_data($tempFile)['uri']);
        Storage::disk('public')->put($filePath, $imageContent);
    }

    private function createPlaceholderImage(int $width = 200, int $height = 200): \GdImage
    {
        $image = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($image, 220, 220, 220);
        $textColor = imagecolorallocate($image, 100, 100, 100);

        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);

        // Add border
        imagerectangle($image, 0, 0, $width - 1, $height - 1, $textColor);

        // Add placeholder text
        $text = 'Roteco';
        $fontSize = 5;
        $textX = ($width - strlen($text) * imagefontwidth($fontSize)) / 2;
        $textY = ($height - imagefontheight($fontSize)) / 2;
        imagestring($image, $fontSize, (int) $textX, (int) $textY, $text, $textColor);

        return $image;
    }
}
