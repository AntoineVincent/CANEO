<?php

namespace DealBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DealBundle\Entity\Commandes;

class CommandController extends Controller
{

    public function newCmdAction(Request $request, $idenchere)
    {
    	$em = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();

    	$cmd = $em->getRepository('DealBundle:Commandes')->findOneBy(array(
	    	'idacheteur' => $user->getId(),
	    	'idenchere' => $idenchere,
	    ));
	    $enchere = $em->getRepository('DealBundle:Encheres')->findOneById($idenchere);
	    $product = $em->getRepository('ProductBundle:Produit')->findOneById($enchere->getIdproduit());

    	$quantite = $request->request->get('quantite');

    	if($user->getType() != "acheteur") {
    		$request->getSession()
    		->getFlashBag()
		    ->add('danger', 'Vous n êtes pas un acheteur vous ne devriez pas être sur cette page !')
		    ;
    	}
    	else {
    		if($quantite != NULL) {
    			if($quantite < $product->getCommandemaximal()) {
    				$request->getSession()
		            ->getFlashBag()
		            ->add('danger', 'Quantité insuffisante ! Veuillez réessayer')
		            ;
    			}
    			else {
		    		if($cmd != NULL) {
		    			$cmd->setNbredecommande($cmd->getNbredecommande() + $quantite);
		    			$enchere->setTotalcommande($enchere->getTotalcommande() + $quantite);

		    			$em->persist($cmd);
		    			$em->flush();
		    			$em->persist($enchere);
		    			$em->flush();

		    			$request->getSession()
		    			->getFlashBag()
		            	->add('success', 'Votre commande à bien été prise en compte, merci !')
		            	;
		    		}
		    		else {
		    			$cmd = new Commandes();
		    			$cmd->setIdacheteur($user->getId());
		    			$cmd->setIdenchere($idenchere);
		    			$cmd->setNbredecommande($quantite);
		    			$enchere->setTotalcommande($enchere->getTotalcommande() + $quantite);

		    			$em->persist($cmd);
		    			$em->flush();
		    			$em->persist($enchere);
		    			$em->flush();

		    			$request->getSession()
		    			->getFlashBag()
		            	->add('success', 'Votre commande à bien été prise en compte, merci !')
		            	;
		    		}
		    	}
		    }
    	}

    	return $this->render('commands/new_command.html.twig', array(
            'user' => $user,
            'cmd' => $cmd,
            'enchere' => $enchere,
            'product' => $product,
        ));
    }
}