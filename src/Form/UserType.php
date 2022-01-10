<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [            
            'constraints' => [
                new Email(),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Administrateur' => "ROLE_ADMIN",
                    'Utilisateur' => "ROLE_USER",
                ],
            ])  
            
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $formEvent) {
                $form = $formEvent->getForm();
                $user = $formEvent->getData();
                
                if(is_null($user->getId()))
                { //! on est en création le mot de passe est obligatoire
                    $form->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,    // correspond au type de champs à répéter
                        'invalid_message' => 'Les mots de passe ne correspondent pas.',
                        // on ne veut pas le require ce champs car (pour l'instant) cela nous forcerait
                        // à resaisir le mot de passe lors de l'édition
                        'required' => true, 
                        // les labels pour chaque champs affiché
                        'first_options'  => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Répétez le mot de passe'],
                        'constraints' => [
                            new NotBlank()
                        ],        
                        // on spécifie au composant formulaire 
                        // de ne pas se charger d'enregistrer cette valeur dans l'objet
                        'mapped' => false, 
                    ]);
                }
                else 
                { //! on est en édition, le mot de passe n'est pas obligatoire
                    $form->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe ne correspondent pas.',
                        'mapped' => false, 
                        'required' => false, 
                        'first_options'  => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Répétez le mot de passe'],
                    ]);
                }
            });
            
        //? Data transformer trouvé sur https://dthlabs.com/select-or-checkbox-in-a-symfony5-form-for-user-roles/
        // $builder->get('roles')
        //     ->addModelTransformer(new CallbackTransformer(
        //         function ($rolesArray) {
                    //  transform the array to a string
        //              return count($rolesArray)? $rolesArray[0]: null;
        //         },
        //         function ($rolesString) {
                    //  transform the string back to an array
        //              return [$rolesString];
        //         }
        // )); 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
