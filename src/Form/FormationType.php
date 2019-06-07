<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class FormationType extends AbstractType
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
            ->add('image',ImageType::class)
            ->add('user', null,array
            ('expanded'=>true,'multiple'=>true))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
