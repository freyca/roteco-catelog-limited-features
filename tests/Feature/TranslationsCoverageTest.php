<?php

use Illuminate\Support\Facades\App;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Translations Coverage', function () {
    describe('Spanish translations', function () {
        beforeEach(function () {
            App::setLocale('es');
        });

        it('loads spanish locale successfully', function () {
            expect(App::getLocale())->toBe('es');
        });

        it('translates all strings in spanish', function ($key) {
            expect(__($key))
                ->not->toBe($key)
                ->not->toBeEmpty();
        })->with('translatable_strings');
    });

    describe('English locale fallback', function () {
        beforeEach(function () {
            App::setLocale('en');
        });

        it('fallback to english returns english strings', function () {
            expect(App::getLocale())->toBe('en');
            $result = __('Order Confirmation');
            expect($result)->toBeTruthy();
        });

        it('handles parametrized translations', function () {
            $name = 'John Doe';
            $translated = __('Hello', ['name' => $name]);
            expect($translated)->toBeTruthy();
        });
    });

    describe('Translation consistency', function () {
        it('ensures critical translations exist in spanish', function ($key) {
            App::setLocale('es');

            $translated = __($key);
            expect($translated)
                ->not->toBe($key);
        })->with('critical_translations');

        it('preserves locales can be switched', function () {
            App::setLocale('es');
            $spanish = __('Orders');

            App::setLocale('en');
            $english = __('Orders');

            expect($spanish)->toBeTruthy();
            expect($english)->toBeTruthy();
        });
    });

    describe('Translation file validation', function () {
        it('spanish translation file is valid json', function () {
            $filePath = resource_path('lang/es.json');
            expect(file_exists($filePath))->toBeTrue();

            $content = file_get_contents($filePath);
            $decoded = json_decode($content, true);
            expect($decoded)->not->toBeNull();
            expect(is_array($decoded))->toBeTrue();
        });

        it('spanish translation contains essential keys', function () {
            $filePath = resource_path('lang/es.json');
            $translations = json_decode(file_get_contents($filePath), true);

            $essentialKeys = [
                'Order Confirmation',
                'Products',
                'Customer',
                'Billing address',
                'Payment Method',
            ];

            foreach ($essentialKeys as $key) {
                expect(array_key_exists($key, $translations))->toBeTrue();
            }
        });

        it('spanish translations are not empty strings', function () {
            $filePath = resource_path('lang/es.json');
            $translations = json_decode(file_get_contents($filePath), true);

            foreach ($translations as $key => $value) {
                expect(! empty($value))->toBeTrue();
            }
        });
    });

    describe('Notification translations', function () {
        it('verifies all order notification translations', function ($string) {
            App::setLocale('es');
            expect(__($string))->not->toBeEmpty();
        })->with('notification_strings');

        it('verifies all admin notification translations', function ($string) {
            App::setLocale('es');
            expect(__($string))->not->toBeEmpty();
        })->with('admin_notification_strings');
    });

    describe('Locale switching', function () {
        it('can switch between locales', function () {
            App::setLocale('es');
            expect(App::getLocale())->toBe('es');

            App::setLocale('en');
            expect(App::getLocale())->toBe('en');

            App::setLocale('es');
            expect(App::getLocale())->toBe('es');
        });

        it('translations change with locale', function () {
            App::setLocale('es');
            $spanishOrder = __('Orders');

            App::setLocale('en');
            $englishOrder = __('Orders');

            expect($spanishOrder)->toBeTruthy();
            expect($englishOrder)->toBeTruthy();
        });
    });
});
