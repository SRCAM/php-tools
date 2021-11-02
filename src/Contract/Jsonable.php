<?php


namespace saber\PhpTools\Contract;


interface Jsonable
{
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string;
}