<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Infos;
use AppBundle\Form\UserType;
use AppBundle\Form\UsermType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $encheresNew = [];
        $encheresOld = [];
        
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
            elseif($verif->getDatemid()->format('Y/m/d') == $datenow) {
                $userFavs = $em->getRepository('ProductBundle:Favoris')->findByIdproduit($verif->getIdproduit());
                foreach($userFavs as $fav) {
                    $member = $em->getRepository('AppBundle:User')->findOneById($fav->getIdacheteur());

                    if($member->getType() == "acheteur" && $verif->getEtatnew() == "normal") {
                        $notif = new Infos();
                            $notif->setIduser($member->getId());
                            $notif->setIdenchere($verif->getId());
                            $notif->setMessage("La vente n°".$verif->getId()." est à la moitié de sa durée. Il vous reste 2 semaine pour passer vos commandes sur le produit.");
                            $notif->setEtat("unread");
                            $notif->setCreatedAt($datenow);

                        $em->persist($notif);
                        $em->flush();

                        $message = \Swift_Message::newInstance()
                            ->setSubject('CANEO : Milieu de vente')//objet du mail
                            ->setFrom(array('anton51200@laposte.net' => 'CANEO Website[Do not reply]')) //adresse expéditeur
                            //->setReadReceiptTo('ninon.pelaez@gmail.com') //accusé de réception
                            ->setTo($member->getEmailCanonical()) //adresse du cabinet qui commande
                            // ->setTo('anton071192@gmail.com') //adresse du cabinet qui commande
                            ->setCharset('utf-8')
                            ->setContentType('text/html')
                            //contenu du mail
                            ->setBody("La vente n°".$verif->getId()." est à la moitié de sa durée. Il vous reste 2 semaine pour passer vos commandes sur le produit. Vous pouvez acceder à la vente directement sur votre espace.");
                             $this->get('mailer')->send($message); //action d'envoi

                        $verif->setEtatnew('normall');
                        $em->persist($verif);
                        $em->flush();
                    }
                }
            }
            elseif($verif->getDateold()->format('Y/m/d') == $datenow) {
                $userFavs = $em->getRepository('ProductBundle:Favoris')->findByIdproduit($verif->getIdproduit());
                foreach($userFavs as $fav) {
                    $member = $em->getRepository('AppBundle:User')->findOneById($fav->getIdacheteur());

                    if($member->getType() == "fournisseur" && $member->getId() != $verif->getIdfournisseur() && $verif->getEtatnew() != 'old') {
                        $notif = new Infos();
                            $notif->setIduser($member->getId());
                            $notif->setIdenchere($verif->getId());
                            $notif->setMessage("La vente n°".$verif->getId()." est bientot terminé. Il vous reste 1 semaine pour devenir le fournisseur de cette vente. Vous pouvez acceder à la vente ");
                            $notif->setEtat("unread");
                            $notif->setCreatedAt($datenow);

                        $em->persist($notif);
                        $em->flush();

                        $message = \Swift_Message::newInstance()
                            ->setSubject('CANEO : Vente bientôt terminée')//objet du mail
                            ->setFrom(array('anton51200@laposte.net' => 'CANEO Website[Do not reply]')) //adresse expéditeur
                            //->setReadReceiptTo('ninon.pelaez@gmail.com') //accusé de réception
                            ->setTo($member->getEmailCanonical()) //adresse du cabinet qui commande
                            // ->setTo('anton071192@gmail.com') //adresse du cabinet qui commande
                            ->setCharset('utf-8')
                            ->setContentType('text/html')
                            //corps du texte : valeurs à appeler dans la vue mail_cabinet.html.twig
                            ->setBody("La vente n°".$verif->getId()." est bientot terminé. Il vous reste 1 semaine pour devenir le fournisseur de cette vente. Vous pouvez acceder à la vente sur votre espace personnel.");
                             $this->get('mailer')->send($message); //action d'envoi
                    }

                    $verif->setEtatnew('old');
                    $em->persist($verif);
                    $em->flush();
                }
            }
        }

        $encheres = $em->getRepository('DealBundle:Encheres')->findByEtat('open');
        if($encheres != NULL) {
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

        $test = $request->request->get('passformail');
        // var_dump($test);exit;

        if ($form->isSubmitted() && $form->isValid()) {

            if($member->getLogo() != NULL) {
                $logo = $member->getLogo();

                $logoName = md5(uniqid()).'.'.$logo->guessExtension();
                $logoDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/logo';
                $logo->move($logoDir, $logoName);

                $member->setLogo($logoName);
            }
            $member->setEnabled(1);
            $member->setRoles(array('ROLE_USER'));

            $em->persist($member);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('CANEO : Validation d inscription')//objet du mail
                ->setFrom(array('anton51200@laposte.net' => 'CANEO Website[Do not reply]')) //adresse expéditeur
                // ->setTo($member->getEmailCanonical())
                ->setTo($member->getEmailCanonical())
                // ->setTo('scan@neo3d.fr')
                ->setCharset('utf-8')
                ->setContentType('text/html')
                //corps du texte :
                ->setBody("Votre inscription a bien été faites, voici vos identifiants :
                     <br><br> Identifiant : ".$member->getUsername().
                    "<br> Email : ".$member->getEmailCanonical().
                    "<br> Mot de Passe : ".$test.
                    "<br><br> Vous pouvez dès à présent vous connecter sur le site. Merci de votre inscription."
                );
            $this->get('mailer')->send($message); //action d'envoi

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'L\'Utilisateur à été créé avec succès ! Un e-mail de confirmation lui a été envoyé.')
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

    public function modifUserAction(Request $request, $iduser) 
    {   
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $editForm = $this->createForm('AppBundle\Form\UsermType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $infos = $request->request->get('infos');
            $user->setInfos($infos);

            $em->persist($user);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Profil modifié avec succès !')
            ;
        }

        return $this->render('user/modif_user.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
        ));
    }

    public function askRegisterAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager();

        $type = $request->request->get('type');
        $identifiant = $request->request->get('identifiant');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $email2 = $request->request->get('email2');
        $nom = $request->request->get('nom');
        $cgv = $request->request->get('cgv');
        $telephone = $request->request->get('telephone');
        $adressefactu = $request->request->get('adressefactu');
        $adresselivr = $request->request->get('adresselivr');
        $cpfactu = $request->request->get('cpfactu');
        $cplivr = $request->request->get('cplivr');
        $villefactu = $request->request->get('villefactu');
        $villelivr = $request->request->get('villelivr');

        if($email != NULL) {
            $message = \Swift_Message::newInstance()
                ->setSubject('CANEO : Demande d inscription')//objet du mail
                ->setFrom(array('anton51200@laposte.net' => 'CANEO Website[Do not reply]')) //adresse expéditeur
                ->setTo('scan@neo3d.fr')
                // ->setTo('scan@neo3d.fr')
                ->setCharset('utf-8')
                ->setContentType('text/html')
                //corps du texte :
                ->setBody("Une nouvelle demande d'inscription à été faite :
                     <br><br> type : ".$type.
                    "<br> identifiant : ".$identifiant.
                    "<br> email : ".$email.
                    "<br> password : ".$password.
                    "<br> email2 : ".$email2.
                    "<br> nom : ".$nom.
                    "<br> cgv : ".$cgv.
                    "<br> telephone : ".$telephone.
                    "<br> adressefactu : ".$adressefactu.
                    "<br> cpfactu : ".$cpfactu.
                    "<br> villefactu : ".$villefactu.
                    "<br> adresselivr : ".$adresselivr.
                    "<br> cplivr : ".$cplivr.
                    "<br> villelivr : ".$villelivr
                );
            $this->get('mailer')->send($message); //action d'envoi

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'INFORMATION : Votre demande d\'inscription a bien été prise en compte, Un e-mail de confirmation vous sera envoyé lorsque votre inscription aura été prise en compte  !')
            ;
        }

        return $this->render('user/ask_register.html.twig', array(

        ));
    }
}
