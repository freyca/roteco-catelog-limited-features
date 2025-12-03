<form wire:submit="add">
    <button type="submit"
        @class([
            'inline',
            'text-primary-100',
            'text-sm',
            'p-2',
            'px-4',
            'rounded',
            'shadow' => $product->stock > 0,
            'border-2' => $product->stock > 0,
            'border-primary-800' => $product->stock > 0,
            'bg-white' => $product->stock > 0,
            'hover:bg-primary-200' => $product->stock > 0,
            'bg-gray-300' => $product->stock <= 0,
            ])
        {{--
        @if($product->stock <= 0) {{ 'disabled' }} @endif
         --}}
    >

        @php
            // $icon = $this->product->stock > config('custom.stock-safety') ? 'heroicon-o-shopping-bag' : 'heroicon-m-x-mark'
            $icon = 'heroicon-o-shopping-bag'
        @endphp

        <span wire:loading.remove class="flex items-center whitespace-nowrap text-primary-800 font-semibold text-md">
            @svg( $icon, 'w-5 h-5') &nbsp;
            {{--
            @if ($product->stock > 0)
            --}}
                {{ '' }}
            {{--
            @else
                {{ __('Not enough stock' )}}
            {{--
            @endif
            --}}
        </span>

        <span wire:loading class="flex items-center whitespace-nowrap text-primary-800 font-semibold text-md">
            {{ '...' }}
        </span>
    </button>
</form>