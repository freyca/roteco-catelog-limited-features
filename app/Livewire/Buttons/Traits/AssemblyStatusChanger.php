<?php

declare(strict_types=1);

namespace App\Livewire\Buttons\Traits;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Cart;

trait AssemblyStatusChanger
{
    public bool $can_be_assembled;

    public bool $mandatory_assembly = false;

    public bool $assembly_status = false;

    public string $assembly_price;

    public function toggleAssemble(Cart $cart): void
    {
        $this->assembly_status = ! $this->assembly_status;

        // If we do not refresh the quantity we will see the one for the product
        // with the contrary assembly
        $this->productQuantity = $cart->getTotalQuantityForProduct($this->product, $this->assembly_status, $this->variant ?? null);
    }

    public function configureAssembly(): void
    {
        $this->determineIfCanBeAssembled();

        if ($this->can_be_assembled) {
            $this->assembly_status = $this->getAssemblyStatus();
            $this->setMandatoryAssemblyStatus();
            $this->setAssemblyPrice();
        }
    }

    private function getAssemblyStatus(): bool
    {
        // If isset by user, return status
        if (isset($this->assembly_status)) {
            return $this->assembly_status;
        }

        return match (true) {
            is_a($this->product, ProductVariant::class) => isset($this->product->product->can_be_assembled) ? $this->product->product->can_be_assembled : false,
            is_a($this->product, Product::class) => $this->product->can_be_assembled ? true : false,
            default => false,
        };
    }

    private function determineIfCanBeAssembled(): void
    {
        $this->can_be_assembled = match (true) {
            is_a($this->product, ProductVariant::class) => isset($this->product->product->can_be_assembled) ? $this->product->product->can_be_assembled : false,
            is_a($this->product, Product::class) => $this->product->can_be_assembled,
            default => false,
        };
    }

    private function setAssemblyPrice(): void
    {
        if (is_a($this->product, ProductVariant::class)) {
            /**
             * @var Product
             */
            $product = $this->product->product;

            $this->assembly_price = $product->getFormattedAssemblyPrice();

            return;
        }

        if (is_a($this->product, Product::class)) {
            $this->assembly_price = $this->product->getFormattedAssemblyPrice();

            return;
        }

        $this->assembly_price = '0';
    }

    private function setMandatoryAssemblyStatus(): void
    {
        $this->mandatory_assembly = match (true) {
            is_a($this->product, ProductVariant::class) => isset($this->product->product->mandatory_assembly) ? $this->product->product->mandatory_assembly : false,
            is_a($this->product, Product::class) => $this->product->mandatory_assembly,
            default => false,
        };
    }
}
