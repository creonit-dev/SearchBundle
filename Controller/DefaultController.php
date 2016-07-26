<?php

namespace Creonit\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CreonitSearchBundle:Default:index.html.twig');
    }
}
