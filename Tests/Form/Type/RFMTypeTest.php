<?php

namespace FM\RFMBundle\Tests\Form\Type;

use FM\RFMBundle\Form\Type\RFMType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Form\FormView;


class RFMTypeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName()
    {
        $type = new RFMType();
        $this->assertEquals('rfm', $type->getName());
    }

    public function testGetParentOld()
    {
        if (version_compare(Kernel::VERSION_ID, '20800') >= 0) {
            $this->markTestSkipped('No need to test on symfony >= 2.8');
        }
        $type = new RFMType();
        $this->assertEquals('text', $type->getParent());
    }

    public function testConfigureOptions()
    {
        if (version_compare(Kernel::VERSION_ID, '20600') < 0) {
            $this->markTestSkipped('No need to test on symfony < 2.6');
        }
        $resolver = new OptionsResolver();
        $type     = new RFMType();
        $type->configureOptions($resolver);
        $this->assertTrue($resolver->isDefined('enable'));
        $this->assertTrue($resolver->isDefined('instance'));
    }

    public function testLegacySetDefaultOptions()
    {
        if (version_compare(Kernel::VERSION_ID, '20600') >= 0) {
            $this->markTestSkipped('No need to test on symfony >= 2.6');
        }
        $resolver = new OptionsResolver();
        $type     = new RFMType();
        $type->setDefaultOptions($resolver);
        $this->assertTrue($resolver->isKnown('enable'));
        $this->assertTrue($resolver->isKnown('instance'));
    }

    public function testBuildView()
    {
        $options = array(
            'instance'   => 'default1',
            'enable'     => true,
            'homeFolder' => '/home',
        );
        $view = new FormView();
        $type = new RFMType();
        $form = $this->createMock('Symfony\Component\Form\Test\FormInterface');
        $type->buildView($view, $form, $options);
        foreach ($options as $name => $value) {
            $this->assertArrayHasKey($name, $view->vars);
            $this->assertEquals($value, $view->vars[$name]);
        }
    }
}
