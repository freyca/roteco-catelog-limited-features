<div
  x-data="{ open: false }"
  @keydown.escape.window="open = false"
>
  <!-- Thumbnail -->
  <div class="mx-auto max-w-lg w-full">
    <div class="relative w-full overflow-hidden pb-[112.5%] cursor-zoom-in">
      <div class="absolute inset-0" x-transition.opacity.duration.700ms>
        <img
          id="product-image"
          @click="open = true"
          class="rounded-md absolute inset-0 w-full h-full object-cover"
          src="{{ @asset('/storage/' . $image) }}"
          alt="Product image"
        />
      </div>
    </div>
  </div>

  <!-- Overlay -->
  <div
    x-show="open"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
    @click.self="open = false"
    x-cloak
  >
    <!-- Enlarged image -->
    <img
      x-transition.scale
      @click.stop="open = false"
      class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-2xl cursor-zoom-out"
      src="{{ asset('storage/'.$image) }}"
      alt="Enlarged product image"
    />
  </div>
</div>