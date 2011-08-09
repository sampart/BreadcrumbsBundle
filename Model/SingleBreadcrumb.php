<?php

namespace WhiteOctober\BreadcrumbsBundle\Model;

class SingleBreadcrumb
{
    public $url;
    public $text;

    public function __construct($text = "", $url = "")
    {
        $this->url = $url;
        $this->text = $text;
    }
}