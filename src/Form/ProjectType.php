<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('team',null,['label'=>'Equipe'])
            ->add('creator',null,['label'=>'Créateur du projet'])
            ->add('needed',null,['label'=>'Nombre d\'utilisateurs requis pour valider le projet'])
            ->add('validated',null,['label'=>'Projet validé'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
