<?php

declare(strict_types=1);

namespace App\Repositories\Database;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class SearchByName
{
    private static int $limit_results = 5;

    public static function search(string $search_term): array
    {
        $results['products'] = self::queryProducts($search_term, self::$limit_results);

        // Return empty array if no results found
        if ($results['products']->count() === 0) {
            return [];
        }

        return $results;
    }

    private static function queryProducts(string $search_term, int $limit_results): Collection
    {
        return self::query(Product::class, $search_term, $limit_results);
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
