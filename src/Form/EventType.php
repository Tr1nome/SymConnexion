<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    'title' => 'title',
                    'config.skin' => 'moono-dark'
                    //...
                ),))
            ->add('max')
            ->add('hour', DateType::class, [
                'label' => 'Date de l\'évènement',
                'html5' => false,
                'attr' => ['class' => 'dropdown']
                ])
            ->add('time', TimeType::class, [
                'label' =>'Heure de début',
                'widget' => 'choice',
                'html5' => false,
            ])
            ->add('place')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
