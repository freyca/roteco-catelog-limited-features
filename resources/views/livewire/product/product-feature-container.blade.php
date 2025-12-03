<div class="my-10">

    <div class="flex justify-center items-center">
        <p class="text-center my-6 bg-primary-800 p-4 rounded-xl max-w-2xl">
            <span class="font-bold text-lg text-primary-100">
                {{ __('Technical details') }}
            </span>
        </p>
    </div>

    <div id="accordion-collapse" data-accordion="collapse" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-1">
        {{-- Get associated families --}}
        @foreach ($features->pluck('family')->unique() as $featureFamily)
            <div>
                {{-- Print name of family --}}
                <h3 class="text-center p-5 font-md border bg-primary-800 border-primary-200 mt-6 text-primary-200 font-semibold rounded rounded-md">
                    {{ __($featureFamily->value) }}
                </h3>

                @foreach ($features as $feature)
                    {{-- Print the feature only if it belogns to family --}}
                    @if ($feature->family === $featureFamily)
                        @foreach ($featureValues as $featureValue)
                            {{-- Print feature value only if belongs to feature --}}
                            @if ($featureValue->product_feature_id === $feature->id)
                                <x-product.product-feature :feature="$feature" :featureValue="$featureValue" />
                            @endif
                        @endforeach
                    @endif
                @endforeach

            </div>
        @endforeach
    </div>
</div>

