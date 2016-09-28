<?php

namespace AppBundle\Form;

use FOS\UserBundle\Util\LegacyFormHelper;
use OrthoBundle\Repository\CategorieUtilisateursRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Identifiant : ',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('plain_password', 'password', array(
                'label' => 'Mot de passe : '
            ))
            ->add('nom', 'text', array(
                'label' => "Nom de l'utilisateur : "
            ))
            ->add('adressefactu', 'text', array(
                'label' => 'Adresse de facturation: ',
                'attr' => array(
                    'placeholder' => 'N°, Rue'
                )
            ))
            ->add('cpfactu', 'text', array(
                'label' => 'Code Postal de facturation : ',
                'attr' => array(
                    'placeholder' => 'Code Postal'
                )
            ))
            ->add('villefactu', 'text', array(
                'label' => 'Ville de facturation : ',
                'attr' => array(
                    'placeholder' => 'Ville'
                )
            ))
            ->add('adresselivraison', 'text', array(
                'label' => 'Adresse de livraison : ',
                'attr' => array(
                    'placeholder' => 'N°, Rue'
                )
            ))
            ->add('cplivraison', 'number', array(
                'label' => 'Code Postal de livraison : ',
                'attr' => array(
                    'placeholder' => 'Code Postal'
                )
            ))
            ->add('villelivraison', 'text', array(
                'label' => 'Ville de livraison : ',
                'attr' => array(
                    'placeholder' => 'Ville'
                )
            ))
            ->add('telephone', 'text', array(
                'label' => 'Téléphone : ',
                'attr' => array(
                    'placeholder' => '09.87.65.43.21'
                )
            ))
            ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array(
                'label' => 'E-mail :',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('mailbis',EmailType::class, array(
                'required' => false,
                'label' => 'E-mail secondaire : '
            ))
            ->add('infos', 'textarea', array(
                'label' => 'CGV (si nouveau membre est un fournisseur) : ',
                'attr' => array(
                    'nullable' => true,
                ),
                'attr' => array(
                    'placeholder' => 'Indiquer ici conditions générales de vente.'
                )
            ))
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'fournisseur' => 'fournisseur',
                    'acheteur' => 'acheteur',
                ),
                'label' => 'Type d Utilisateur : ',
                'placeholder' => 'Selectionner...',
                'empty_data'  => null
            ))
            ->add('logo', FileType::class, array(
                'label' => 'Logo : ',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Selectionnez un fichier'
                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

}
