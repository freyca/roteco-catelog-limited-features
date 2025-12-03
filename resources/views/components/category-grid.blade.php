<div class="container mx-auto py-8">
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
        @foreach ($categories as $category)
            <x-category.category-card :category="$category" />
        @endforeach
    </div>
</div>
