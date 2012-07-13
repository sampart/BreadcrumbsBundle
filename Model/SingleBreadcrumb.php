<?php

namespace WhiteOctober\BreadcrumbsBundle\Model;

class SingleBreadcrumb
{
    public $url;
    public $text;
    public $translationParameters;

    public function __construct($text = "", $url = "", array $translationParameters = array())
    {
        $this->url = $url;
        $this->text = $text;
        $this->translationParameters = $translationParameters;
    }
}