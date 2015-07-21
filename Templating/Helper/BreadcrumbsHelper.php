<?php

namespace WhiteOctober\BreadcrumbsBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper extends Helper
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var array The default options load from config file
     */
    protected $options = array();

    /**
     * @param \Symfony\Component\Templating\EngineInterface $templating
     * @param \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs
     * @param array $options The default options load from config file
     */
    public function __construct(EngineInterface $templating, Breadcrumbs $breadcrumbs, array $options)
    {
        $this->templating  = $templating;
        $this->breadcrumbs = $breadcrumbs;
        $this->options = array_merge($options, array(
            'namespace' => Breadcrumbs::DEFAULT_NAMESPACE, // inject default namespace to options
        ));
    }

    /**
     * Returns the HTML for the namespace breadcrumbs
     *
     * @param array $options The user-supplied options from the view
     * @return string A HTML string
     */
    public function breadcrumbs(array $options = array())
    {
        $options = $this->resolveOptions($options);

        // Assign namespace breadcrumbs
        $options["breadcrumbs"] = $this->breadcrumbs->getNamespaceBreadcrumbs($options['namespace']);

        return $this->templating->render(
            $options["viewTemplate"],
            $options
        );
    }

    /**
     * {@inheritdoc}
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
     * @param array $options The user-supplied options from the view
     * @return array
     */
    private function resolveOptions(array $options = array())
    {
        return array_merge($this->options, $options);
    }
}
