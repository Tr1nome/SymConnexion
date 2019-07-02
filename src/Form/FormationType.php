<?php
namespace App\Form;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('hour', DateType::class, [
                'label' => 'date du prochain cours',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker','style'=>'cursor:pointer'
            ]])
            ->add('description',CKEditorType::class, array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    'title' => 'title',
                    'config.skin' => 'moono-dark'
                    //...
                ),))
            ->add('image',ImageType::class)
            ->add('day', null,array
            ('expanded'=>false,'multiple'=>false))
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
