<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Login extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', TextType::class, [
			'required' => true,
			'label' => 'Username:',
			'attr' => [
				'class' => 'form-control'
			]
		]);
		$builder->add('password', PasswordType::class, [
			'required' => true,
			'label' => 'Password:',
			'attr' => [
				'class' => 'form-control'
			]
		]);
		$builder->add('Login', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Apdc\ApdcBundle\Entity\Login'
		));
	}
}
