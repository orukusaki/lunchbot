<?php
namespace RgpJones\Lunchbot;

class Shopper
{
    private $name;

    /**
     * @var ShopperCollection
     */
    private $collection;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param ShopperCollection $colletion
     */
    public function setCollection(ShopperCollection $collection) {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function next()
    {
        if (!$this->collection) {
            throw new \InvalidArgumentException('I am not part of a collection');
        }

        return $this->collection->next($this);
    }

    public function prev()
    {
        if (!$this->collection) {
            throw new \InvalidArgumentException('I am not part of a collection');
        }

        return $this->collection->prev($this);
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
