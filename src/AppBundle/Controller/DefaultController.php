<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $allmembers = $em->getRepository('AppBundle:User')->findAll();
        foreach($allmembers as $member) {
            $number = count($em->getRepository('AppBundle:Infos')->findBy(array(
                'iduser' => $member->getId(),
                'etat' => 'unread',
            )));

            $member->setNotifs($number);
            $em->persist($member);
            $em->flush();
        }

        $date = new \DateTime();
        $datenow = $date->format('Y/m/d');        
        $encheresVerif = $em->getRepository('DealBundle:Encheres')->findAll();
        
        foreach($encheresVerif as $verif) {
            if($verif->getDatenew()->format('Y/m/d') == $datenow) {
                $verif->setEtatnew('normal');
                $em->persist($verif);
                $em->flush();
            }
            elseif($verif->getDateold()->format('Y/m/d') == $datenow) {
                $verif->setEtatnew('old');
                $em->persist($verif);
                $em->flush();
            }
        }

        $encheres = $em->getRepository('DealBundle:Encheres')->findByEtat('open');
        if($encheres == NULL) {
            $encheresNew = [];
            $encheresOld = [];
        }
        else {
            foreach($encheres as $enchere) {
                $product = $em->getRepository('ProductBundle:Produit')->findOneById($enchere->getIdproduit());
                $fournisseur = $em->getRepository('AppBundle:User')->findOneById($enchere->getIdfournisseur());
                $commande = $em->getRepository('DealBundle:Commandes')->findOneBy(array(
                    'idenchere' => $enchere->getId(),
                    'idacheteur' => $user->getId(),
                ));
                if ($commande == NULL) {
                    $commandeUser = "Pas de commandes";
                }
                else {
                    $commandeUser = $commande->getNbredecommande();
                }
                if($enchere->getEtatnew() == "new") {
                    $encheresNew [] = array (
                        'id' => $enchere->getId(),
                        'prix' =>  $enchere->getPrix(),
                        'logoFournisseur' => $fournisseur->getLogo(),
                        'produit' => $product->getNom(),
                        'totalCommande' => $enchere->getTotalcommande(),
                        'commandeUser' => $commandeUser,
                        'annee' => $enchere->getFulldate()->format('Y'),
                        'mois' => $enchere->getFulldate()->format('m'),
                        'jour' => $enchere->getFulldate()->format('d'),
                        'minicom' => $product->getCommandemaximal(),
                    );
                }
                elseif ($enchere->getEtatnew() == "old") {
                    $encheresOld [] = array (
                        'id' => $enchere->getId(),
                        'prix' =>  $enchere->getPrix(),
                        'logoFournisseur' => $fournisseur->getLogo(),
                        'produit' => $product->getNom(),
                        'totalCommande' => $enchere->getTotalcommande(),
                        'commandeUser' => $commandeUser,
                        'annee' => $enchere->getFulldate()->format('Y'),
                        'mois' => $enchere->getFulldate()->format('m'),
                        'jour' => $enchere->getFulldate()->format('d'),
                        'minicom' => $product->getCommandemaximal(),
                    );
                }
            }
        }

        return $this->render('default/dashboard.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'user' => $user,
            'encheresNew' => $encheresNew,
            'encheresOld' => $encheresOld,
        ));
    }

    public function profilAction(Request $request, $iduser)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $member = $em->getRepository('AppBundle:User')->findOneById($iduser);

        $tabproduct = [];

        $favoris = $em->getRepository('ProductBundle:Favoris')->findByIdacheteur($member->getId());
        foreach($favoris as $favori) {
            $product = $em->getRepository('ProductBundle:Produit')->findOneById($favori->getIdproduit());

            $enchere = $em->getRepository('DealBundle:Encheres')->findOneByIdproduit($product->getId());
            if($enchere != NULL){
                $idenchere = $enchere->getId();
            }
            else {
                $idenchere = "non";
            }

            if($favori->getPrixfournisseur() != NULL) {
                $prixfourni = $favori->getPrixvente();
            }
            else {
                $prixfourni = "Aucun prix !";
            }

            $tabproduct [] = array(
                'nom' => $product->getNom(),
                'enchere' => $enchere,
                'idenchere' => $idenchere,
                'id' => $product->getId(),
                'prixprod' => $product->getPrixminimal(),
                'prixfourni' => $prixfourni,
                'idfav' => $favori->getId(),
            );
        }

        return $this->render('default/profil.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'user' => $user,
            'favoris' => $tabproduct,
            'member' => $member,
        ));
    }

    public function newUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $member = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $logo = $member->getLogo();

            $logoName = md5(uniqid()).'.'.$logo->guessExtension();
            $logoDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/logo';
            $logo->move($logoDir, $logoName);

            $member->setLogo($logoName);
            $member->setEnabled(1);

            $em->persist($member);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'L\'Utilisateur à été créé avec succès !')
            ;
        }

        return $this->render('default/addmember.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
    public function listUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $members = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/show_user.html.twig', array(
            'members' => $members,
            'user' => $user,
        ));
    }

    public function showNotifAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $notifs = $em->getRepository('AppBundle:Infos')->findByIduser($user->getId());
        foreach($notifs as $notif) {
            $notif->setEtat("read");

            $em->persist($notif);
            $em->flush();
        }
        $number = count($em->getRepository('AppBundle:Infos')->findBy(array(
            'iduser'=> $user->getId(),
            'etat' => 'unread',
        )));

        $user->setNotifs($number);
        $em->persist($user);
        $em->flush();

        return $this->render('default/show_notifs.html.twig', array(
            'user' => $user,
            'notifs' => $notifs,
        ));
    }
}
