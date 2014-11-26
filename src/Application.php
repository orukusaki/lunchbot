<?php

namespace RgpJones\Lunchbot;

use Pimple\Container;
use Silex\Application as BaseApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application extends BaseApplication
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $app = $this;
        $app['config'] = $values['config'];

        $app->register(new PersistedThingsProvider);
        $app->register(new CommandProvider);

        $app['slack'] = function () use ($app) {
            return new Slack($app['config'], $app['debug']);
        };

        $app['date_validator'] = function () use ($app) {
            return new DateValidator($app['cancelled_dates']);
        };

        $app->post('/', function (Request $request) use ($app) {

                $argv = explode(' ', trim($request->get('text')));
                $commandName = strtolower(array_shift($argv));

                /** @var Command $command */
                $command = $app['commands']->offsetExists($commandName)
                    ? $app['commands'][$commandName]
                    : $app['commands']['help'];

                $response = $command->run($argv, $request->get('user_name'));

                return new Response($response);
            }
        );
    }
}
