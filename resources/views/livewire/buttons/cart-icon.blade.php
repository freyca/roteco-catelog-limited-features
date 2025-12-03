<a href="{{ route('checkout.cart') }}">
    <button type="button" class="flex text-sm rounded-full md:me-0" id="user-menu-button" aria-expanded="false">
        <span class="sr-only">
            {{ __('Open cart') }}
        </span>
        @svg('heroicon-o-shopping-bag', 'w-8 h-8 text-primary-800 rounded')
        @if ($cartItems > 0)
            <span class="relative flex right-4 bottom-2 animate-pulse">
                <span id="cart-count"
                    class="absolute bg-primary-600 text-white rounded-full w-6 h-6 flex items-center justify-center">
                    {{ $cartItems }}
                </span>
            </span>
        @endif
    </button>
</a>
