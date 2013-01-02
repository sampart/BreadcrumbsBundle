<?php

namespace WhiteOctober\BreadcrumbsBundle\Model;

class Breadcrumbs implements \Iterator, \ArrayAccess, \Countable
{
    private $breadcrumbs = array();

    private $position = 0;

    public function addItem($text, $url = "", array $translationParameters = array())
    {
        $b = new SingleBreadcrumb($text, $url, $translationParameters);
        $this->breadcrumbs[] = $b;

        return $this;
    }

    public function addObjectArray(array $objects, $text, $url = "", array $translationParameters = array()) {
        foreach($objects as $object) {
            $itemText = $this->validateArgument($object, $text);
            if ($url != "") {
                $itemUrl = $this->validateArgument($object, $url);
            } else {
                $itemUrl = "";
            }
            $this->addItem($itemText, $itemUrl, $translationParameters);
        }

        return $this;
    }

    public function clear()
    {
        $this->breadcrumbs = array();

        return $this;
    }

    public function addObjectTree($object, $text, $url = "", $parent = 'parent', array $translationParameters = array(), $firstPosition = -1) {
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
        array_splice($this->breadcrumbs, $firstPosition, 0, array($b));
        if ($itemParent) {
            $this->addObjectTree($itemParent, $text, $url, $parent, $translationParameters, $firstPosition);
        }
        return $this;
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

    private function validateArgument($object, $argument) {
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
