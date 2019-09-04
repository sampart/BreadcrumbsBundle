This project is no longer maintained.
If you are using it with Symfony >= 4.3, you may want to use [this fork](https://github.com/mhujer/BreadcrumbsBundle) instead.

Installation
============

1. Configure templating for your application if you haven't already.  For example:

    ```yaml
    # app/config/config.yml (Symfony <=3)
    framework:
        templating:
            engines: ['twig']
    
    # config/packages/framework.yaml (Symfony 4)
    templating:
        engines: ['twig']
    ```

2. Install this bundle using [Composer](https://getcomposer.org/):
    
    ``` bash
    composer require whiteoctober/breadcrumbs-bundle
    ```
    
3. Add this bundle to your application's kernel:
    
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
    
    If you're using Symfony 4, this step will be done for you by Symfony Flex.

4. Configure the bundle in your config:
    
    ``` yaml
    # app/config/config.yml
    white_october_breadcrumbs: ~
    ```
  
That's it for basic configuration. For more options check the [Configuration](#configuration) section.

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

For Symfony 4, don't retrieve the service via `get`, instead use
[dependency injection](https://symfony.com/doc/current/service_container.html#fetching-and-using-services):
                                                              
```php
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class YourController extends AbstractController
{
    public function yourAction(Breadcrumbs $breadcrumbs)
    {
      // ...
    }
}
```
 

Then, in your template:

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

    // Pass "_demo_hello" route name with route parameters
    $breadcrumbs->addRouteItem("Hello Breadcrumbs", "_demo_hello", [
        'name' => 'Breadcrumbs',
    ]);

    // Add "homepage" route link at the start of the breadcrumbs
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
    viewTemplate:       'WhiteOctoberBreadcrumbsBundle::microdata.html.twig'
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

There are two methods for doing this.

1. You can override the template used by copying the
    `Resources/views/microdata.html.twig` file out of the bundle and placing it
    into `app/Resources/WhiteOctoberBreadcrumbsBundle/views`, then customising
    as you see fit. Check the [Overriding bundle templates][1] documentation section
    for more information.

2. Use the `viewTemplate` configuration parameter:
    
    ``` jinja
    {{ wo_render_breadcrumbs({ viewTemplate: "YourOwnBundle::yourBreadcrumbs.html.twig" }) }}
    ```
> **NOTE:** If you want to use the JSON-LD format, there's already an existing template 
at `WhiteOctoberBreadcrumbsBundle::json-ld.html.twig`. Just set this template as the value for 
`viewTemplate` either in your Twig function call (see Step 2 above) or in your bundle [configuration](#configuration).



[1]: http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates
[2]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx

Contributing
============

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the [issues list](https://github.com/whiteoctober/breadcrumbsbundle/issues) is a good place to start.
If you're a first-time code contributor, you may find Github's guide to [forking projects](https://guides.github.com/activities/forking/) helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our [code of conduct](https://github.com/whiteoctober/open-source-code-of-conduct/blob/master/code_of_conduct.md).
