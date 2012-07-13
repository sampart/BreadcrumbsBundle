<?php

namespace WhiteOctober\BreadcrumbsBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Provides an extension for Twig to output breadcrumbs
 *
 */
class BreadcrumbsExtension extends \Twig_Extension
{
    protected $container;
    protected $breadcrumbs;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->breadcrumbs = $container->get("white_october_breadcrumbs");
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            "wo_breadcrumbs"  => new \Twig_Function_Method($this, "getBreadcrumbs", array("is_safe" => array("html"))),
            "wo_render_breadcrumbs" => new \Twig_Function_Method($this, "renderBreadcrumbs", array("is_safe" => array("html"))),
        );
    }

    public function getFilters()
    {
        return array(
            "wo_is_final_breadcrumb" => new \Twig_Filter_Method($this, "isLastBreadcrumb"),
        );
    }

    /**
     * Returns the breadcrumbs object
     * 
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Renders the breadcrumbs in a list
     *
     * @return string
     */
    public function renderBreadcrumbs(array $options = array())
    {
        return $this->container->get("white_october_breadcrumbs.helper")->breadcrumbs($options);
    }

    /**
     * Checks if this breadcrumb is the last one in the collection
     *
     * @param $crumb
     * @return boolean
     */
    public function isLastBreadcrumb($crumb)
    {
        return ($this->breadcrumbs[count($this->breadcrumbs)-1] === $crumb);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "breadcrumbs";
    }
}
