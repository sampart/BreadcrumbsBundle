Installation
============

> **NOTE:** The bundle is compatible with Symfony `2.0` upwards.

1. Download this bundle to your project first. The preferred way to do it is
    to use [Composer](https://getcomposer.org/) package manager:
    
    ``` json
    $ composer require whiteoctober/breadcrumbs-bundle
    ```
    
    > **NOTE:** If you haven't installed `Composer` yet, check the [installation guide][2].

    > **NOTE:** If you're not using `Composer`, add the `BreadcrumbsBundle` to your autoloader manually.

2. Add this bundle to your application's kernel:
    
    ``` php
    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),
            // ...
        );
    }
    ```

3. Configure the bundle in your config:
    
    ``` yaml
    # app/config/config.yml
    white_october_breadcrumbs: ~
    ```
    
That's  it for basic configuration. For more options check the [Configuration](#configuration) section.

Usage
=====

In your application controller methods:

``` php
public function yourAction(User $user)
{
    $breadcrumbs = $this->get("white_october_breadcrumbs");
    // Simple example
    $breadcrumbs->addItem("Home", $this->get("router")->generate("index"));

    // Example without URL
    $breadcrumbs->addItem("Some text without link");

    // Example with parameter injected into translation "user.profile"
    $breadcrumbs->addItem($txt, $url, ["%user%" => $user->getName()]);
}
```

and then in your template:

``` jinja
{{ wo_render_breadcrumbs() }}
```

The last item in the breadcrumbs collection will automatically be rendered
as plain text rather than a `<a>...</a>` tag.

The `addItem()` method adds an item to the *end* of the breadcrumbs collection.
You can use the `prependItem()` method to add an item to the *beginning* of
the breadcrumbs collection.  This is handy when used in conjunction with
hierarchical data (e.g. Doctrine Nested-Set).  This example uses categories in
a product catalog:

``` php
public function yourAction(Category $category)
{
    $breadcrumbs = $this->get("white_october_breadcrumbs");

    $node = $category;

    while ($node) {
        $breadcrumbs->prependItem($node->getName(), "<category URL>");

        $node = $node->getParent();
    }
}
```

If you do not want to generate a URL manually, you can easily add breadcrumb items
passing only the route name with any required parameters, using the `addRouteItem()`
and `prependRouteItem()` methods:

``` php
public function yourAction()
{
    $breadcrumbs = $this->get("white_october_breadcrumbs");
    
    // Pass "_demo" route name without any parameters
    $breadcrumbs->addRouteItem("Demo", "_demo");

    // Pass "_demo_hello" route name with parameters
    $breadcrumbs->addRouteItem("Hello Breadcrumbs", "_demo_hello", [
        'name' => 'Breadcrumbs',
    ]);

    // Add "homepage" route link to begin of breadcrumbs
    $breadcrumbs->prependRouteItem("Home", "homepage");
}
```

Configuration
=============

The following *default* parameters can be overriden in your `config.yml` or similar:

``` yaml
# app/config/config.yml
white_october_breadcrumbs:
    separator:          '/'
    separatorClass:     'separator'
    listId:             'wo-breadcrumbs'
    listClass:          'breadcrumb'
    itemClass:          ''
    linkRel:            ''
    locale:             ~ # defaults to null, so the default locale is used
    translation_domain: ~ # defaults to null, so the default domain is used
    viewTemplate:       'WhiteOctoberBreadcrumbsBundle::breadcrumbs.html.twig'
```

These can also be passed as parameters in the view when rendering the
breadcrumbs - for example:

``` jinja
{{ wo_render_breadcrumbs({separator: '>', listId: 'breadcrumbs'}) }}
```

> **NOTE:** If you need more than one set of breadcrumbs on the same page you can use namespaces.
By default, breadcrumbs use the `default` namespace, but you can add more.
To add breadcrumbs to your custom namespace use `addNamespaceItem` / `prependNamespaceItem`
or `addNamespaceRouteItem` / `prependNamespaceRouteItem` methods respectively, for example:

``` php
public function yourAction(User $user)
{
    $breadcrumbs = $this->get("white_october_breadcrumbs");

    // Simple example
    $breadcrumbs->prependNamespaceItem("subsection", "Home", $this->get("router")->generate("index"));

    // Example without URL
    $breadcrumbs->addNamespaceItem("subsection", "Some text without link");

    // Example with parameter injected into translation "user.profile"
    $breadcrumbs->addNamespaceItem("subsection", $txt, $url, ["%user%" => $user->getName()]);
    
    // Example with route name with required parameters
    $breadcrumbs->addNamespaceRouteItem("subsection", $user->getName(), "user_show", ["id" => $user->getId()]);
}
```

Then to render the `subsection` breadcrumbs in your templates, specify this namespace in the options:

``` jinja
{{ wo_render_breadcrumbs({namespace: "subsection"}) }}
```

Advanced Usage
==============

You can add a whole array of objects at once

``` php
$breadcrumbs->addObjectArray(array $objects, $text, $url, $translationParameters);
```

```
objects:            array of objects
text:               name of object property or closure
url:                name of URL property or closure
```

Example:

``` php
$that = $this;
$breadcrumbs->addObjectArray($selectedPath, "name", function($object) use ($that) {
    return $that->generateUrl('_object_index', ['slug' => $object->getSlug()]);
});
```

You can also add a tree path

``` php
$breadcrumbs->addObjectTree($object, $text, $url = "", $parent = 'parent', array $translationParameters = [], $firstPosition = -1)
```

```
object:             object to start with
text:               name of object property or closure
url:                name of URL property or closure
parent:             name of parent property or closure
firstPosition:      position to start inserting items (-1 = determine automatically)
```

> **NOTE:** You can use `addNamespaceObjectArray` and `addNamespaceObjectTree` respectively
for work with multiple breadcrumbs on the same page.

Overriding the template
=======================

There are two methods of doing this.

1. You can override the template used by copying the
    `Resources/views/breadcrumbs.html.twig` file out of the bundle and placing it
    into `app/Resources/WhiteOctoberBreadcrumbsBundle/views`, then customising
    as you see fit. Check the [Overriding bundle templates][1] documentation section
    for more information.

2. Use the `viewTemplate` configuration parameter:
    
    ``` jinja
    {{ wo_render_breadcrumbs({ viewTemplate: "YourOwnBundle::yourBreadcrumbs.html.twig" }) }}
    ```


[1]: http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates
[2]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
