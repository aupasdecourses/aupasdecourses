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
		$choices = array_merge(['Type' => 'default'], $choices);

		$builder->add('type', ChoiceType::class, [
			'label'		=> 'Type de commentaire',
			'attr'		=> [
				'class'		=> 'form-control'
			],
			'choices'	=> $choices,
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
		$merchants = array_merge(['Commercant' => -1], $merchants);
		
		$builder->add('merchant_id', ChoiceType::class, [
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