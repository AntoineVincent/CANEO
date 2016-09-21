<?php

namespace ProductBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Produit;
use ProductBundle\Form\ProductType;

class ProductController extends Controller
{
    public function newProductAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $product = new Produit();
        $form = $this->createForm('ProductBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $product->getPhoto();

            $photoName = md5(uniqid()).'.'.$photo->guessExtension();
            $photoDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/products';
            $photo->move($photoDir, $photoName);

            $product->setPhoto($photoName);

            $em->persist($product);
            $em->flush();
        }

        return $this->render('produits/new_product.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }

    public function listProductAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $products = $em->getRepository('ProductBundle:Produit')->findAll();

        foreach($products as $product) {
        	$favoris = $em->getRepository('ProductBundle:Favoris')->findBy(array(
        		'idproduit' => $product->getId(),
        		'idacheteur' => $user->getId()
        	));
        	if($favoris != NULL) {
        		$favoris = "oui";
        	}
        	else {
        		$favoris = "non";
        	}

        	$enchere = $em->getRepository('DealBundle:Encheres')->findOneByIdproduit($product->getId());
        	if($enchere != NULL) {
        		$enchere = "oui";
        	}
        	else {
        		$enchere = "non";
        	}

        	$tabproducts[] = array(
                'nom' => $product->getNom(),
                'prixminimal' => $product->getPrixminimal(),
                'commandemaximal' => $product->getCommandemaximal(),
                'favoris' => $favoris,
                'enchere' => $enchere,
            );
        }

        return $this->render('produits/show_products.html.twig', array(
        	'products' => $tabproducts,
        	'user' => $user,
        ));
    }
}