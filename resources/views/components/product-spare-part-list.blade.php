<div class="ml-3">
    <ul>
    @foreach ($relatedSpareparts as $spare_part)
        <li class="p-1 flex gap-2 items-center">
            <div class="flex-1 flex items-center text-left">⚙️ - {{ $spare_part->name }}</div>
                @if($spare_part->price_with_discount)
                    <div class="flex items-center text-left font-bold text-green-600">
                    {{ $spare_part->getFormattedPriceWithDiscount() }}
                @else
                    <div class="flex items-center text-left font-bold">
                    {{ $spare_part->getFormattedPrice() }}
                @endif
            </div>
            @livewire('buttons.product-cart-buttons', ['product' => $spare_part, collect() ])
        </li>
    @endforeach
    </ul>
</div>
