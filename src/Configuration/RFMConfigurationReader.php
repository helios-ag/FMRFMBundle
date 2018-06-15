<?php

namespace FM\RFMBundle\Configuration;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RFMConfigurationReader.
 */
class RFMConfigurationReader
{

    /** @var array */
    protected $parameters;

    /** @var RouterInterface  */
    protected $router;

    /**
     * RFMConfigurationReader constructor.
     * @param array $parameters
     * @param RouterInterface $router
     */
    public function __construct(array $parameters, RouterInterface $router)
    {
        $this->parameters = $parameters;
        $this->router = $router;
    }

    public function getConfiguration($instance)
    {
        $rfmConfigurationParameters = $this->parameters;
        $parameters = $rfmConfigurationParameters['instances'][$instance];

        $allowed  = ['logger', 'options', 'security', 'upload', 'images', 'mkdir_mode'];
        $config = array_filter(
            $parameters,
            function ($key) use ($allowed) {
                return in_array($key, $allowed);
            },
            ARRAY_FILTER_USE_KEY
        );
        return $config;
    }

    public function getClientConfiguration($instance)
    {
        $rfmConfigurationParameters = $this->parameters;
        $parameters = $rfmConfigurationParameters['instances'][$instance];

        $config = $parameters['client_config'];
        $formatter = $config['formatter'];
        $config['formatter'] = (object)['datetime' => (object)['skeleton' => $formatter['datetime_skeleton']]];
        $config['api'] = [
            'lang' => 'php',
            'connectorUrl' => $this->router->generate('rfm_run', ['instance' => $instance], UrlGeneratorInterface::ABSOLUTE_URL),
            'requestParams' => (object)['GET' => (object)[], 'MIXED' => (object)[], 'POST' => (object)[]]
        ];

        return $config;
    }
}