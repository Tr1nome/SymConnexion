<?php

namespace App\Form;

use App\Entity\ListItem;
use App\Entity\TaskList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('listItems',CollectionType::class,array(
                'entry_type'=>ListItemType::class,
                'entry_options'=>array(
                    'label'=>false,
                    'attr'=>array('class'=>'listitem')
                ),
                'prototype'=>true,
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskList::class,
        ]);
    }
}
