<?php

namespace App\Helpers;

class FuzzyLogic
{
    private static string $_basic_path = 'fuzzy-logic/categories-data/';

    private static function getValues(string $category) : array
    {
        $filePath = base_path(static::$_basic_path.'/'.$category.'.json');

        $fileContents = file_get_contents($filePath);

        return json_decode($fileContents, true) ?? [];
    }

    private static function stringify(string $raw_text) : string
    {
        $result = '';

        switch ($raw_text)
        {
            case 'low': $result = 'Низький'; break;
            case 'moderate': $result = 'Середній'; break;
            case 'high': $result = 'Високий'; break;
            case 'critical': $result = 'Критичний'; break;
        }

        return $result . ' рівень';
    }

    public static function parseValue(int $value, string $category, bool $is_raw = false) : string
    {
        $category_rows = static::getValues($category);

        $raw_result = '';

        foreach ($category_rows as $category_row)
        {
            if($category_row['value'] == $value)
            {
                $raw_result = $category_row['category'];
                break;
            }
        }

        return $is_raw ? $raw_result : static::stringify($raw_result);
    }
}
