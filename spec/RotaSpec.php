<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopper;
use Storage;

class RotaSpec extends ObjectBehavior
{
    function it_generates_rota_for_next_5_days()
    {
        $this->beConstructedWith(
            new Shopper(['Alice', 'Bob', 'Chris', 'Dave'], 'Dave')
        );

        $this->generateRota(new \DateTime('2010-01-11'), 5)->shouldReturn(
            [
                '2010-01-11' => 'Alice',
                '2010-01-12' => 'Bob',
                '2010-01-13' => 'Chris',
                '2010-01-14' => 'Dave',
                '2010-01-15' => 'Alice'
            ]
        );
    }

    function it_generates_rota_for_next_5_days_and_skip_weekend()
    {
        $this->beConstructedWith(
            new Shopper(['Alice', 'Bob', 'Chris', 'Dave'], 'Dave')
        );

        $this->generateRota(new \DateTime('2010-01-01'), 5)->shouldReturn(
            [
                '2010-01-01' => 'Alice',
                '2010-01-04' => 'Bob',
                '2010-01-05' => 'Chris',
                '2010-01-06' => 'Dave',
                '2010-01-07' => 'Alice'
            ]
        );
    }
}
