<?php

namespace FM\RFMBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Registration of the extension via DI.
 *
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2018 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class FMRFMExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('form.xml');
        $container->setParameter('fm_rfm', $config);
        $container->setAlias('fm_rfm.configurator', $config['configuration_provider']);
        $container->setAlias('fm_rfm.loader', $config['loader']);
        $container->getAlias('fm_rfm.loader')->setPublic(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'https://helios-ag.github.io/schema/dic/fm_rfm';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'fm_rfm';
    }
}
