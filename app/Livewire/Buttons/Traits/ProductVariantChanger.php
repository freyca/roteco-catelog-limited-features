<?php

declare(strict_types=1);

namespace App\Livewire\Buttons\Traits;

use App\Models\ProductVariant;
use Livewire\Attributes\On;

trait ProductVariantChanger
{
    public int $variant_id;

    public ?ProductVariant $variant;

    #[On('variant-selection-changed')]
    public function variantChanged(): void
    {
        /**
         * @var ProductVariant
         */
        $variant = ProductVariant::find($this->variant_id);

        $this->variant = $variant;
    }

    private function maybeSetVariant(): void
    {
        if (! $this->variants->count()) {
            $this->variant = null;
        }

        $this->variant = $this->variants->first();
    }
}
