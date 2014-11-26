<?php

namespace RgpJones\Lunchbot;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersistedThingsProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * @var Storage
     */
    private $storage;

    private $rota;
    private $shoppers;
    private $cancelledDates;

    public function register(Container $container) {
        $container['rota'] = function () {
            return $this->getRota();
        };

        $container['members'] = function () {
            return $this->getShoppers();
        };

        $container['cancelled_dates'] = function () {
            return $this->getCancelledDates();
        };
    }

    public function boot(Application $app)
    {
        $app->finish(
            function (Request $request, Response $response) {
                if ($response->isSuccessful()) {
                    $this->persistAllTheThings();
                }
            }
        );

        $this->storage = $app['storage'];

        $data = $this->storage->load();

        if (!isset($data['members'])) {
            $data['members'] = [];
        }

        if (!isset($data['rota'])) {
            $data['rota'] = [];
        }

        if (!isset($data['cancelledDates'])) {
            $data['cancelledDates'] = [];
        }

        $this->shoppers = new ShopperCollection($data['members']);
        $this->rota = new Rota($this->shoppers, $app['date_validator'], $data['rota']);
        $this->cancelledDates = $data['cancelledDates'];
    }

    public function persistAllTheThings()
    {
        $this->storage->save(
            [
                'shoppers'       => $this->shoppers,
                'cancelledDates' => $this->cancelledDates,
                'rota'           => $this->rota,
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getCancelledDates() {
        return $this->cancelledDates;
    }

    /**
     * @return mixed
     */
    public function getRota() {
        return $this->rota;
    }

    /**
     * @return mixed
     */
    public function getShoppers() {
        return $this->shoppers;
    }
}
