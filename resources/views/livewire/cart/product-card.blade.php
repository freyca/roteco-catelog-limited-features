@inject('cart', 'App\Services\Cart')

<div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 xl:mb-2 lg:w-full">
    <div class="rounded-lg border bg-white p-2 shadow-sm md:p-6 space-x-6">
        <div class="space-y-4 grid grid-cols-3 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0 ">

            <a href="{{ $path . '/' . $product->slug }}" class="shrink-0 md:order-1">
                <img class="mx-auto h-20 w-20 xl:h-32 xl:w-32 object-contain"
                    src="{{ @asset('/storage/' . $product->disassembly->product->main_image) }}" alt=""
                />
            </a>

            <div class="ml-2 sm:ml-0 w-full min-w-0 flex-1 space-y-4 col-span-2 md:order-2 md:max-w-md">
                <div>
                    <a href="{{ $path . '/'}}{{isset($product->slug) ? $product->slug : $parent->slug}}"
                        class="text-base font-medium text-primary-900 hover:underline">
                        @if(isset($variant) && !is_null($variant))
                            <p>
                                {{ $variant->name }}
                                @if($assembly_status)
                                    <span class="text-sm font-normal">
                                        {{ ' (' . __('with assembly') . ')'}}
                                    </span>
                                @endif
                            </p>
                            <span class="text-sm text-primary-500">{{ $product->name }}</span>
                        @else
                            <p>
                                {{ $product->name }}
                                @if($assembly_status)
                                    <span class="text-sm font-normal">
                                        {{ ' (' . __('with assembly') . ')'}}
                                    </span>
                                @endif
                            </p>
                        @endif
                    </a>
                    <p class="text-base text-primary-900 truncate">
                        {{ $product->slogan }}
                    </p>
                </div>
            </div>

            <hr class="col-span-3" md:hidden>

            <div class="flex col-span-2 justify-around md:order-4 md:grid">
                <div class="flex items-center justify-between md:justify-center ">
                    <x-livewire.atoms.buttons.increment-decrement-cart
                        :product="$product"
                        :product-quantity="$quantity"
                        :assembly-status="$assembly_status"
                        :variant="$variant ?? null"
                    />
                </div>

                <div class="flex items-center gap-4">
                    <x-livewire.atoms.buttons.remove-from-cart
                        :product="$product"
                        :assembly-status="$assembly_status"
                        :variant="$variant ?? null"
                    />
                </div>
            </div>

            <div class="text-center self-center md:order-3 md:w-32">
                @php
                    if(isset($variant) && !is_null($variant)) {
                        $has_discount = ! is_null($variant->price_with_discount);
                    } else {
                        $has_discount = ! is_null($product->price_with_discount);
                    }
                @endphp
                @if ($has_discount)
                    <p class="text-base line-through font-medium text-primary-900 inline-block">
                        @if($cart->hasProduct($product, $assembly_status, $variant ?? null))
                            {{ $cart->getTotalCostforProductWithoutDiscount($product, $assembly_status, $variant, true) }}
                        @endif
                    </p>
                @endif

                    <p @class([
                        'text-base',
                        'font-bold',
                        'inline-block',
                        'text-primary-800' => !$has_discount,
                        'text-success-600' => $has_discount,
                    ])>
                        @if($cart->hasProduct($product, $assembly_status, $variant ?? null))
                            {{ $cart->getTotalCostforProduct($product, $assembly_status, $variant ?? null, true) }}
                        @endif
                    </p>

                    @if($assembly_status)
                        <p class="text-sm font-normal text-primary-900 inline-block">
                            {{'(' . __('Assembly included') . ')'}}
                        </p>
                    @endif
            </div>
        </div>
    </div>
</div>
