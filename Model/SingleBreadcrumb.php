<?php

namespace WhiteOctober\BreadcrumbsBundle\Model;

class SingleBreadcrumb
{
    public $url;
    public $text;
    public $translationParameters;
    public $translate;

    public function __construct($text = "", $url = "", array $translationParameters = array(), $translate = true)
    {
        $this->url = $url;
        $this->text = $text;
        $this->translationParameters = $translationParameters;
        $this->translate = $translate;
    }
}