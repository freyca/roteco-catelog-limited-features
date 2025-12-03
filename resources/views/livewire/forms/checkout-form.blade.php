@inject('cart', 'App\Services\Cart')

<div class="mx-auto pt-10 max-w-4xl flex-1 space-y-6 xl:mb-2 lg:w-full">

    <p class="py-2 text-2xl font-semibold text-center text-primary-900 ">
        {{ __('User details') }}
    </p>

    <form wire:submit="create">
        {{ $this->form }}

        <div class="rounded-lg border bg-white p-2 md:p-6 space-x-6 mt-10">
            <div class="space-y-3">
                @if ($cart->getTotalDiscount() > 0)
                    <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-primary-500">
                            {{ __('Price') }}
                        </dt>
                        <dd class="text-base line-through font-medium text-primary-900">
                            {{ $cart->getTotalCostWithoutDiscount(true) }}
                        </dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4">
                        <dt class="text-base font-normal text-primary-500">
                            {{ __('Savings') }}
                        </dt>
                        <dd class="text-base font-bold text-success-600">
                            - {{ $cart->getTotalDiscount(true) }}
                        </dd>
                    </dl>
                @endif

                <dl class="flex items-center justify-between gap-4">
                    <dt class="text-base font-bold text-primary-900">
                        {{ __('Without taxes') }}
                    </dt>
                    <dd class="text-base font-bold text-primary-900">
                        {{ $cart->getTotalCostWithoutTaxes(true) }}
                    </dd>
                </dl>

                <dl class="flex items-center justify-between gap-4 @if ($cart->getTotalDiscount() > 0) {{ 'border-t border-primary-200 pt-2' }} @endif">
                    <dt class="text-base font-medium text-primary-700">
                        {{ __('Total') }}
                    </dt>
                    <dd class="text-base font-medium text-primary-700">
                        {{ $cart->getTotalCost(true) }}
                    </dd>
                </dl>

                <div class="space-y-3">
                    <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-500">
                            {{ __('Place order') }}
                        </a>
                    </button>

                    <div class="flex items-center justify-center gap-2">
                        <span class="text-sm font-normal text-primary-500"> {{ __('or') }} </span>
                        <a href="/" title=""
                            class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline">
                            {{ __('Continue shopping') }}
                            @svg('heroicon-s-arrow-right-circle', 'w-5 h-5')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="space-y-4 rounded-lg border border-primary-200 bg-white p-4 shadow-sm sm:p-6"> --}}
        {{--    <form class="space-y-4"> --}}
        {{--        <div> --}}
        {{--            <label for="voucher" class="mb-2 block text-sm font-medium text-primary-900"> --}}
        {{--                Do you have a voucher or gift card? --}}
        {{--            </label> --}}
        {{--            <input type="text" id="voucher" --}}
        {{--                class="block w-full rounded-lg border border-primary-300 bg-primary-50 p-2.5 text-sm text-primary-900 focus:border-primary-500 focus:ring-primary-500" --}}
        {{--                placeholder="" required /> --}}
        {{--        </div> --}}
        {{-- --}}
        {{--        <button type="submit" --}}
        {{--            class="flex w-full items-center justify-center rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300"> --}}
        {{--            Apply Code --}}
        {{--        </button> --}}
        {{--    </form> --}}
        {{-- </div> --}}

    </form>

    <x-filament-actions::modals />
</div>