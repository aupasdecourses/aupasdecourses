<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Apdc\ApdcBundle\Services\Stats;

class Comment extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$stats = new Stats();
		$choices = [];

		foreach ($stats->getCommentsType() as $t) {
			$choices[$t['label']] = $t['type'];
		}

		$builder->add('type', ChoiceType::class, [
			'label'		=> false,
			'attr'		=> [
				'class'		=> 'form-control'
			],
			'choices'		=> $choices,
		]);
		$builder->add('Creer', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'attr'			=> ['class' => 'inline'],
			'data_class'	=> 'Apdc\ApdcBundle\Entity\Comment'
		]);
	}
}