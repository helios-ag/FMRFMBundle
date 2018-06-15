<?php

namespace FM\RFMBundle;

use FM\RFMBundle\DependencyInjection\Compiler\TwigFormPass;
use FM\RFMBundle\DependencyInjection\FMRFMExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RFMBundle.
 */
class FMRFMBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TwigFormPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new FMRFMExtension();
    }
}