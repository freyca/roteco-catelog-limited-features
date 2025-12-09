@php
    $path = match (true) {
        get_class($product) === 'App\Models\ProductSparePart' => '/pieza-de-repuesto',
        default => '/producto',
    };
@endphp

<div
    class="bg-white rounded-lg overflow-hidden group transition-shadow duration-300 hover:shadow-2xl flex flex-col justify-between h-full py-2">
    <a href="{{ $path . '/' . $product->slug }}" class="">
        <div class="relative pb-[112.5%] overflow-hidden rounded-md border border-solid border-primary-800">
            <img class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 transform group-hover:scale-110"
                src="{{ @asset('/storage/' . $product->main_image) }}" alt="{{ $product->name }}">
            <div
                class="absolute inset-0 bg-gradient-to-b from-slate-400 to-transparent bg-opacity-70 flex items-center justify-center text-center opacity-0 group-hover:opacity-100 transition duration-300">
                <div class="mx-auto text-center">
                    <p class="text-white bg-black rounded-md py-2 opacity-80 m-4 text-sm sm:text-base">
                        {{ Str::limit($product->slogan, 60) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="md:py-4 md:px-2 sm:px-0 flex-grow flex flex-col justify-between">
            <div class="grid grid-cols-3 mt-auto overflow-hidden md:gap-2">
                <div class="col-span-3 md:col-span-2 my-1 text-center md:text-left">
                    <h3 class="text-sm text-primary-800 truncate md:text-wrap">
                        {{ $product->name }}
                    </h3>
                </div>

                {{--
                <div class="text-nowrap col-span-3 md:col-span-1 md:text-right">
                    @if ($product->price_with_discount)
                        <p class="text-md text-center font-semibold text-sm bg-danger-500 rounded-md py-1">
                            <span class="p-2 text-primary-100">
                                {{ $product->getFormattedPriceWithDiscount() }}
                            </span>
                        </p>
                        <p class="text-primary-800 text-center text-sm line-through">
                            {{ $product->getFormattedPrice() }}
                        </p>
                    @else
                        <p class="text-md text-center font-semibold text-sm bg-primary-800 rounded-md py-1">
                            <span class="p-2 text-primary-100">
                                {{ $product->getFormattedPrice() }}
                            </span>
                        </p>
                    @endif
                </div>
                @livewire('buttons.add-to-cart', ['product' => $product])
                --}}
            </div>
        </div>
    </a>
</div>
