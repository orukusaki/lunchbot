<?php
namespace RgpJones\Lunchbot;

use ArrayAccess;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use JsonSerializable;

class ShopperCollection implements ArrayAccess, JsonSerializable
{
    /**
     * @var Shopper[]
     */
    private $shoppers = [];

    public function construct(array $shoppers)
    {
        foreach ($shoppers as $name) {
            $shopper = new Shopper($name);
            $shopper->setCollection($this);
            $this->shoppers[] = $shopper;
        }
    }

    public function add($name)
    {
        $this->shoppers[] = new Shopper($name);
    }

    public function offsetExists($offset) {

        return isset($this->shoppers[$this->getIndex($offset)]);
    }

    public function offsetGet($offset) {

        return $this->shoppers[$this->getIndex($offset)];
    }

    public function offsetSet($offset, $value) {

        $this->shoppers[$this->getIndex($offset)] = $value;
    }

    public function offsetUnset($offset) {

        unset($this->shoppers[$this->getIndex($offset)]);
    }

    public function next(Shopper $from)
    {
        $idx = array_search($from, $this->shoppers);

        if ($idx === false) {
            throw new InvalidArgumentException('Not part of this collection');
        }

        return $this[$idx +1];
    }

    public function prev(Shopper $from)
    {
        $idx = array_search($from, $this->shoppers);

        if ($idx === false) {
            throw new InvalidArgumentException('Not part of this collection');
        }

        return $this[$idx -1];
    }

    public function get($name)
    {
        foreach ($this->shoppers as $shopper) {
            if ($shopper->getName() == $name) {
                return $shopper;
            }
        }
    }

    /**
     * @param $offset
     * @return int
     */
    private function getIndex($offset) {
        return $offset % count($this->shoppers);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->shoppers;
    }
}
