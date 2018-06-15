<?php

namespace FM\RFMBundle\Tests\DependencyInjection\Compiler;

use FM\RFMBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigFormPassTest.
 */
class TwigFormPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $pass      = new TwigFormPass();
        $pass->process($container);
        $this->assertFalse($container->hasParameter('twig.form.resources'));
        $container = new ContainerBuilder();
        $container->setParameter('twig.form.resources', array());
        $pass->process($container);
        $this->assertEquals(array(
            '@FMRFM/Form/rfm_widget.html.twig',
        ), $container->getParameter('twig.form.resources'));
    }
}
