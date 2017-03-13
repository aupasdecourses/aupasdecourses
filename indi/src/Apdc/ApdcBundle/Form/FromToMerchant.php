<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Apdc\ApdcBundle\Services\Magento;

class FromToMerchant extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$merchants = new Magento();

		$builder->add('from', TextType::class, [
			'required'	=> true,
			'label'		=> 'Date début:',
			'attr'		=> [
				'class'		=> 'form-control datepicker'
			]
		]);
		$builder->add('to', TextType::class, [
			'required'	=> false,
			'label'		=> 'Date fin:',
			'attr'		=> [
				'class'		=> 'form-control datepicker'
			]
		]);
		$choices = [];
		foreach ($merchants->getMerchants() as $storeid => $merchant) {
			foreach ($merchant as $com_id => $merch) {
				$choices[$merch['name'].' - '.$merch['store']] = $com_id;
			}
		}
		ksort($choices);
		$choices_final = array_merge(['All' => -1], $choices);
		$builder->add('merchant', ChoiceType::class, [
			'label'		=> 'Magasin:',
			'attr'		=> [
				'class'		=> 'form-control'
			],
			'choices'	=> $choices_final,
			'data'		=> -1
		]);
		$builder->add('Search', SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'attr' => [ 'class' => 'inline'],
			'data_class' => 'Apdc\ApdcBundle\Entity\FromToMerchant'
		));
	}
}
