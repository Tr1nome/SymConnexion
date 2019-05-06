<?php

namespace App\Form;

use App\Entity\ListItem;
use App\Entity\TaskList;
use App\Form\DataTransformer\TaskListToNumberTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListItemType extends AbstractType
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TaskListToNumberTransformer($this->em);
        $builder
            ->add('label')
            ->add('taskList',HiddenType::class)
        ;

        $builder->get('taskList')
            ->addModelTransformer($transformer)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListItem::class,
        ]);
    }
}
