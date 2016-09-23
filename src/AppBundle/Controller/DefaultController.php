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

        return $this->render('default/dashboard.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'user' => $user,
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
            $tabproduct [] = array(
                'nom' => $product->getNom(),
                'enchere' => $enchere,
                'idenchere' => $idenchere,
                'id' => $product->getId(),
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
