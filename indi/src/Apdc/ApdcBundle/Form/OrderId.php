<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderId extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('id', TextType::class, [
			'required'	=> true,
			'label'		=> '# Commande:',
			'attr'		=> [
				'class'		=>	'form-control'
			]
		]);
        $builder->add('Search', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'attr' => [ 'class' => 'inline'],
			'data_class' => 'Apdc\ApdcBundle\Entity\OrderId'
		));
	}
}
