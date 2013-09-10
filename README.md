Installation
============

**NB** There is now no need to use the 2.1 branch when installing; the bundle
is compatible with 2.0 upwards.  Use the master branch instead.

  1. Add this bundle to your vendor/ dir using the vendors script:

    Add the following lines in your ``deps`` file:

        [WhiteOctoberBreadcrumbsBundle]
            git=git://github.com/whiteoctober/BreadcrumbsBundle.git
            target=/bundles/WhiteOctober/BreadcrumbsBundle

    and run the vendors script:

        ./bin/vendors install

    **Or** add the following to your `composer.json`:

        "whiteoctober/breadcrumbs-bundle": "dev-master"

    and run:

        php composer.phar install

    The bundle is compatible with Symfony 2.0 upwards.


  2. If you're not using Composer, add the WhiteOctober namespace to your autoloader:

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


That's  it for basic configuration.

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

The last item in the breadcrumbs collection will automatically be rendered
as plain text rather than a `<a>...</a>` tag.

Configuration
=============

The following parameters can be overriden in your `config.yml` or similar:

    white_october_breadcrumbs:
        separator:          defaults to '/'
        separatorClass:     defaults to 'separator'
        listId:             defaults to 'wo-breadcrumbs'
        listClass:          defaults to 'breadcrumb'
        itemClass:          defaults to ''
        linkRel:            defaults to ''
        locale:             defaults to null, so the default locale is used
        translation_domain: defaults to null, so the default domain is used
        viewTemplate:       defaults to "WhiteOctoberBreadcrumbsBundle::breadcrumbs.html.twig"

These can also be passed as parameters in the view when rendering the
breadcrumbs - for example:

    {{ wo_render_breadcrumbs({separator: '>', listId: 'breadcrumbs'}) }}

Advanced Usage
==============

You can add a whole array of objects at once

    $breadcrumbs->addObjectArray(array $objects, $text, $url, $translationParameters);

    objects:            array of objects
    text:               name of object property or closure
    url:                name of URL property or closure

Example:

    $that = $this;
    $breadcrumbs->addObjectArray($selectedPath, "name", function($object) use ($that) {
        return $that->generateUrl('_object_index', array('slug' => $object->getSlug()));
    });

You can also add a tree path

    $breadcrumbs->addObjectTree($object, $text, $url = "", $parent = 'parent', array $translationParameters = array(), $firstPosition = -1)

    object:             object to start with
    text:               name of object property or closure
    url:                name of URL property or closure
    parent:             name of parent property or closure
    firstPosition:      position to start inserting items (-1 = determine automatically)

Overriding the template
=======================

There are two methods of doing this.

  1. You can override the template used by copying the
     `Resources/views/breadcrumbs.html.twig` file out of the bundle and placing it
     into `app/Resources/WhiteOctoberBreadcrumbsBundle/views`, then customising
     as you see fit.

  2. Use the `viewTemplate` configuration parameter:

        {{ wo_render_breadcrumbs({ viewTemplate: "YourOwnBundle::yourBreadcrumbs.html.twig" }) }}
