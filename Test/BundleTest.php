<?php
// The condition is needed for testing the bundle against Symfony versions 2.3.* and 3.0.*
if (!class_exists('\PHPUnit_Framework_TestCase') &&
    class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

class BundleTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public function testInitBundle()
    {
        $client = static::createClient();

        $container = $client->getContainer();

        // Test if the service exists
        $this->assertTrue($container->has('white_october_breadcrumbs.helper'));

        $service = $container->get('white_october_breadcrumbs.helper');
        $this->assertInstanceOf(\WhiteOctober\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper::class, $service);
    }

    public static function getKernelClass()
    {
        return \WhiteOctober\BreadcrumbsBundle\Test\AppKernel::class;
    }
}
