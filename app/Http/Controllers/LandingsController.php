<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Factories\BreadCrumbs\StandardPageBreadCrumbs;
use Illuminate\View\View;

class LandingsController extends Controller
{
    public function aboutUs(): View
    {
        return view('pages.about-us', [
            'seotags' => new SeoTags('about_us'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('About us') => route('about-us'), // @phpstan-ignore-line
            ]),
        ]);
    }

    public function privacyPolicy(): View
    {
        return view('pages.privacy-policy', [
            'seotags' => new SeoTags('privacy_policy'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Privacy policy') => route('privacy-policy'), // @phpstan-ignore-line
            ]),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'seotags' => new SeoTags('contact'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Contact us') => route('contact'), // @phpstan-ignore-line
            ]),
        ]);
    }
}
