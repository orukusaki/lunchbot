<?php
namespace RgpJones\Lunchbot\Command;

use RgpJones\Lunchbot\Command;
use DateTime;
use RgpJones\Lunchbot\Slack;
use RgpJones\Lunchbot\Rota as RotaService;

class Cancel implements Command
{
    /**
     * @var RotaService
     */
    protected $rota;

    /**
     * @var Slack
     */
    private $slack;

    public function __construct(RotaService $rota, Slack $slack)
    {
        $this->rota = $rota;
        $this->slack = $slack;
    }

    public function getUsage()
    {
        return '`cancel` [date]: Cancel lunchclub for today, or on date specified';
    }

    public function run(array $args, $username)
    {
        $date = isset($args[1])
            ? new DateTime($args[1])
            : new DateTime();

        if ($this->rota->cancelOnDate($date)) {
            $message = 'Lunchclub has been cancelled on ';
        } else {
            $message = "Couldn't cancel Lunchclub on ";
        }

        $this->slack->send($message . $date->format('l, jS F Y'));
    }
}
