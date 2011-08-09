<?php

namespace WhiteOctober\BreadcrumbsBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper extends Helper
{
    protected $templating;
    protected $breadcrumbs;

    public function __construct(EngineInterface $templating, Breadcrumbs $breadcrumbs)
    {
        $this->templating  = $templating;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Returns the HTML for the breadcrumbs
     *
     * @param $name
     * @return string A HTML string
     */
    public function breadcrumbs()
    {
        return $this->templating->render("WhiteOctoberBreadcrumbsBundle::breadcrumbs.html.twig");
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'breadcrumbs';
    }
}
