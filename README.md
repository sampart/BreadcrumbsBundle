Installation
============

  1. Add this bundle to your vendor/ dir using the vendors script:

    Add the following lines in your ``deps`` file:

        [WhiteOctoberBreadcrumbsBundle]
            git=git://github.com/whiteoctober/BreadcrumbsBundle.git
            target=/bundles/WhiteOctober/BreadcrumbsBundle

    **Or** add the following to your composer.json:

      	"whiteoctober/breadcrumbs-bundle": "master"

    If you are using Symfony 2.1, you will need to use the ``2.1`` branch:

        "whiteoctober/breadcrumbs-bundle": "2.1.x-dev"

    or the appropriate ``version=2.1`` line in your ``deps`` file.

    Run the vendors script:

        ./bin/vendors install

  2. Add the WhiteOctober namespace to your autoloader:

        // app/autoload.php
        $loader->registerNamespaces(array(
            'WhiteOctober' => __DIR__.'/../vendor/bundles',
        ));

  3. Add this bundle to your application's kernel:

        // app/AppKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
                // ...
            );
        }

  4. Configure the `white_october_breadcrumbs` service in your config.yml:

        white_october_breadcrumbs: ~


That's  it for configuration.

Usage
=====

In your application controller methods:

    public function yourAction(User $user)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // Simple example
        $breadcrumbs->addItem("Home", $this->get("router")->generate("index"));

        // Example without URL
        $breadcrumbs->addItem("Some text without link");

        // Example with parameter injected into translation "user.profile"
        $breadcrumbs->addItem($txt, $url, array("%user%" => $user->getName()));
    }

and then in your template:

    {{ wo_render_breadcrumbs() }}

or overriding some default parameters:

    {{ wo_render_breadcrumbs({separator: '>', listId: 'breadcrumbs'}) }}

The following parameters can be overriden:

    separator:          defaults to '/'
    listId:             defaults to 'wo-breadcrumbs'
    listClass:          defaults to 'breadcrumb'
    locale:             defaults to null, so the default locale is used
    translation_domain: defaults to null, so the default domain is used
   
The last item in the breadcrumbs collection will automatically be rendered
as plain text rather than a `<a>...</a>` tag.

Overriding the template
=======================

You can override the template used by copying the
`Resources/views/breadcrumbs.html.twig` file out of the bundle and placing it
into `app/Resources/WhiteOctoberBreadcrumbsBundle/views`, then customising
as you see fit.
