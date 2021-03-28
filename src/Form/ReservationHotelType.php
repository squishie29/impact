<?php

namespace App\Form;

use App\Entity\Hotel;
use App\Entity\ReservationHotel;
use App\Entity\Room;
use App\Repository\RoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationHotelType extends AbstractType
{

//    public function __construct($id)
//    {
//        $this->id = $id;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $hotel = $options['hotel'];
        $builder
            ->add('debut')
            ->add('fin')
            ->add('userId')
            ->add('roomId', EntityType::class, array(
                'class' => Room::class,
                'placeholder' => 'Choose an Room',
                'required' => true,
                'choice_label' => function ($Room) {
                    return $Room->getType();
                },
//                'query_builder' => function(RoomRepository $er) use ($id) {
//                    return $er->createQueryBuilder('e')
//                        ->where('e.hotel = hotel')
//                        ->setParameter('hotel', $id)
//                        ;
//                },
            ))
            ->add('confirmation', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReservationHotel::class,
        ]);
    }
}
