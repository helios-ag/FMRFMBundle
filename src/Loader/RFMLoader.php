<?php

namespace FM\RFMBundle\Loader;

use FM\RFMBundle\Configuration\RFMConfigurationReader;
use RFM\Api\AwsS3Api;
use RFM\Repository\Local\Storage as LocalStorage;
use RFM\Repository\S3\Storage as S3Storage;
use RFM\Api\LocalApi;
use Symfony\Component\HttpFoundation\Request;
use RFM\Application;

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
        $type = $this->configurationReader->getConfigurationType($this->instance);

        switch ($type) {
            case 'local':
                $local = new LocalStorage($this->configurationReader->getConfiguration($this->instance));
                $app->setStorage($local);
                $app->api = new LocalApi();
                break;
            case 's3':
                $s3 = new S3Storage($this->configurationReader->getConfiguration($this->instance));
                $app->setStorage($s3);
                $app->api = new AwsS3Api();
                break;
        }

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