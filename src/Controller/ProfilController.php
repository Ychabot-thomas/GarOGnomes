<?php
/**
 * Created by PhpStorm.
 * User: yannchabot-thomas
 * Date: 16/03/2019
 * Time: 18:34
 */
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ProfilController extends AbstractController {

    /**
     * @Route("/profilindex", name="profil_index")
     * @return Response
     */

    public function index(): Response{
        return $this->render('/profil/profil_index.html.twig');
    }

}