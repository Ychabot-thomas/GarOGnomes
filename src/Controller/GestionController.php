<?php
/**
 * Created by PhpStorm.
 * User: yannchabot-thomas
 * Date: 16/03/2019
 * Time: 20:02
 */
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class GestionController extends AbstractController
{

    /**
     * @Route ("/gestionindex", name="gestion")
     */

    public function gestion()
    {
        return $this->render('gestion/gestion_index.html.twig');
    }

    /**
     * @Route ("/gestion/ajout_utilisateur", name="app_ajt_utilisateur")
     */

    public function ajout_utilisateur()
    {
        return $this->render('gestion/ajout_utilisateur.html.twig');
    }

    /**
     * @Route ("/gestion/gestion_utilisateur", name="app_gst_utilisateur")
     */

    public function gestion_utilisateur()
    {
        return $this->render('gestion/gestion_utilisateur.html.twig');
    }

    /**
     * @Route ("/gestion/supprimer_utilisateur", name="app_spr_joueur")
     */

    public function supprimer_utilisateur()
    {
        return $this->render('gestion/supprimer_utilisateur.html.twig');
    }

    /**
     * @Route ("/gestion/gestion_partie", name="app_gst_partie")
     */

    public function gestion_partie()
    {
        return $this->render('gestion/gestion_partie.html.twig');
    }

    /**
     * @Route ("/gestion/supprimer_partie", name="app_spr_partie")
     */

    public function supprimer_partie()
    {
        return $this->render('gestion/supprimer_partie.html.twig');
    }

    /**
     * @Route ("/gestion/statistique_joueur", name="app_sta_joueur")
     */

    public function statistique_joueur()
    {
        return $this->render('gestion/statistique_joueur.html.twig');
    }

    /**
     * @Route ("/gestion/statistique_partie", name="app_sta_partie")
     */

    public function statistique_partie()
    {
        return $this->render('gestion/statistique_partie.html.twig');
    }

    /**
     * @Route ("/gestion/mail_joueur", name="app_mail_joueur")
     */

    public function mail_joueur()
    {
        return $this->render('gestion/mail_joueur.html.twig');
    }

    /**
     * @Route ("/gestion/mail_communaute", name="app_mail_communaute")
     */

    public function mail_communaute()
    {
        return $this->render('gestion/mail_communaute.html.twig');
    }
}
