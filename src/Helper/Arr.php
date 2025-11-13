<?php declare(strict_types=1);

namespace Cells\Helper;

readonly class Arr
{
    /**
     * @template K
     * @template V
     * @param array<K, array<V>> $elements
     * @return array<array<K, V>>
     */
    public static function cartesian(array $elements): array
    {
        $result = [[]];
        foreach ($elements as $key => $values) {
            $append = [];
            foreach($result as $product) {
                foreach($values as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    /**
     * @template T
     * @param array<T> $arr
     * @return T
     */
    public static function rand(array $arr)
    {
        return $arr[array_rand($arr)];
    }
}
