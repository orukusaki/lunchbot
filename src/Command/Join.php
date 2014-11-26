<?php
namespace RgpJones\Lunchbot\Command;

use RgpJones\Lunchbot\Command;
use RgpJones\Lunchbot\ShopperCollection;
use RgpJones\Lunchbot\Slack;

class Join implements Command
{
    /**
     * @var ShopperCollection
     */
    protected $shoppers;

    /**
     * @var Slack
     */
    private $slack;

    public function __construct(ShopperCollection $shoppers, Slack $slack)
    {
        $this->shoppers = $shoppers;
        $this->slack = $slack;
    }

    public function getUsage()
    {
        return '`join`: Join lunch club';
    }

    public function run(array $args, $username)
    {
        if (!isset($username)) {
            throw new \RunTimeException('No username found to join');
        }
        $this->shoppers->add($username);

        $this->slack->send("{$args['user_name']} has been added to Lunchclub");
    }
}
