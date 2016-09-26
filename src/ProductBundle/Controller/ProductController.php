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
            $product->setEtat('non');
            $product->setIdfournisseur(0);

            $em->persist($product);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Le Produit à été créé avec succès !')
            ;
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
                'favoris' => $favoris,
                'enchere' => $enchere,
                'idenchere' => $idenchere,
            );
        }

        return $this->render('produits/show_products.html.twig', array(
            'products' => $tabproducts,
            'user' => $user,
        ));
    }
    public function ficheProductAction(Request $request, $idproduct)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $product = $em->getRepository('ProductBundle:Produit')->findOneById($idproduct);

        $enchere = $em->getRepository('DealBundle:Encheres')->findOneByIdproduit($product->getId());

        if($enchere != NULL) {
            $fournisseur = $em->getRepository('AppBundle:User')->findOneById($enchere->getIdfournisseur());

            return $this->render('produits/fiche_product.html.twig', array(
                'product' => $product,
                'user' => $user,
                'enchere' => $enchere,
                'fournisseur' => $fournisseur,
            ));
        }

        return $this->render('produits/fiche_product.html.twig', array(
            'product' => $product,
            'user' => $user,
            'enchere' => $enchere,
        ));
    }
}