<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Component\Request\ComponentRequest;
use Creonit\AdminBundle\Component\Response\ComponentResponse;
use Creonit\AdminBundle\Component\Scope\Scope;
use Creonit\AdminBundle\Component\TableComponent;
use Propel\Runtime\ActiveQuery\Criteria;

class QueryTable extends TableComponent
{

    /**
     * @title Список запросов
     * @cols Запрос, Пользователь, Дата
     * @header
     * {{ button('Примеры запросов', {size: 'sm', icon: 'comment-o'}) | open('ExampleTable') }}
     * {{ button('Автозамены', {size: 'sm', icon: 'wrench'}) | open('WordformTable') }}
     *
     * \Query
     * @entity Creonit\SearchBundle\Model\SearchQuery
     * @pagination 50
     * @field created_at:date
     * @field user {load: "entity.getUserId() ? entity.getUser().getTitle() : ''"}
     * @col {{ text }}
     * @col {{ user ? user : 'Гость' }}
     * @col {{ created_at }}
     *
     */
    public function schema(){
    }

    protected function filter(ComponentRequest $request, ComponentResponse $response, $query, Scope $scope, $relation, $relationValue, $level)
    {
        $query->orderByCreatedAt(Criteria::DESC);
    }


}