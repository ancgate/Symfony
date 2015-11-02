<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MerQury\PlateformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DivertController extends Controller {

    public function indexAction() {

        $content = $this->get('templating')->render('MerQuryPlateformBundle:Divert:index.html.twig', 
                array(
                    'name'=> 'winzou'
                    
                ));

        return new Response($content);
    }

}