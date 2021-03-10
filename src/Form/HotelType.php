<?php

namespace App\Form;

use App\Entity\Hotel;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HotelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('stars')
            ->add('photo',FileType::class, [
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => false,])
            ->add('description', CKEditorType::class, [
                'config'=> [
                    'uiColor' => "#e2e2e2",
                    'toolbar' => 'full',
                    'required' => true
                ]
            ])
            ->add('adress')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hotel::class,
        ]);
    }
}
