<div class="flex flex-col gap-6">
    {{--
    @if(is_a($product, App\Models\ProductSparePart::class) || is_a($product, App\Models\ProductComplement::class))
        @if($product->price_when_user_owns_product != null)
            <x-product.product-price-when-user-owns-product :product="$product" />
        @endif
    @endif

    @if (isset($variants) && !is_null($variant))
        <x-livewire.atoms.product-variant-selector :variants="$variants" />
    @endif

    @if($can_be_assembled)
        <x-livewire.atoms.assembly-status :product="$product" :mandatoryassembly="$mandatory_assembly" :assembly-price="$assembly_price" />
    @endif


    @if (isset($variant) && !is_null($variant))
        <x-livewire.atoms.product-price :product="$product" :variant="$variant" />
    @else
        <x-livewire.atoms.product-price :product="$product" />
    @endif
 --}}
    @inject(cart, '\App\Services\Cart')

    <div class="flex">
        @if(!$cart->hasProduct($product, $assembly_status, $variant ?? null))
            <x-livewire.atoms.buttons.add-to-cart
                :product="$product"
            />
        @else
            <x-livewire.atoms.buttons.remove-from-cart
                :product="$product"
                :assembly_status="$assembly_status"
                :variant="$this->variant ?? null"
            />

            <x-livewire.atoms.buttons.increment-decrement-cart
                :product="$product"
                :product-quantity="$productQuantity"
                :assembly_status="$assembly_status"
                :variant="$this->variant ?? null"
            />
        @endif
    </div>
</div>
