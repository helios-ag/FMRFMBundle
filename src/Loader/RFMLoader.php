<?php

namespace FM\RFMBundle\Loader;

use FM\RFMBundle\Configuration\RFMConfigurationReader;
use RFM\Application;
use RFM\Repository\Local\Storage;
use RFM\Api\LocalApi;

/**
 * Class RFMLoader.
 */
class RFMLoader
{

    /** @var string */
    protected $instance;

    /** @var RFMConfigurationReader  */
    protected $configurationReader;

    /**
     * RFMLoader constructor.
     * @param RFMConfigurationReader $configurationReader
     */
    public function __construct(RFMConfigurationReader $configurationReader)
    {
        $this->configurationReader = $configurationReader;
    }

    public function load()
    {
        $app = new Application();
        $local = new Storage($this->configurationReader->getConfiguration($this->instance));
        $app->setStorage($local);
        $app->api = new LocalApi();

        $app->run();
    }

    /**
     * @param string $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }
}