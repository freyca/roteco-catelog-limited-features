<div>
    <p>
        <span class="text-md font-semibold p-3 px-4 mr-4 rounded text-primary-100 bg-primary-800">
            @if((isset($variant) && !is_null($variant)))
                @if ($product->price_with_discount)
                    {{ $variant->getFormattedPriceWithDiscount() }}
                @else
                    {{ $variant->getFormattedPrice() }}
                @endif
            @else
                @if ($product->price_with_discount)
                    {{ $product->getFormattedPriceWithDiscount() }}
                @else
                    {{ $product->getFormattedPrice() }}
                @endif
            @endif
        </span>

        @if($product->price_with_discount)
            @php
                $price = $product->getFormattedPrice();
            @endphp
        @endif

        @if(isset($variant) && !is_null($variant) && $variant->price_with_discount)
            @php
                $price = $variant->getFormattedPrice();
            @endphp
        @endif

        @if (isset($price))
            <span class="pr-2 text-primary-800">
                {{ __('Before') . ': ' }}
                <span class="line-through font-semibold">
                    {{ $price }}
                </span>
            </span>
        @endif
    </p>
</div>