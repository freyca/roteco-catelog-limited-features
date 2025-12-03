# Requirements
 - php8.2+
 - node20
 - npm10

# Deploy
 - composer install
 - npm install
 - npm run build
 - cp .env.example .env
 - php artisan key:generate
 - php artisan storage:link
 - Ensure dirs exists
   - public/storage/product-images
   - public/storage/category-images

# TODO:
 - keep filter sidebar if is open (must convert open/close button to livewire)
 - create listeners for outofstock events
 - create listener for user created - IMPORTANT

 IMPORTANT: maybe susbtract order stock in OrderProduct and not in Order
 This could be more eficient, since we do not have to wait for queue and we always have previous state

 ## Metodos de pago
 - sequra para financiación

 ## Metodos de envio
 - Directamente a través do proveedor: url + numero seguimiento

 ## Buscador
 - no busca nombres de variantes -esperable-

