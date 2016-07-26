<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Component\TableComponent;

class ExampleTable extends TableComponent
{

    /**
     * @title Примеры запросов
     * @cols Запрос, .
     * @header
     * {{ button('Добавить запрос', {size: 'sm', type: 'success'}) | open('ExampleEditor') }}
     *
     * \Example
     * @entity Creonit\SearchBundle\Model\SearchExample
     * @col {{ text | open('ExampleEditor', {key: _key}) }}
     * @col {{ _delete() }}
     *
     */
    public function schema(){
    }
}