<div class="ml-3">
    <ul>
    @foreach ($relatedSpareparts as $spare_part)
        <li class="p-1 flex gap-2 items-center">
            <div class="flex-1 flex items-center text-left">⚙️ - {{ $spare_part->name }}</div>
            <div class="flex items-center text-left font-bold">
                @if($spare_part->price_with_discount)
                    <span class="text-green-600">
                        {{ $spare_part->getFormattedPriceWithDiscount() }}
                    </span>
                    &nbsp;
                @endif
                <span @class([
                    'line-through' => $spare_part->price_with_discount
                ])>
                    {{ $spare_part->getFormattedPrice() }}
                </span>
            </div>
            @livewire('buttons.product-cart-buttons', ['product' => $spare_part, collect() ])
        </li>
    @endforeach
    </ul>
</div>
