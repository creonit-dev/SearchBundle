<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Component\EditorComponent;

class WordformEditor extends EditorComponent
{
    /**
     * @title Автозамена
     * @entity Creonit\SearchBundle\Model\SearchWordform
     *
     * @field source {required: true}
     * @field target {required: true}
     *
     * @template
     *
     * {{ source | text | group('Исходное слово') }}
     * {{ target | text | group('Замены') }}
     *
     */
    public function schema(){
    }
}