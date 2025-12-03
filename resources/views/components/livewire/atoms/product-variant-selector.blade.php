<div>
    <label for="variants" class="mr-4">{{ __('Choose a variant') }}:</label>

    <select wire:change="variantChanged()" wire:model.change="variant_id" name="variants" id="product_variants" class="focus:border-inherit focus:outline-none focus:ring-inherit rounded-md">
        @foreach($variants as $variant)
            <option value="{{ $variant->id }}">{{$variant->name}}</option>
        @endforeach
    </select>
</div>