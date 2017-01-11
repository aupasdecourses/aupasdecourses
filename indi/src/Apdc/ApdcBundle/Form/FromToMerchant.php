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
			'required' => true,
			'label' => 'Date début:',
			'attr' => [
				'class' => 'form-control datepicker'
			]
		]);
		$builder->add('to', TextType::class, [
			'required' => false,
			'label' => 'Date fin:',
			'attr' => [
				'class' => 'form-control datepicker'
			]
		]);
		$merchants->getMerchants();
		$choices = ['All' => -1];
		foreach($merchants as $com_id => $merchant) {
			$choices[$merchant['name']] = $com_id;
		}
		$builder->add('merchant', ChoiceType::class, [
			'label' => 'Magasins:',
			'attr' => [
				'class' => 'form-control'
			],
			'choices' => $choices,
			'data' => -1
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
