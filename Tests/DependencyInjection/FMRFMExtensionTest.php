<?php

namespace FM\RFMBundle\Tests\DependencyInjection;

use FM\RFMBundle\DependencyInjection\FMRFMExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Class FMRFMExtensionTest
 */
class FMRFMExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new FMRFMExtension(),
        );
    }

    public function testServices()
    {
        $this->load();
        $this->assertContainerBuilderHasAlias('fm_rfm.configurator');
        $this->assertContainerBuilderHasService('fm_rfm.loader');
        $this->assertContainerBuilderHasService('fm_rfm.configurator.default');
        $this->assertContainerBuilderHasService('twig.extension.fm_rfm_init');
    }

    public function testMinimumConfiguration()
    {
        $this->container = new ContainerBuilder();
        $loader          = new FMRFMExtension();
        $loader->load(array($this->getMinimalConfiguration()), $this->container);
        $this->assertTrue($this->container instanceof ContainerBuilder);
    }

    protected function getMinimalConfiguration()
    {
        $yaml = <<<'EOF'
instances:
    default:
        options:
            serverRoot: true
            fileRoot: /uploads
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
