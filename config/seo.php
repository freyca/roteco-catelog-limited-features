<?php

/**
 * Every index of the array needs two values:
 * title: this will be the webpage title
 * description: this will be the meta description tag content
 *
 * Array can have additional elements
 * Each one of the elements will be rendered as a meta tag in head
 * This meta tag wil have the key as name and the value as content
 */
return [
    // This tags are always written in head
    // Its values can be overwriten in every page
    'default' => [
        'robots' => 'index, follow',
        'author' => 'Fran Rey - franreycastedo@gmail.com',
    ],

    // Use this if you want to reaturn a view and do not want to be indexed
    'noindex' => [
        'title' => env('APP_NAME', ''),
        'description' => env('APP_DESCRIPTION', ''),
        'robots' => 'noindex, nofollow',
    ],

    'index' => [
        'title' => 'Meta titulo de la página de inicio',
        'description' => 'Meta descripción de la página de categorías',
    ],

    'product_all' => [
        'title' => '',
        'description' => '',
    ],

    'complements_all' => [
        'title' => '',
        'description' => '',
    ],

    'spare_parts_all' => [
        'title' => '',
        'description' => '',
    ],

    'categories' => [
        'title' => '',
        'description' => '',
    ],

    'contact' => [
        'title' => '',
        'description' => '',
    ],

    'about_us' => [
        'title' => '',
        'description' => '',
    ],

    'privacy_policy' => [
        'title' => '',
        'description' => '',
    ],
];
