<?php

declare(strict_types=1);

namespace App\Repositories\Database;

use App\Models\Product;
use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use Illuminate\Database\Eloquent\Collection;

class SearchByName
{
    private static int $limit_results = 5;

    public static function search(string $search_term): array
    {
        $results['products'] = self::queryProducts($search_term, self::$limit_results);
        // $results['complements'] = self::queryProductComplements($search_term, self::$limit_results - $results['products']->count());
        // $results['spare-parts'] = self::querySpareParts($search_term, self::$limit_results - $results['products']->count() - $results['complements']->count());

        // Return empty array allows the hability to do not display the div
        // from the view without inspecting the array elements
        if (
            $results['products']->count() === 0 // &&
            // $results['complements']->count() === 0 &&
            // $results['spare-parts']->count() === 0
        ) {
            return [];
        }

        return $results;
    }

    private static function queryProducts(string $search_term, int $limit_results): Collection
    {
        return self::query(Product::class, $search_term, $limit_results);
    }

    private static function queryProductComplements(string $search_term, int $limit_results): Collection
    {
        return self::query(ProductComplement::class, $search_term, $limit_results);
    }

    private static function querySpareParts(string $search_term, int $limit_results): Collection
    {
        return self::query(ProductSparePart::class, $search_term, $limit_results);
    }

    private static function query(string $class_name, string $search_term, int $limit_results): Collection
    {
        return ($limit_results === 0)
            ? new Collection
            : $class_name::where('name', 'like', "%{$search_term}%")
                ->limit($limit_results)
                ->get();
    }
}
