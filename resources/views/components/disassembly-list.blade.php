<div id="accordion-collapse" data-accordion="collapse">
    @php
        $counter=1; // Initialize counter for accordion items
    @endphp

    @foreach ($relatedDisassemblies as $disassembly)
        <h2 id="accordion-collapse-heading-{{ $counter }}" onclick="changeImage( {{ $disassembly }} )">
            <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl hover:bg-gray-100 gap-3" data-accordion-target="#accordion-collapse-body-{{ $counter }}" aria-expanded="false" aria-controls="accordion-collapse-body-{{  $counter }}">
                <span>{{ $disassembly->name }}</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                </svg>
            </button>
        </h2>

        <div id="accordion-collapse-body-{{ $counter }}" class="hidden" aria-labelledby="accordion-collapse-heading-{{ $counter }}">
            @php
                $relatedSpareParts=$disassembly->productSpareParts;
            @endphp

            <x-product-spare-part-list :relatedSpareparts=$relatedSpareParts />
        </div>

        @php
            $counter++
        @endphp
    @endforeach
</div>

<script>
    function changeImage( $disassembly) {
        let disassembly_image = $disassembly.main_image;

        let image_url = window.location.origin + '/storage/' + disassembly_image;

        document.getElementById('product-image').src = image_url;
    }
</script>