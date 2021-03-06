<?php

class Rota
{
    private $shopper;

    private $interval;

    private $currentRota;

    public function __construct(Shopper $shopper, array $currentRota = array())
    {
        $this->shopper = $shopper;
        $this->interval = new DateInterval('P1D');
        $this->currentRota = $currentRota;
    }

    public function generate(DateTime $date, $days)
    {
        $date = clone $date;

        $rota[$this->getDateKey($date)] = $this->getNextShopper($date);
        while (count($rota) < $days) {
            $date = $date->add($this->interval);
            $rota[$this->getDateKey($date)] = $this->getNextShopper($date);
        }
        $this->currentRota = array_merge($this->currentRota, $rota);
        return $rota;
    }

    public function getCurrentRota()
    {
        return $this->currentRota;
    }

    public function getShopperForDate(DateTime $date)
    {
        if (!isset($this->currentRota[$this->getDateKey($date)])) {
            $this->generate($date, 1);
        }
        return $this->currentRota[$this->getDateKey($date)];
    }


    public function skipShopperForDate(DateTime $date)
    {
        while (isset($this->currentRota[$this->getDateKey($date)])) {
            $currentDate = clone $date;
            $this->currentRota[$this->getDateKey($currentDate)] = $this->getNextShopper($date->add($this->interval));
        }
    }

    protected function getNextShopper(\DateTime $date)
    {
        if (isset($this->currentRota[$this->getDateKey($date)])) {
            $shopper = $this->currentRota[$this->getDateKey($date)];
            $this->shopper->setCurrentShopper($this->currentRota[$this->getDateKey($date)]);
            return $shopper;
        } else {
            return $this->shopper->next();
        }
    }

    protected function getNextValidDate(DateTime $date)
    {
        while (in_array($date->format('l'), array('Saturday', 'Sunday'))) {
            $date->add($this->interval);
        }
        return $date;
    }


    protected function getDateKey(DateTime $date)
    {
        return $this->getNextValidDate($date)->format('Y-m-d');
    }


}
