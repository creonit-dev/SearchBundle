<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Component\TableComponent;

class WordformTable extends TableComponent
{
    /**
     * @title Автозамены
     * @cols Исходное слово, Замена, .
     * @header
     * {{ button('Добавить замену', {size: 'sm', type: 'success'}) | open('WordformEditor') }}
     *
     * \Wordform
     * @entity Creonit\SearchBundle\Model\SearchWordform
     * @col {{ source | open('WordformEditor', {key: _key}) }}
     * @col {{ target | open('WordformEditor', {key: _key}) }}
     * @col {{ _delete() }}
     *
     */
    public function schema(){
    }
}