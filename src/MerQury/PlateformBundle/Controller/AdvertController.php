<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MerQury\PlateformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use MerQury\PlateformBundle\Entity\Advert;
use MerQury\PlateformBundle\Entity\Image;
use MerQury\PlateformBundle\Entity\Application;
use MerQury\PlateformBundle\Entity\AdvertSkill;

class AdvertController extends Controller {

    public function indexAction($page) {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }

        $listAdverts = array(
            array(
                'title' => 'Recherche développpeur Symfony2',
                'id' => 1,
                'author' => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Mission de webmaster',
                'id' => 2,
                'author' => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Offre de stage webdesigner',
                'id' => 3,
                'author' => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date' => new \Datetime())
        );
        return $this->render('MerQuryPlateformBundle:Advert:index.html.twig', array('listAdverts' => array()));
    }

    public function menuAction() {

        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony2'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('MerQuryPlateformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));
    }

    public function viewAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em
                ->getRepository('MerQuryPlateformBundle:Advert')
                ->find($id)
        ;

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }


        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
                ->getRepository('MerQuryPlateformBundle:Application')
                ->findBy(array('advert' => $advert))
        ;

        $listAdvertSkills = $em
                ->getRepository('MerQuryPlateformBundle:AdvertSkill')
                ->findBy(array('advert' => $advert))
        ;

        return $this->render('MerQuryPlateformBundle:Advert:view.html.twig', array(
                    'advert' => $advert,
                    'listApplications' => $listApplications,
                    'listAdvertSkills' => $listAdvertSkills
        ));


        //$tag = $request->query->get('tag');
        //return new Response("Affichage de l'annonce d'id : " . $id . ", avec le tag : " . $tag);
        //return $this->render('MerQuryPlateformBundle:Advert:view.html.twig', array('id' => $id));
        //return $this->redirect($this->get('router')->generate('mer_qury_plateform_home'));
        //return new JsonResponse(array('id' => $id));
        // $session = $request->getSession();
        //$userId = $session->get('user_id');
        //$session->set('user_id', 91);
        //return new Response("Je suis une page de test, je n'ai rien à dire");
    }

    public function addAction(Request $request) {



        //$antispam = $this->container->get('mer_qury_plateform.antispam');
        // Je pars du principe que $text contient le texte d'un message quelconque
        //$text = '...';
//    if ($antispam->isSpam($text)) {
//
//      throw new \Exception('Votre message a été détecté comme spam !');
//    }
        // Création de l'entité

        $advert = new Advert();

        $advert->setTitle('Recherche développeur Symfony2.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…");

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');
        // On lie l'image à l'annonce

        $advert->setImage($image);

        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");
        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");
        // On lie les candidatures à l'annonce
        $application1->setAdvert($advert);
        $application2->setAdvert($advert);
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();



        // On récupère toutes les compétences possibles

        $listSkills = $em->getRepository('MerQuryPlateformBundle:Skill')->findAll();
        // Pour chaque compétence

        foreach ($listSkills as $skill) {
            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();
            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);
            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');
            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $em->persist($advertSkill);
        }
        $em = $this->getDoctrine()->getManager();
        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);
        // Étape 1 bis : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
        // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
        $em->persist($application1);
        $em->persist($application2);
        // Étape 2 : On « flush » tout ce qui a été persisté avant
        $em->flush();
        // Reste de la méthode qu'on avait déjà écrit
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
            return $this->redirect($this->generateUrl('mer_qury_plateform_view', array('id' => $advert->getId())));
        }
        return $this->render('MerQuryPlateformBundle:Advert:add.html.twig');


        //return $this->redirect($this->generateUrl('mer_qury_plateform_view', array('id' => 5)));
    }

    public function editAction($id, Request $request) {
//        if ($request->isMethod('POST')) {
//            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
//            return $this->redirect($this->generateUrl('mer_qury_plateform_view', array('id' => 5)));
//        }

        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id

        $advert = $em->getRepository('MerQuryPlateformBundle:Advert')->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('MerQuryPlateformBundle:Category')->findAll();
        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }
        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();
        return $this->render('MerQuryPlateformBundle:Advert:edit.html.twig', array('advert' => $advert));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        $em->flush();



        return $this->render('MerQuryPlateformBundle:Advert:delete.html.twig');
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
                ->getRepository('OCPlatformBundle:Advert')
        ;

        $listAdverts = $repository->myFindAll();
        // ...
    }

// Depuis un contrôleur
    public function listAction() {
        $listAdverts = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('OCPlatformBundle:Advert')
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
