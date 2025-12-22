<div class="ml-3">
    <ul>
    @foreach ($relatedSpareparts as $spare_part)
        <div class="py-2 grid grid-cols-[1fr_auto] md:grid-cols-[1fr_auto_auto] gap-1 border-b border-gray-300">
            <div
                class="text-left row-start-1 md:col-auto md:row-auto flex items-center">
                {{ $spare_part->number_in_image }} - {{ $spare_part->name }} - {{ $spare_part->reference }}
            </div>

            <div
                class="mr-2 font-bold row-start-2 md:col-auto md:row-auto flex items-center">
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

            <div
                class="col-start-2 row-span-2 flex items-center w-max md:col-auto md:row-span-1 md:w-auto md:flex-none">
                @livewire('buttons.product-cart-buttons', ['product' => $spare_part, collect() ])
            </div>
        </div>
    @endforeach
    </ul>
</div>
