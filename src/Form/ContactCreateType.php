<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contact', null, [
                'attr' => ['maxlength' => 50],
            ])
            ->add('numero', null, [
                'attr' => [
                    'maxlength' => 13,
                    'pattern' => '\\d*',
                    'inputmode' => 'numeric',
                    'placeholder' => 'Ex: 0341234567'
                ],
            ])
            ->add('email', null, [
                'attr' => ['maxlength' => 100],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
