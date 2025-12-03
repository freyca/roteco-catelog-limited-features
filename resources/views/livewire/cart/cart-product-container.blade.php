<div>
    @if($cart->isEmpty())
        @php
            redirect(route('checkout.cart'));
        @endphp
    @endif

    <p class="py-2 text-2xl font-semibold text-center text-primary-900 ">
        {{ __('Products') }}
    </p>

    <div>
        @foreach ($cart->getCart() as $cart_item)
            @livewire(
                'cart.product-card',
                ['order_product' => $cart_item],
                key('product-' . Str::random(5) . '-' . $cart_item->assemblyPrice() . '-' . $cart_item->productVariantId() . '-' . Str::random(5))
            )
        @endforeach
    </div>
</div>
