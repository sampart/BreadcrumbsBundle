<?php

namespace WhiteOctober\BreadcrumbsBundle\Model;

use Symfony\Component\Routing\RouterInterface;

class Breadcrumbs implements \Iterator, \ArrayAccess, \Countable
{
    const DEFAULT_NAMESPACE = "default";

    private $breadcrumbs = array(
        self::DEFAULT_NAMESPACE => array()
    );

    /**
     * @var RouterInterface
     */
    private $router;

    public function addItem($text, $url = "", array $translationParameters = array())
    {
        return $this->addNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationParameters);
    }

    public function addNamespaceItem($namespace, $text, $url = "", array $translationParameters = array())
    {
        $b = new SingleBreadcrumb($text, $url, $translationParameters);
        $this->breadcrumbs[$namespace][] = $b;

        return $this;
    }

    public function prependItem($text, $url = "", array $translationParameters = array())
    {
        return $this->prependNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationParameters);
    }

    public function prependNamespaceItem($namespace, $text, $url = "", array $translationParameters = array())
    {
        $b = new SingleBreadcrumb($text, $url, $translationParameters);
        array_unshift($this->breadcrumbs[$namespace], $b);

        return $this;
    }

    public function addRouteItem($text, $route, array $parameters = array(), $referenceType = RouterInterface::ABSOLUTE_PATH, array $translationParameters = array())
    {
        return $this->addNamespaceRouteItem(self::DEFAULT_NAMESPACE, $text, $route, $parameters, $referenceType, $translationParameters);
    }

    public function addNamespaceRouteItem($namespace, $text, $route, array $parameters = array(), $referenceType = RouterInterface::ABSOLUTE_PATH, array $translationParameters = array())
    {
        $url = $this->router->generate($route, $parameters, $referenceType);

        return $this->addNamespaceItem($namespace, $text, $url, $translationParameters);
    }

    public function prependRouteItem($text, $route, array $parameters = array(), $referenceType = RouterInterface::ABSOLUTE_PATH, array $translationParameters = array())
    {
        return $this->prependNamespaceRouteItem(self::DEFAULT_NAMESPACE, $text, $route, $parameters, $referenceType, $translationParameters);
    }

    public function prependNamespaceRouteItem($namespace, $text, $route, array $parameters = array(), $referenceType = RouterInterface::ABSOLUTE_PATH, array $translationParameters = array())
    {
        $url = $this->router->generate($route, $parameters, $referenceType);

        return $this->prependNamespaceItem($namespace, $text, $url, $translationParameters);
    }

    public function addObjectArray(array $objects, $text, $url = "", array $translationParameters = array())
    {
        return $this->addNamespaceObjectArray(self::DEFAULT_NAMESPACE, $objects, $text, $url, $translationParameters);
    }

    public function addNamespaceObjectArray($namespace, array $objects, $text, $url = "", array $translationParameters = array())
    {
        foreach($objects as $object) {
            $itemText = $this->validateArgument($object, $text);
            if ($url != "") {
                $itemUrl = $this->validateArgument($object, $url);
            } else {
                $itemUrl = "";
            }
            $this->addNamespaceItem($namespace, $itemText, $itemUrl, $translationParameters);
        }

        return $this;
    }

    public function clear($namespace = "")
    {
        if (strlen($namespace)) {
            $this->breadcrumbs[$namespace] = array();
        } else {
            $this->breadcrumbs = array();
        }

        return $this;
    }

    public function addObjectTree($object, $text, $url = "", $parent = 'parent', array $translationParameters = array(), $firstPosition = -1)
    {
        return $this->addNamespaceObjectTree(self::DEFAULT_NAMESPACE, $object, $text, $url, $parent, $translationParameters, $firstPosition);
    }

    public function addNamespaceObjectTree($namespace, $object, $text, $url = "", $parent = 'parent', array $translationParameters = array(), $firstPosition = -1)
    {
        $itemText = $this->validateArgument($object, $text);
        if ($url != "") {
            $itemUrl = $this->validateArgument($object, $url);
        } else {
            $itemUrl = "";
        }
        $itemParent = $this->validateArgument($object, $parent);
        if ($firstPosition == -1) {
            $firstPosition = sizeof($this->breadcrumbs);
        }
        $b = new SingleBreadcrumb($itemText, $itemUrl, $translationParameters);
        array_splice($this->breadcrumbs[$namespace], $firstPosition, 0, array($b));
        if ($itemParent) {
            $this->addNamespaceObjectTree($namespace, $itemParent, $text, $url, $parent, $translationParameters, $firstPosition);
        }
        return $this;
    }

    public function getNamespaceBreadcrumbs($namespace = self::DEFAULT_NAMESPACE)
    {
        // Check whether requested namespace breadcrumbs is exists
        if (!$this->hasNamespaceBreadcrumbs($namespace)) {
            throw new \InvalidArgumentException(sprintf(
                'The breadcrumb namespace "%s" does not exist', $namespace
            ));
        }

        return $this->breadcrumbs[$namespace];
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function hasNamespaceBreadcrumbs($namespace = self::DEFAULT_NAMESPACE)
    {
        return isset($this->breadcrumbs[$namespace]);
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function rewind($namespace = self::DEFAULT_NAMESPACE)
    {
        return reset($this->breadcrumbs[$namespace]);
    }

    public function current($namespace = self::DEFAULT_NAMESPACE)
    {
        return current($this->breadcrumbs[$namespace]);
    }

    public function key($namespace = self::DEFAULT_NAMESPACE)
    {
        return key($this->breadcrumbs[$namespace]);
    }

    public function next($namespace = self::DEFAULT_NAMESPACE)
    {
        return next($this->breadcrumbs[$namespace]);
    }

    public function valid($namespace = self::DEFAULT_NAMESPACE)
    {
        return null !== key($this->breadcrumbs[$namespace]);
    }

    public function offsetExists($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        return isset($this->breadcrumbs[$namespace][$offset]);
    }

    public function offsetSet($offset, $value, $namespace = self::DEFAULT_NAMESPACE)
    {
        $this->breadcrumbs[$namespace][$offset] = $value;
    }

    public function offsetGet($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        return isset($this->breadcrumbs[$namespace][$offset]) ? $this->breadcrumbs[$namespace][$offset] : null;
    }

    public function offsetUnset($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        unset($this->breadcrumbs[$namespace][$offset]);
    }

    public function count($namespace = self::DEFAULT_NAMESPACE)
    {
        return count($this->breadcrumbs[$namespace]);
    }

    private function validateArgument($object, $argument)
    {
        if (is_callable($argument)) {
            return $argument($object);
        }

        $getter = 'get' . ucfirst($argument);
        if (method_exists($object, $getter)) {
            return call_user_func(array(&$object, $getter), $getter);
        }

        throw new \InvalidArgumentException(sprintf(
            'Neither a valid callback function passed nor a method with the name %s() is exists', $getter
        ));
    }
}
