<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UniqueSlug
{
    /**
     * Turn a title into a slug that is not used yet in $table.
     *
     * @param  array<string, mixed>  $scope  extra where-conditions, e.g. ['project_id' => 3]
     */
    public static function make(string $base, string $table, array $scope = [], string $column = 'slug'): string
    {
        $root = Str::slug($base) ?: 'item';
        $slug = $root;
        $i = 2;

        while (DB::table($table)->where($scope)->where($column, $slug)->exists()) {
            $slug = $root . '-' . $i++;
        }

        return $slug;
    }
}
