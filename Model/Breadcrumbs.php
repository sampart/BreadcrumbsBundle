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

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function rewind()
    {
        return reset($this->breadcrumbs);
    }

    public function current()
    {
        return current($this->breadcrumbs);
    }

    public function key()
    {
        return key($this->breadcrumbs);
    }

    public function next()
    {
        return next($this->breadcrumbs);
    }

    public function valid()
    {
        return key($this->breadcrumbs) !== null;
    }

    public function offsetExists($offset)
    {
        return isset($this->breadcrumbs[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->breadcrumbs[$offset] = $value;
    }

    public function offsetGet($offset)
    {
        return isset($this->breadcrumbs[$offset]) ? $this->breadcrumbs[$offset] : null;
    }

    public function offsetUnset($offset)
    {
        unset($this->breadcrumbs[$offset]);
    }

    public function count()
    {
        return count($this->breadcrumbs);
    }

    private function validateArgument($object, $argument)
    {
        if (is_callable($argument)) {
            return $argument($object);
        } else {
            if (method_exists($object,'get' . $argument)) {
                return call_user_func(array(&$object,  'get' . $argument), 'get' . $argument);
            } else {
                throw new \InvalidArgumentException("Neither a method with the name get$argument() exists nor is it a valid callback function.");
            }
        }
    }
}
