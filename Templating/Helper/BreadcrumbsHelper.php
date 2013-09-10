<?php

namespace WhiteOctober\BreadcrumbsBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper extends Helper
{
    protected $templating;
    protected $breadcrumbs;
    protected $options = array();

    /**
     * @param \Symfony\Component\Templating\EngineInterface $templating
     * @param \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs
     * @param array $options
     */
    public function __construct(EngineInterface $templating, Breadcrumbs $breadcrumbs, array $options)
    {
        $this->templating  = $templating;
        $this->breadcrumbs = $breadcrumbs;
        $this->options = $options;
    }

    /**
     * Returns the HTML for the breadcrumbs
     *
     * @param $options
     * @return string A HTML string
     */
    public function breadcrumbs(array $options = array())
    {
        $options = $this->resolveOptions($options);

        return $this->templating->render(
                $options["viewTemplate"],
                $options
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'breadcrumbs';
    }

    /**
     * Merges user-supplied options from the view
     * with base config values
     *
     * @param array $options
     * @return array
     */
    private function resolveOptions(array $options = array())
    {
        $this->options["breadcrumbs"] = $this->breadcrumbs;
        return array_merge($this->options, $options);
    }
}
