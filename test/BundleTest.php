<?php
use Nyholm\BundleTest\BaseBundleTestCase;

class BundleTest extends BaseBundleTestCase

{
    protected function getBundleClass()
    {
        return WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle::class;
    }

    public function testInitBundle()
    {
        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        // Test if you services exists
        $this->assertTrue($container->has('white_october_breadcrumbs.helper'));
        $service = $container->get('white_october_breadcrumbs.helper');
        $this->assertInstanceOf(\WhiteOctober\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper::class, $service);
    }
}
