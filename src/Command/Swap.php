<?php
namespace Command;
use Command;
use RotaManager;

class Swap implements Command
{
    protected $rotaManager;
    protected $args = array();

    public function __construct(RotaManager $rotaManager, array $args = array())
    {
        $this->rotaManager = $rotaManager;
    }

    public function getUsage()
    {
        return '`swap` <name>: Swap shopping duty with <name> (to-do)';
    }

    public function run()
    {

    }
}