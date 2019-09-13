<?php

/**
 * Created by PhpStorm.
 * User: yannchabot-thomas
 * Date: 16/03/2019
 * Time: 18:34
 */

namespace App\Controller;

use App\Repository\AlbumRepository;
use App\Repository\NewsRepository;
// use App\Repository\AlbumsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @return Response
     */

    public function index(NewsRepository $newsRepository, AlbumRepository $albumRepository): Response
    {
        $news = $newsRepository->findBy([], ["id" => "DESC"], 10);
        // $albums = $albumRepository->findBy([], ["id" => "DESC"], 10);

        return $this->render('base.html.twig', [
            'news' => $news,
            // 'albums' => $albums,
        ]);
    }
}
