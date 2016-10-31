<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FromToType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('from', TextType::class, [
			'required' => true,
			'label' => 'From:',
			'attr' => [
				'class' => 'form-control datepicker'
			]
		]);
		$builder->add('to', TextType::class, [
			'required' => false,
			'label' => 'To:',
			'attr' => [
				'class' => 'form-control datepicker'
			]
		]);
		$builder->add('Search', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\FromTo'
		));
	}
}
