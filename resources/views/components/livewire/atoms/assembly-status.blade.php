<div>
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox"
            value=""
            class="sr-only peer"
            checked
            @if ($mandatoryassembly)
                disabled
            @endif
            wire:click="toggleAssemble"
        >

        <div class="relative w-11 h-6 bg-primary-400 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-primary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all @if($mandatoryassembly) {{'peer-checked:bg-primary-300'}} @else {{'peer-checked:bg-primary-800'}} @endif"></div>

        <span class="ms-3 font-semibold text-primary-800">
            {{__('Assembly') . ':'}}
                @if ($mandatoryassembly)
                    {{'(' . __('mandatory') . ')'}}
                @endif
            {{$assemblyPrice}}
        </span>
    </label>
</div>
