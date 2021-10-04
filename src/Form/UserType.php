<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\Migrations\Version\Direction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password',PasswordType::class,[
                'invalid_message' => 'Le mot de passe de contenir au moins une lettre et un nombre et minimum 8 caractères'
            ])
            ->add('nom')
            ->add('prenom')
            ->add('secteur',ChoiceType::class, [
                'choices' => [
                    'RH'=> 'RH',
                    'Informatique' => 'Informatique',
                    'Comptabilité' => 'Comptabilité',
                    'Direction' => 'Direction',
                ]
            ])
            
            ->add('contrat',ChoiceType::class, [
                'choices' => [
                    'CDI'=> 'CDI',
                    'CDD' => 'CDD',
                    'Interim' => 'Interim',
                ]
            ])
            ->add('dateDeSortie',DateType::class, [
                'placeholder' => '',
                'input' => 'string',
                'years' => range(date('Y')-1, date('Y')+100),
                'required'=>false])
            ->add('photo',FileType::class,[
                'label' => 'Image',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                        ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
