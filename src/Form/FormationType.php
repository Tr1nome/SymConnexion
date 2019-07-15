<?php
namespace App\Form;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,['label'=>'Nom de la formation'])
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    'title' => 'title',
                    'config.skin' => 'moono-dark'
                    //...
                ),))
            ->add('hour', DateType::class, [
                'label' => 'date du prochain cours',
                'html5' => false,
                'attr' => ['class' => 'dropdown']
                ])
            ->add('time', TimeType::class, [
                'label' =>'heure',
                'widget' => 'choice',
                'html5' => false,
            ])
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
