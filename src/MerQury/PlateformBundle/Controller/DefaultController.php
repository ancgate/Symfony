<?php

namespace MerQury\PlateformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MerQuryPlateformBundle:Default:index.html.twig', array('name' => $name));
    }
}
