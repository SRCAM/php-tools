<?php


namespace saber\PhpTools\Arrays;


use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use saber\PhpTools\Contract\Arrayable;
use saber\PhpTools\Contract\Jsonable;
use saber\PhpTools\Json\Json;

class Container  implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Arrayable, Jsonable
{


    protected $items = [];


    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }


    /**
     * @param mixed $offset
     * @param mixed $value
     * @return $this|void
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
        return $this;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)){
            unset($this->items[$offset]);
        }
    }


    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return  $this->items;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return Json::jsonEncode($this->items,$options);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @param $value
     * @return $this|void
     */
    public function __set($name, $value)
    {
        return $this->offsetSet($name,$value);
    }


    public function __unset($name)
    {
         $this->offsetUnset($name);
    }


}