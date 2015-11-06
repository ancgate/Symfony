<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MerQury\PlateformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use MerQury\PlateformBundle\Entity\Advert;

class AdvertController extends Controller {

    public function indexAction($page) {
        if ($page < 1) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        $nbPerPage = 2;
        // Pour récupérer la liste de toutes les annonces : on utilise findAll()
        $listAdverts = $this->getDoctrine()
                ->getManager()
                ->getRepository('MerQuryPlateformBundle:Advert')
                ->getAdverts($page, $nbPerPage);
        $nbPages = ceil(count($listAdverts) / $nbPerPage);
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

// L'appel de la vue ne change pas
        return $this->render('MerQuryPlateformBundle:Advert:index.html.twig', array(
                    'listAdverts' => $listAdverts,
                    'nbPages' => $nbPages,
                    'page' => $page
        ));
    }

    public function menuAction($limit = 3) {
        $listAdverts = $this->getDoctrine()
                ->getManager()
                ->getRepository('MerQuryPlateformBundle:Advert')
                ->findBy(
                array(), // Pas de critère
                array('date' => 'desc'), // On trie par date décroissante
                $limit, // On sélectionne $limit annonces
                0                        // À partir du premier
        );
        ;
        return $this->render('MerQuryPlateformBundle:Advert:menu.html.twig', array(
                    'listAdverts' => $listAdverts
        ));
    }

    public function viewAction($id, Request $request) {

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // Pour récupérer une annonce unique : on utilise find()
        $advert = $em->getRepository('MerQuryPlateformBundle:Advert')->find($id);
        // On vérifie que l'annonce avec cet id existe bien
        if ($advert === null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // On récupère la liste des advertSkill pour l'annonce $advert

        $listAdvertSkills = $em->getRepository('MerQuryPlateformBundle:AdvertSkill')->findByAdvert($advert);
        // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :

        return $this->render('MerQuryPlateformBundle:Advert:view.html.twig', array(
                    'advert' => $advert,
                    'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request) {
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
        $advert = new Advert();

         $form = $this->createForm(new AdvertType(), $advert);

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirect($this->generateUrl('mer_qury_plateform_view', array('id' => $advert->getId())));
        }

        return $this->render('MerQuryPlateformBundle:Advert:add.html.twig', array(
                    'form' => $form->createView(),
        ));




//        if ($request->isMethod('POST')) {
//            // Ici, on s'occupera de la création et de la gestion du formulaire
//            $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');
//            // Puis on redirige vers la page de visualisation de cet article
//            return $this->redirect($this->generateUrl('mer_qury_plateform_view', array('id' => 1)));
//        }
//        
        // Si on n'est pas en POST, alors on affiche le formulaire
    }

    public function editAction($id, Request $request) {

        // On récupère l'EntityManager

        $em = $this->getDoctrine()->getManager();
        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository('MerQuryPlateformBundle:Advert')->find($id);
        // Si l'annonce n'existe pas, on affiche une erreur 404

        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // Ici, on s'occupera de la création et de la gestion du formulaire

        return $this->render('MerQuryPlateformBundle:Advert:edit.html.twig', array(
                    'advert' => $advert
        ));
    }

    public function deleteAction($id, Request $request) {
        // On récupère l'EntityManager

        $em = $this->getDoctrine()->getManager();
        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository('MerQuryPlateformBundle:Advert')->find($id);
        // Si l'annonce n'existe pas, on affiche une erreur 404

        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        if ($request->isMethod('POST')) {
            // Si la requête est en POST, on deletea l'article
            $request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée.');
            // Puis on redirige vers l'accueil

            return $this->redirect($this->generateUrl('mer_qury_plateform_home'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de delete
        return $this->render('MerQuryPlateformBundle:Advert:delete.html.twig', array(
                    'advert' => $advert
        ));
    }

    public function viewSlugAction($slug, $year, $format) {
        return new Response(
                "On pourrait afficher l'annonce correspondant au
            slug '" . $slug . "', créée en " . $year . " et au format " . $format . "."
        );
    }

    public function editImageAction($advertId) {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce
        $advert = $em->getRepository('MerQuryPlateformBundle:Advert')->find($advertId);
        // On modifie l'URL de l'image par exemple
        $advert->getImage()->setUrl('test.png');
        // On n'a pas besoin de persister l'annonce ni l'image.
        // Rappelez-vous, ces entités sont automatiquement persistées car
        // on les a récupérées depuis Doctrine lui-même
        // On déclenche la modification
        $em->flush();
        return new Response('OK');
    }

    public function testAction() {

        $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('MerQuryPlateformBundle:Advert')
        ;

        $listAdverts = $repository->myFindAll();
        // ...
    }

// Depuis un contrôleur
    public function listAction() {
        $listAdverts = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('MerQuryPlateformBundle:Advert')
                ->getAdvertWithApplications()
        ;

        foreach ($listAdverts as $advert) {
            // Ne déclenche pas de requête : les candidatures sont déjà chargées !
            // Vous pourriez faire une boucle dessus pour les afficher toutes
            $advert->getApplications();
        }

        // …
    }

}
