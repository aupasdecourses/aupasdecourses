<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Submit extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
//		$builder->add('Submit', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'attr' => [ 'class' => 'inline'],
		));
	}
}
