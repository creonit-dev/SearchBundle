<?php

namespace Creonit\SearchBundle\Admin;

use Creonit\AdminBundle\Module;

class SearchModule extends Module
{
    protected function configure()
    {
        $this
            ->setTitle('Поиск')
            ->setIcon('search')
            ->setTemplate('QueryTable')
        ;
    }


    public function initialize()
    {
        $this->addComponent(new QueryTable);
        $this->addComponent(new ExampleTable);
        $this->addComponent(new ExampleEditor);
        $this->addComponent(new WordformTable);
        $this->addComponent(new WordformEditor);
    }
}