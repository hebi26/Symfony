<?php

namespace VL\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('VLCrudBundle:Default:index.html.twig');
    }
}
