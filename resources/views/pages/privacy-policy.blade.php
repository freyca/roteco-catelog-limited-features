<x-layouts.app :seotags="$seotags">

    <x-bread-crumbs :breadcrumbs="$breadcrumbs" />

    <div class="container mx-auto p-4">
        <div class="grid gap-4">
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-4">
                <h2 class="text-xl font-bold mb-2">
                    Nuestra Historia
                </h2>

                <p class="text-primary-700">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vel urna quis urna fermentum
                    bibendum.
                </p>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden p-4">
                <h2 class="text-xl font-bold mb-2">
                    Nuestro Equipo
                </h2>
                <p class="text-primary-700">
                    Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
            </div>


            <div class="container mx-auto p-4 bg-white rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-2">
                    About Us
                </h2>

                <p class="text-primary-700 mb-4">
                    We are a company dedicated to providing the best products and services to our
                    customers...
                </p>

                <div class="flex space-x-4">
                    <img src="path/to/image1.jpg" alt="Team member 1" class="w-1/4 rounded-lg">
                    <img src="path/to/image2.jpg" alt="Team member 2" class="w-1/4 rounded-lg">
                    <img src="path/to/image3.jpg" alt="Team member 3" class="w-1/4 rounded-lg">
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
