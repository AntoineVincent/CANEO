<?php

namespace ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'label' => 'Nom du produit : ',
                'attr' => array(
                    'placeholder' => 'Nom du produit'
                )
            ))
            ->add('prixminimal', 'number', array(
                'label' => 'Prix maximum à l\'unité au démarrage des enchères : ',
                'attr' => array(
                    'placeholder' => 'Prix en euros (xx.xx)'
                )
            ))
            ->add('commandemaximal', 'integer', array(
                'label' => 'Nombre minimal de produit pour une commande : ',
                'attr' => array(
                    'placeholder' => ''
                )
            ))
            ->add('photo', FileType::class, array('label' => 'Photo du produit : '))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProductBundle\Entity\Produit',
        ));
    }
}