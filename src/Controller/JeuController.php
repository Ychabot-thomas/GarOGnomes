<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Entity\Partie;
use App\Repository\CarteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JeuController
 * @package App\Controller
 * @Route("/jeu")
 */
class JeuController extends AbstractController
{

    /**
     * @Route("/nouvelle-partie", name="new_game")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function newGame(UserRepository $userRepository)
    {

        return $this->render('jeu/creer_partie.html.twig', [
            'user'        => $this->getUser(),
            'adversaires' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/creer-partie", name="create_game")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param UserRepository $userRepository
     * @param CarteRepository $carteRepository
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function createGame(
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository,
        CarteRepository $carteRepository,
        \Swift_Mailer $mailer
    ) {
        $partie = new Partie();
        $partie->setj1_partie($this->getUser());
        $j2 = $userRepository->find($request->request->get('adversaire'));
        $partie->setj2_partie($j2);

        $cartes = $carteRepository->findAll();

        $tJ1 = [];
        $tJ2 = [];
        $shogun1 = '';
        $shogun2 = '';

        /** @var Carte $carte */
        foreach ($cartes as $carte) {
            if ($carte->getCamps() === 'J1') {
                if ($carte->isShogun()) {
                    $shogun1 = $carte->getId();
                } else {
                    $tJ1[] = $carte->getId();
                }
            }

            if ($carte->getCamps() === 'J2') {
                if ($carte->isShogun()) {
                    $shogun2 = $carte->getId();
                } else {
                    $tJ2[] = $carte->getId();
                }
            }
        }

        shuffle($tJ1);
        shuffle($tJ2);

        $terrainJ1 = [
            1 => [1 => $shogun1, 2 => $tJ1[0], 3 => $tJ1[1], 4 => $tJ1[2]],
            2 => [1 => $tJ1[3], 2 => $tJ1[4], 3 => $tJ1[5]],
            3 => [1 => $tJ1[6], 2 => $tJ1[7]],
            4 => [1 => $tJ1[8]],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => []
        ];

        $partie->setTerrainJ1($terrainJ1);


        $terrainJ2 = [
            1 => [1 => $shogun2, 2 => $tJ2[0], 3 => $tJ2[1], 4 => $tJ2[2]],
            2 => [1 => $tJ2[3], 2 => $tJ2[4], 3 => $tJ2[5]],
            3 => [1 => $tJ2[6], 2 => $tJ2[7]],
            4 => [1 => $tJ2[8]],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => []
        ];

        $partie->setTerrainJ2($terrainJ2);
        $partie->setTour($partie->getj1_partie());
        $partie->setMove(1);
        $partie->setDebut(new \DateTime());
        $partie->setTimePartie(new \DateTime());
        $partie->setEtatPartie(1);

        $entityManager->persist($partie);
        $entityManager->flush();

        return $this->redirectToRoute('show_game', ['partie' => $partie->getId()]);
    }

    /**
     * @Route("/affiche-partie/{partie}", name="show_game")
     * @param Partie $partie
     * @param CarteRepository $carteRepository
     * @return Response
     */
    public function showGame(Partie $partie, CarteRepository $carteRepository)
    {
        if (empty($partie->getGagnantPartie()))
            if ($partie->getj1_partie() === $this->getUser()) {
                //en base c'est J1, adversaire = J2;
                $terrainJoueur = $partie->getTerrainJ1();
                $terrainAdversaire = $partie->getTerrainJ2();
            } else {
                $terrainAdversaire = $partie->getTerrainJ1();
                $terrainJoueur = $partie->getTerrainJ2();
            } else {
            $this->addFlash('danger', 'Cette partie a déjà été terminé !');
            return $this->render('jeu/index.html.twig');
        }


        return $this->render('jeu/afficher_partie.html.twig', [
            'partie'            => $partie,
            'terrainAdversaire' => $terrainAdversaire,
            'terrainJoueur'     => $terrainJoueur,
            'tCartes'           => $carteRepository->findByArrayId()
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param CarteRepository $carteRepository
     * @param Request $request
     * @param Partie $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/deplacement-partie/{partie}", name="deplacement_game", methods={"POST"})
     */
    public function move(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Partie $partie)
    {
        $carte = $carteRepository->find($request->request->get('id'));

        if ($carte !== null) {
            $numPile = $request->request->get('pile');
            $position = $request->request->get('position');
            $valeurDeplacement = $request->request->get('valeur');

            $terrainJoueur = null;
            $terrainAdv = null;

            //vérifie si le joueur connecté est le joueur 1
            if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                $terrainJoueur = $partie->getTerrainJ1();
                $terrainAdv = $partie->getTerrainJ2();
            } else {
                $terrainJoueur = $partie->getTerrainJ2();
                $terrainAdv = $partie->getTerrainJ1();
            }

            $pileDepart = $terrainJoueur[$numPile];
            $terrainJoueur[$numPile] = [];

            $pileDestination = $numPile + $valeurDeplacement;
            //fait en sorte que la pile de destination soit entre 1 et 11
            if ($pileDestination > 11)
                $pileDestination = 11;

            $pileDestinationAdv = 11 - $numPile - $valeurDeplacement + 1;
            //fait en sorte que la pile de destination du côté adverse soit entre 1 et 11
            if ($pileDestinationAdv < 0)
                $pileDestinationAdv = 0;
            $nbCartes = count($terrainJoueur[$pileDestination]);
            $i = 1;
            if ($nbCartes === 0)
                $terrainJoueur[$pileDestination] = [];

            //rajoute les cartes dans la pile de destination
            foreach ($pileDepart as $index => $idCarte) :
                if ($i >= $position)
                    $terrainJoueur[$pileDestination][$nbCartes + $index] = $idCarte;
                else
                    $terrainJoueur[$numPile][$index] = $idCarte;
                $i++;
            endforeach;

            if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                $partie->setTerrainJ1($terrainJoueur);
            } else {
                $partie->setTerrainJ2($terrainJoueur);
            }

            $entityManager->flush();


            if ($pileDestinationAdv > 0) {
                if (count($terrainAdv[$pileDestinationAdv]) > 0) {
                    $idCombattantJoueur = end($terrainJoueur[$pileDestination]);
                    $idCombattantAdv = end($terrainAdv[$pileDestinationAdv]);
                    return $this->json(['etat' => 'conflit', 'idCombattantJoueur' => $idCombattantJoueur, 'idCombattantAdv' => $idCombattantAdv, 'pileDestination' => $pileDestination, 'pileDestinationAdv' => $pileDestinationAdv, 'taillePile1' => count($terrainJoueur[$pileDestination]), 'taillePile2' => count($terrainAdv[$pileDestinationAdv])]);
                } else {
                    return $this->json(['etat' => 'pas de conflit']);
                }
            }
        }
        return $this->json('OK', Response::HTTP_OK);
    }


    /**
     * @param CarteRepository $cartesRepository
     * @param Request $request
     * @param Partie $partie
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/resolve-conflict/{partie}", name="resolve_conflict_game", methods={"POST"})
     */
    public function resolveConflict(CarteRepository $cartesRepository, Request $request, Partie $partie, EntityManagerInterface $entityManager)
    {

        $carteJ1 = $cartesRepository->find($request->request->get('idCombattantJoueur'));
        $carteJ2 = $cartesRepository->find($request->request->get('idCombattantAdv'));
        $pile1 = $request->request->get('pileDestination');
        $pile2 = $request->request->get('pileDestinationAdv');

        if ($carteJ1 && $carteJ2) {
            if ($carteJ1->getCouleur() == $carteJ2->getCouleur()) {
                if ($carteJ1->getPoid() > $carteJ2->getPoid()) {
                    if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                        $terrain2 = $partie->getTerrainJ2();
                    } else {
                        $terrain2 = $partie->getTerrainJ1();
                    }

                    //$tab = array_pop($terrain2[$pile2]);

                    if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                        $partie->setTerrainJ2($terrain2);
                    } else {
                        $partie->setTerrainJ1($terrain2);
                    }
                    $entityManager->flush();
                } elseif ($carteJ1->getPoid() < $carteJ2->getPoid()) {
                    if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                        $terrain1 = $partie->getTerrainJ1();
                    } else {
                        $terrain1 = $partie->getTerrainJ2();
                    }
                    // $tab = array_pop($terrain1[$pile1]);

                    if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                        $partie->setTerrainJ1($terrain1);
                    } else {
                        $partie->setTerrainJ2($terrain1);
                    }
                    $entityManager->flush();
                    // return $this->json(['test' => 'baya2', 'vainqueur' => $carteJ2->getId(),'perdant' =>$tab, 'terrain' => $terrain1]);
                } elseif ($carteJ1->getPoid() === $carteJ2->getPoid()) {
                    if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                        $terrain1 = $partie->getTerrainJ1();
                        $terrain2 = $partie->getTerrainJ2();
                    } else {
                        $terrain1 = $partie->getTerrainJ2();
                        $terrain2 = $partie->getTerrainJ1();
                    }
                    $terrain1[$pile1 - 1][] = $carteJ1->getId();
                    array_pop($terrain1[$pile1]);
                    $terrain2[$pile2 - 1][] = $carteJ2->getId();
                    array_pop($terrain2[$pile2]);

                    $partie->setTerrainJ1($terrain1);
                    $partie->setTerrainJ2($terrain2);

                    $entityManager->flush();
                }
            } elseif (
                $carteJ1->getCouleur() == "bleu" && $carteJ2->getCouleur() == "rouge" ||
                $carteJ1->getCouleur() == "rouge" && $carteJ2->getCouleur() == "blanc" ||
                $carteJ1->getCouleur() == "blanc" && $carteJ2->getCouleur() == "bleu"
            ) {
                if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                    $terrain2 = $partie->getTerrainJ2();
                } else {
                    $terrain2 = $partie->getTerrainJ1();
                }

                // $tab = array_pop($terrain2[$pile2]);

                if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                    $partie->setTerrainJ2($terrain2);
                } else {
                    $partie->setTerrainJ1($terrain2);
                }


                $entityManager->flush();
            } elseif (
                $carteJ2->getCouleur() == "bleu" && $carteJ1->getCouleur() == "rouge" ||
                $carteJ2->getCouleur() == "rouge" && $carteJ1->getCouleur() == "blanc" ||
                $carteJ2->getCouleur() == "blanc" && $carteJ1->getCouleur() == "bleu"
            ) {
                if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                    $terrain1 = $partie->getTerrainJ1();
                } else {
                    $terrain1 = $partie->getTerrainJ2();
                }
                // $tab = array_pop($terrain1[$pile1]);

                if ($this->getUser()->getId() === $partie->getj1_partie()->getId()) {
                    $partie->setTerrainJ1($terrain1);
                } else {
                    $partie->setTerrainJ2($terrain1);
                }

                $entityManager->flush();
            }


            $idCombattantJoueur = end($terrain1[$pile1]);
            $idCombattantAdv = end($terrain2[$pile2]);




            return $this->json(['taillePile1' => count($terrain1[$pile1]), 'taillePile2' => count($terrain2[$pile2]), 'idCombattantJoueur2' => $idCombattantJoueur, 'idCombattantAdv2' => $idCombattantAdv]);
        }
    }

    /**
     * @Route("/ajax/sauvegar/lance/des/{partie}", methods={"POST"}, name="sauvegarder_lance_des")
     * @param Request $request
     * @param Partie $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sauvegarderLanceDes(Request $request, Partie $partie)
    {
        $de1 = $request->request->get('de1');
        $de2 = $request->request->get('de2');
        $de3 = $request->request->get('de3');

        $des = ['de1' => $de1, 'de2' => $de2, 'de3' => $de3];
        $partie->setDes($des);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json('OK', Response::HTTP_OK);
    }

    /**
     * @param CarteRepository $carteRepository
     * @param Partie $partie
     * @return Response
     * @Route("/refresh-terrain/{partie}", name="refresh_game")
     */
    public function refreshTerrain(CarteRepository $carteRepository, Partie $partie)
    {
        if ($partie->getj1_partie()->getId() === $this->getUser()->getId()) {
            //joueur = J1, adversaire = J2;
            $terrainJoueur = $partie->getTerrainJ1();
            $terrainAdversaire = $partie->getTerrainJ2();
        } else {
            $terrainAdversaire = $partie->getTerrainJ1();
            $terrainJoueur = $partie->getTerrainJ2();
        }

        return $this->render('jeu/plateua.html.twig', [
            'partie'            => $partie,
            'terrainAdversaire' => $terrainAdversaire,
            'terrainJoueur'     => $terrainJoueur,
            'tCartes'           => $carteRepository->findByArrayId()
        ]);
    }

    /**
     * @Route("/which-turn/{partie}", name="which_turn")
     * @param Partie $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function whichTurn(Partie $partie)
    {
        dump(get_class_methods(get_class($partie)));
        if ($this->getUser()->getId() === $partie->getj1_partie()->getId() && $partie->getTour() === $partie->getj1_partie()) {
            return $this->json(['montour' => true, 'tour' => $partie->getTour()]);
        } elseif ($this->getUser()->getId() === $partie->getj1_partie()->getId() && $partie->getTour() === $partie->getj2_partie()) {
            return $this->json(['montour' => false, 'tour' => $partie->getTour()]);
        }

        if ($this->getUser()->getId() === $partie->getj2_partie()->getId() && $partie->getTour() === $partie->getj2_partie()) {
            return $this->json(['montour' => true, 'tour' => $partie->getTour()]);
        } elseif ($this->getUser()->getId() === $partie->getj2_partie()->getId() && $partie->getTour() === $partie->getj1_partie()) {
            return $this->json(['montour' => false, 'tour' => $partie->getTour()]);
        }
        return $this->json('ok');
    }

    /**
     * @param Partie $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/change-turn/{partie}", name="change_turn")
     */
    public function changeTurn(Partie $partie)
    {
        if ($this->getUser() !== $partie->getTour())
            throw new NotFoundHttpException();

        if ($partie->getTour() === $partie->getj1_partie())
            $partie->setTour($partie->getj2_partie());
        else
            $partie->setTour($partie->getj1_partie());

        $em = $this->getDoctrine()->getManager();
        $em->persist($partie);
        $em->flush();
        return $this->json('OK', Response::HTTP_OK);
    }

    /**
     * @param Partie $partie
     * @return Response
     */
    public function abandon(Partie $partie)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->getUser() === $partie->getj1_partie())
            $partie->setGagnantPartie($partie->getj2_partie());
        else
            $partie->setGagnantPartie($partie->getj1_partie());

        $em->persist($partie);
        $em->flush();

        $this->addFlash('warning', 'Vous avez abandonné la partie !');

        return $this->render('jeu/index.html.twig');
    }

    /**
     * @param Partie $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/which-move/{partie}", name="which_move")
     */
    public function whichMove(Partie $partie)
    {
        return $this->json($partie->getMove());
    }

    /**
     * @param Partie $partie
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/change-move/{partie}", name="change_move",methods={"POST"})
     */
    public function changeMove(Partie $partie, EntityManagerInterface $entityManager)
    {
        if ($this->getUser() !== $partie->getTour())
            throw new NotFoundHttpException();

        if ($partie->getMove() === 1)
            $partie->setMove(2);
        else
            $partie->setMove(1);

        $entityManager->flush();
        return $this->json('OK', Response::HTTP_OK);
    }
}
