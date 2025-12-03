<div class="bg-primary-800 rounded-md relative flex m-2">
    <a href="{{ $category->slug }}" class="h-full mx-auto">
        <figure>
            <picture>
                <img class="p-6 pb-12" src="{{ @asset('/storage/' . $category->big_image) }}" alt="{{ $category->name }}">
            </picture>

            <figcaption class="absolute bottom-0 right-0 min-w-full md:min-w-64 max-w-full md:max-w-32 text-center">
                <h3 class="md:text-lg md:font-semibold text-primary-800 rounded-md truncate border-2 md:border-b-4 md:border-r-4 border-primary-800 bg-white">
                    <span class="py-2 px-2">
                        {{ $category->name }}
                    </span>
                </h3>
            </figcaption>
        </figure>
    </a>
</div>