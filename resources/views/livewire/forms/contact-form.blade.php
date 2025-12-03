<div>
    <form wire:submit="submit" class="col-span-5 mt-10 mb-8">
        @if (session()->has('contactFormSuccess'))
            <div class="mb-10 mx-auto text-center">
                <p class="text-primary-500 font-semibold mb-1">{{ __('We have received your message') }}</p>
                <p class="text-primary-800 font-semibold mb-1">{{ __('We will answer back as soon as possible') }}</p>
            </div>
        @endif

        {{ $this->form }}

        <div class="mt-10">
            <button type="submit"
                class="flex w-full items-center justify-center rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-500">
                {{ __('Send') }}
            </button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
