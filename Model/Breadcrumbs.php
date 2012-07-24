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
}