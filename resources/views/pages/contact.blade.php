<x-layouts.app :seotags="$seotags">

    <x-bread-crumbs :breadcrumbs="$breadcrumbs" />

    <div class="mx-4">
        @livewire('forms.contact-form')

        <x-location-map />
    </div>

    <x-buttons.whats-app-button />
</x-layouts.app>
