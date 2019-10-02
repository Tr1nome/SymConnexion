<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\TaskListToNumberTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('file',FileType::class,array('label' => 'image','required' => false))
            ->add('project',HiddenType::class)
            ->add('allowed', ChoiceType::class, [
                'label'=> 'Autoriser ?',
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
