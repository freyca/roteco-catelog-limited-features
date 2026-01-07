<?php

return [
    /**
     * Default website title
     */
    'title' => env('APP_NAME', 'Maquinaria Roteco'),

    /**
     * Sections which will be shown in the navigation bar
     *
     * This configuration is hardly coupled with tests/Datasets/ConfigUrls.php
     * If any url is added, removed or modified here it should be in done in
     * correspondant dataset
     */
    'nav-sections' => [
        'productos' => '/productos',
        //    'complementos' => '/complementos-producto',
        //    'repuestos' => '/piezas-de-repuesto',
        'categorias' => '/categorias',
    ],

    'footer-sections' => [
        //    'contacto' => '/contacto',
        //    'sobre nosotros' => '/sobre-nosotros',
        //    'politica de privacidad' => '/politica-de-privacidad',
    ],

    /**
     * Categories to show in the main page after banner
     */
    'featured-categories' => [
        1,
        2,
        3,
    ],

    /**
     * Products to show in the main page
     * If the array is empty, it will 15 products Products table
     * Products need to be published to be shown
     * TODO: criteria to show products
     */
    'featured-products' => [
        1,
        2,
        3,
    ],

    /**
     * Directory to save product images
     */
    'product-image-storage' => '/product-images',

    /**
     * Directory to save category images
     */
    'category-image-storage' => '/category-images',

    /**
     * Taxes
     */
    'tax_iva' => 0.21,

    /**
     * Mail destination addresses
     */
    'admin_email' => env('MAIL_ADMIN_EMAIL'),

    /**
     * Web logo
     */
    'web_logo' => '/images/logo.png',
    'web_logo_alt' => 'Roteco',

    /**
     * Stock safety
     */
    'stock-safety' => 3,

    /**
     * Mail where admin notifications will be sent
     */
    'mail_admin_address' => env('MAIL_ADMIN_ADDRESS'),
];
