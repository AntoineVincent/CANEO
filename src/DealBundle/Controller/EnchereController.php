<?php

namespace DealBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DealBundle\Entity\Encheres;

class EnchereController extends Controller
{

    public function allEnchereAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	$tabenchere = [];

        $encheres = $em->getRepository('DealBundle:Encheres')->findAll();
        foreach($encheres as $enchere) {
        	$product = $em->getRepository('ProductBundle:Produit')->findOneById($enchere->getIdproduit());
        	$fournisseur = $em->getRepository('AppBundle:User')->findOneById($enchere->getIdfournisseur());

        	$tabenchere [] = array (
        		'prix' =>  $enchere->getPrix(),
        		'logoFournisseur' => $fournisseur->getLogo(),
        		'produit' => $product->getNom(),
        		'totalCommande' => $enchere->getTotalcommande(),
        	);
        }


        return $this->render('encheres/all_enchere.html.twig', array(
            'user' => $user,
            'tabenchere' => $tabenchere,
        ));
    }

}