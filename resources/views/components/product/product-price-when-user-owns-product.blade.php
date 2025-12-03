<div class="bg-primary-200 p-4 rounded-lg w-full md:w-2/3 justify-center">
    <div class="px-2 justify-text mb-2">
        <p class="font-semibold">{{ __('Important') . ':' }}</p>
        <p>{{ __('If this complement or spare part is for a product you purchased us before you have an special price') }}</p>

        @if(! auth()->user() )
            <p>
                <span>{{ __('To enjoy this especial price login to your account') }}</span>
                <span>
                    <a class="underline font-md font-semibold" href="/user">
                        {{ __('here' )}}
                    </a>
                </span>
            </p>
        @endif
    </div>

    <div class="px-2">
        <p class="text-md font-semibold p-3 px-4 mr-4 rounded text-primary-100 bg-primary-800 inline-block">
            {{ $product->getFormattedPriceWhenUserOwnsProduct() }}
        </p>
    </div>
</div>