<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NoteOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('orderId', TextType::class, [
					'label'	=> false,
					'attr'	=> [
						'style'	=> 'visibility:hidden'
					]
				])
				->add('note', NumberType::class, [
					'label'		=> 'Note',
					'required'	=> true,
				])
				->add('submit', SubmitType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'attr'	=> [ 'class' => 'inline' ],
            'data_class' => 'Apdc\ApdcBundle\Entity\NoteOrder'
        ));
    }
}
