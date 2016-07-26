<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Component\EditorComponent;

class ExampleEditor extends EditorComponent
{
    /**
     * @title Запрос
     * @entity Creonit\SearchBundle\Model\SearchExample
     * @field text {required: true}
     * @template
     *
     * {{ text | text }}
     *
     */
    public function schema(){
    }
}