<div class="location-map mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-4">{{ __('Our Location') }}</h2>
    <div id="map" class="w-full h-96 rounded-md shadow-md"></div>
</div>

<style>
    .location-map {
        padding-top: 2rem;
        /* py-8 */
        padding-bottom: 2rem;
        /* py-8 */
        margin-left: auto;
        /* mx-auto */
        margin-right: auto;
        /* mx-auto */
    }

    .location-map h2 {
        font-size: 1.5rem;
        /* text-2xl */
        font-weight: 600;
        /* font-semibold */
        margin-bottom: 1rem;
        /* mb-4 */
    }

    .location-map #map {
        width: 100%;
        height: 24rem;
        /* h-96 */
        border-radius: 0.375rem;
        /* rounded-md */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        /* shadow-md */
    }
</style>

<!-- Include Leaflet JS and CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([43.016, -7.55], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([43.016, -7.55]).addTo(map)
            .bindPopup('Here you can buy <b>Roteco</b> supplies')
            .openPopup();
    });
</script>
