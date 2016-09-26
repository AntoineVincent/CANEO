<?php

namespace ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Favoris;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use AppBundle\Entity\Infos;


class DefaultController extends Controller
{
    public function showFavorisAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	$favoris = $em->getRepository('ProductBundle:Favoris')->findByIdacheteur($user->getId());

    	$tabproducts = [];

    	foreach($favoris as $favori) {

    		$product = $em->getRepository('ProductBundle:Produit')->findOneById($favori->getIdproduit());

	        $enchere = $em->getRepository('DealBundle:Encheres')->findOneByIdproduit($product->getId());
	        if($enchere != NULL){
	        	$idenchere = $enchere->getId();
	        }
	        else {
	        	$idenchere = "non";
	        }

	        $tabproducts[] = array(
	            'id' => $product->getId(),
	            'nom' => $product->getNom(),
	            'prixminimal' => $product->getPrixminimal(),
	            'commandemaximal' => $product->getCommandemaximal(),
	            'enchere' => $enchere,
	            'idenchere' => $idenchere,
	        );
    	}


        return $this->render('default/fav_show.html.twig', array(
        	'user' =>$user,
        	'products' => $tabproducts,
        ));
    }

    public function addDeleteFavAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	$idproduit = $this->getRequest()->request->get('idproduit');

    	$verifFav = $em->getRepository('ProductBundle:Favoris')->findOneBy(array(
    		'idacheteur' => $user->getId(),
    		'idproduit' => $idproduit,
    	));

    	if($verifFav != NULL) {
    		$em->remove($verifFav);
    		$em->flush();
	    	
	    	$msg = "remove";
    	}
    	else {
    		$favoris = new Favoris();

	    	$favoris->setIdacheteur($user->getId());
	    	$favoris->setIdproduit($idproduit);

	    	$em->persist($favoris);
	    	$em->flush();

	    	$msg = "added";
    	}

    	$tab = [];
        $tab[] = array(
            'message' => $msg,
            );
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($tab, 'json');

        $response = new Response($jsonContent);
        return $response;
    }

    public function updatePriceFavAction(Request $request, $idfavori)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $prixvente = $request->request->get('newprice');
        $prixfourni = $request->request->get('total');
        $datenotif = $request->request->get('datenotif');

        $favori = $em->getRepository('ProductBundle:Favoris')->findOneById($idfavori);

        if($prixvente != NULL) {

            $favori->setPrixvente($prixvente);
            $favori->setPrixfournisseur($prixfourni);

            $em->persist($favori);
            $em->flush();

            $product = $em->getRepository('ProductBundle:Produit')->findOneById($favori->getIdproduit());
            $oldfourni = $product->getIdfournisseur();

            if($product->getPrixminimal() > $prixvente) {
                $product->setPrixminimal($prixvente);
                $product->setIdfournisseur($user->getId());

                if($oldfourni != 0 && $user->getId() != $oldfourni) {
                    $notif = new Infos();
                    $notif->setIduser($oldfourni);
                    $notif->setIdproduit($product->getId());
                    $notif->setMessage("Vous n'avez plus le prix le moins cher pour le produit : ".$product->getNom().". Il est desormais vendu pour le prix de ".$prixvente."€");
                    $notif->setEtat("unread");
                    $notif->setCreatedAt($datenotif);
                    // var_dump($notif);exit;
                    $em->persist($notif);
                    $em->flush();
                }

                $em->persist($product);
                $em->flush();
            }

             $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Votre prix à bien été enregistré !')
                    ;

            return $this->redirectToRoute('profil', array('iduser' => $user->getId()));


        }

        return $this->render('default/fav_update.html.twig', array(
            'user' =>$user,
            'favori' => $favori,
        ));
    }
}
