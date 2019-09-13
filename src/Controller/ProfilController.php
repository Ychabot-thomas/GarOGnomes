<?php

/**
 * Created by PhpStorm.
 * User: yannchabot-thomas
 * Date: 16/03/2019
 * Time: 18:34
 */

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsFormType;
use App\Entity\Gif;
use App\Form\GifFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{

    /**
     * @Route("/profilindex", name="profil_index")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $news = new News();
        $news->setDatecreation(new \datetime);
        $formnews = $this->createForm(NewsFormType::class, $news);

        $gif = new Gif();
        $gif->setDatecreation(new \datetime);
        $formgif = $this->createForm(GifFormType::class, $gif);

        $formnews->handleRequest($request);
        $formgif->handleRequest($request);


        if ($formnews->isSubmitted() && $formnews->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('profil_index');
        }


        if ($formgif->isSubmitted() && $formgif->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gif);
            $entityManager->flush();

            return $this->redirectToRoute('profil_index');
        }


        return $this->render('/profil/profil_index.html.twig', [
            'newsForm' => $formnews->createView(),
            'gifForm' => $formgif->createView()
        ]);
    }
}
