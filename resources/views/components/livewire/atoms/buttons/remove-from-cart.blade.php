<form wire:submit="remove">
    <button type="submit"
        class="inline shadow bg-primary-100 border-2 border-primary-800 text-primary-800 text-sm py-2 px-4 rounded hover:bg-primary-300">
        <span wire:loading.remove class="flex items-center">
            @svg('heroicon-s-trash', 'h-4 w-4') &nbsp;
        </span>

        <span wire:loading class="flex items-center whitespace-nowrap text-primary-800 font-semibold text-md">
            {{ '...' }}
        </span>
    </button>
</form>