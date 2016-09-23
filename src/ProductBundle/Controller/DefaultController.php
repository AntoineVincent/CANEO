<?php

namespace ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Favoris;

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

	        $tabproducts[] = array(
	            'id' => $product->getId(),
	            'nom' => $product->getNom(),
	            'prixminimal' => $product->getPrixminimal(),
	            'commandemaximal' => $product->getCommandemaximal(),
	            'enchere' => $enchere,
	            'idenchere' => $enchere->getId(),
	        );
    	}


        return $this->render('default/fav_show.html.twig', array(
        	'user' =>$user,
        	'products' => $tabproducts,
        ));
    }
}
