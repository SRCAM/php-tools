<?php


namespace saber\PhpTools\Arrays;


class Tree
{
    /**
     * 数组转树状
     * @param array $array
     * @param string $pk
     * @param string $parentKey
     * @param string $subKey
     * @return array
     */
    public static function tree(array $array, string $pk = 'id', string $parentKey = 'pid', string $subKey = '_child'): array
    {

        $list = [];
        $tree = [];
        foreach ($array as $item) {
            $list[$item[$pk]] = $item;
        }
        foreach ($list as &$item) {
            if (isset($list[$item[$parentKey]])) {
                $list[$item[$parentKey]][$subKey][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }
        return $tree;
    }


}