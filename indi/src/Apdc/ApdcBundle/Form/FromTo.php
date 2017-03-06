<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FromTo extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('from', TextType::class, [
			'required'	=> true,
			'label'		=> 'Date début:',
			'attr'		=> [
				'class'		=> 'form-control datepicker'
			]
		]);
		$builder->add('to', TextType::class, [
			'required'	=> true,
			'label'		=> 'Date fin:',
			'attr'		=> [
				'class'		=> 'form-control datepicker'
			]
		]);
		$builder->add('Search', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'attr' => [ 'class' => 'inline'],
			'data_class' => 'Apdc\ApdcBundle\Entity\FromTo'
		));
	}
}
