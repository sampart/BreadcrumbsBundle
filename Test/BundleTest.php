<?php

class BundleTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public function testInitBundle()
    {
        $client = static::createClient();

        $container = $client->getContainer();

        // Test if your services exists
        $this->assertTrue($container->has('white_october_breadcrumbs.helper'));

        $service = $container->get('white_october_breadcrumbs.helper');
        $this->assertInstanceOf(\WhiteOctober\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper::class, $service);
    }

    public static function getKernelClass()
    {
        return \WhiteOctober\BreadcrumbsBundle\Test\AppKernel::class;
    }
}
