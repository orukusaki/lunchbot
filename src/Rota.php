<?php
namespace RgpJones\Lunchbot;

use DateInterval;
use DateTime;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use JsonSerializable;

/**
 * Represents the next 7 working days of rota'd shopping
 * @package RgpJones\Lunchbot
 */
class Rota implements JsonSerializable
{
    private $shoppers;

    private $interval;

    private $currentRota;

    private $dateValidator;

    const DAYS = 7;

    public function __construct(ShopperCollection $shoppers, DateValidator $dateValidator, array $currentRota = [])
    {
        $this->shoppers = $shoppers;
        $this->interval = new DateInterval('P1D');
        $this->dateValidator = $dateValidator;

        foreach ($currentRota as $date => $name) {
            $this->currentRota[] = $shoppers->get($name);
        }

        $this->generate();
    }

    private function generate()
    {
        $daysToGenerate = self::DAYS - count($this->currentRota);
        $date = new \DateTimeImmutable(end(array_keys($this->currentRota)));
        $shopper = $this->currentRota[$this->getDateKey($date)];

        while ($daysToGenerate--) {

            do {
                $date = $date->modify('next working day');
            } while ($this->dateIsCancelled($date));

            $shopper = $shopper->next();

            $this->currentRota[$this->getDateKey($date)] = $shopper->getName();
        }
    }

    public function getCurrentRota()
    {
        return $this->currentRota;
    }

    public function getShopperForDate(DateTime $date)
    {
        $this->generate();

        if (!isset($this->currentRota[$this->getDateKey($date)])) {
            throw new InvalidArgumentException('Shopper for that date us unknown');
        }

        return $this->currentRota[$this->getDateKey($date)];
    }

    public function skipShopperForDate(DateTime $date)
    {
        $skipDate = $this->getDateKey($date);

        reset($this->currentRota);

        while (key($this->currentRota) != $skipDate) {
            next($this->currentRota);
        }
        do {
            $this->currentRota[key($this->currentRota)] = current($this->currentRota)->next();
        } while (next($this->currentRota) !== false);
    }

    public function cancelOnDate(DateTime $cancelDate)
    {
        if ($this->dateValidator->isDateValid($cancelDate)) {
            $date = clone $cancelDate;
            if (isset($this->currentRota[$this->getDateKey($date)])) {
                $shopper = $this->currentRota[$this->getDateKey($date)];
                unset($this->currentRota[$this->getDateKey($date)]);
                while (isset($this->currentRota[$this->getDateKey($date->add($this->interval))])) {
                    $nextShopper = $this->currentRota[$this->getDateKey($date)];
                    $this->currentRota[$this->getDateKey($date)] = $shopper;
                    $shopper = $nextShopper;
                }
                $this->currentRota[$this->getDateKey($date)] = $shopper;
            }
            $this->dateValidator->addCancelledDate($cancelDate);

            return true;
        }

        return false;
    }

    public function getNextShopper(\DateTime $date)
    {
        if (isset($this->currentRota[$this->getDateKey($date)])) {
            $shopper = $this->currentRota[$this->getDateKey($date)];
            $this->shopper->setCurrentShopper($this->currentRota[$this->getDateKey($date)]);

            return $shopper;
        } else {
            return $this->shopper->next();
        }
    }

    public function getPreviousShopper(DateTime $date)
    {
        $previousShopper = null;
        if (isset($this->currentRota[$this->getDateKey($date->sub($this->interval))])) {
            $previousShopper = $this->currentRota[$this->getDateKey($date->sub($this->interval))];
        }

        return $previousShopper;
    }

    public function getPreviousRotaDate(DateTime $date)
    {
        $rotaDates = $this->getRotaDatesWithDate($date);

        $rotaDate = null;
        $offset = array_search($date->format('Y-m-d'), $rotaDates);
        if ($offset > 0) {
            $rotaDate = new DateTime($rotaDates[$offset-1]);
        }

        return $rotaDate;
    }

    protected function getRotaDatesWithDate(DateTime $date)
    {
        $date = $date->format('Y-m-d');
        $rotaDates = array_keys($this->currentRota);
        if (!in_array($date, $rotaDates)) {
            $rotaDates[] = $date;
        }
        sort($rotaDates);

        return $rotaDates;
    }

    protected function getDateKey(DateTime $date)
    {
        return $this->dateValidator->getNextValidDate($date)->format('Y-m-d');
    }

    public function swapShopper($argument1, $argument2)
    {
        // TODO: write logic here
    }

    function jsonSerialize()
    {
        return $this->currentRota;
    }
}
