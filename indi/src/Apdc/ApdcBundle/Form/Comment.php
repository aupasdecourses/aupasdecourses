<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Apdc\ApdcBundle\Services\Stats;
use Apdc\ApdcBundle\Services\Magento;

class Comment extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$stats = new Stats();
		$magento = new Magento();
		
		$choices = [];
		foreach ($stats->getCommentsType() as $t) {
			$choices[$t['label']] = $t['type'];
		}
		$choices = array_merge(['Selectionner un type' => ''], $choices);

		$builder->add('type', ChoiceType::class, [
			'required'	=> true,
			'label'		=> 'Type de commentaire',
			'attr'		=> [
				'class'		=> 'form-control'
			],
			'choices'	=> $choices,
			'group_by'	=> function($key, $value, $index) {
				if (strpos($key, "not_visible") !== false) {
					return 'Commentaires internes';
				}
				// TODO
				// if (strpos($key, "is_visible") !== false) {
				// 	return 'Commentaires visibles';
				// }
			},
		]);

		$builder->add('order_id', TextType::class, [
			'required'	=> true,
			'label'		=> '# Commande',
			'attr'		=> [
				'class'			=> 'form-control',
				'placeholder'	=> '# Commande',
			]
		]);

		$merchants = [];
		foreach ($magento->getMerchantsByStore() as $storeid => $merch) {
			foreach ($merch as $com_id => $m) {
				$merchants[$m['name']] = $com_id;
			}
		}
		ksort($merchants);
		$merchants = array_merge(['Aucun commercant' => 0], $merchants);
		$merchants = array_merge(['Tous les commercants' => -1], $merchants);
		$merchants = array_merge(['Selectionner un commercant' => ''], $merchants);
		
		$builder->add('merchant_id', ChoiceType::class, [
			'required'	=> true,
			'label'		=> 'Commercant',
			'attr'		=> [
				'class'		=> 'form-control'
			],
			'choices'	=> $merchants,
		]);

		$builder->add('text', TextareaType::class, [
			'required'	=> true,
			'label'		=> 'Commentaire',
			'attr'		=> [
				'class'			=> 'form-control',
				'placeholder'	=> 'Commentaire',
			]
		]);

		$builder->add('Creer', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class'	=> 'Apdc\ApdcBundle\Entity\Comment'
		]);
	}
}