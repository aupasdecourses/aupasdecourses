<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Apdc\ApdcBundle\Services\Magento;

class PayoutType extends AbstractType
{	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('reference', TextType::class);


		$merchants = new Magento();

//		$displayedChoices = [];
		$merchantChoices = [];
		$ibanChoices = [];
		foreach ($merchants->getApdcBankFields() as $key => $content) {
//			$displayedChoices[$content['name'].' - '.$content['iban']]	= $content['name'].' / '.$content['iban'];
			$merchantChoices[$content['name'].' - '.$content['iban']]	= $content['name'];
			$ibanChoices[$content['name'].' - '.$content['iban']] = $content['iban'];

		}
		ksort($displayedChoices);
		ksort($merchantChoices);
		ksort($ibanChoices);


/*
		echo'<pre>';
		print_R($displayedChoices);
		echo'<pre>';


		echo'<pre>';
		print_R($merchantChoices);
		echo'<pre>';



		echo'<pre>';
		print_R($ibanChoices);
		echo'<pre>';
 */



		$builder->add('ownerName', ChoiceType::class, [
			'label'		=> 'Commercant',
			'choices'	=> 	$merchantChoices,
			'required'	=> true,
			]
		);

		$builder->add('iban', ChoiceType::class, [
			'label'		=> 'Iban',
			'choices'	=> $ibanChoices,
			'required'	=> true,
			]

		);

		$builder->add('shopperEmail', EmailType::class)
				->add('shopperReference', TextType::class);
			


		$builder->add('value', MoneyType::class, [
			'divisor' => 100, 
			]
		);
	
		$builder->add('date', DateTimeType::class, [
					'label' => false,
					'attr'	=> [
						'style' => 'visibility:hidden'
					]
				]);
		

		$builder->add('submit', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'data_class'		=> 'Apdc\ApdcBundle\Entity\Payout',
			'csrf_protection'	=> true
        ));
    }
}
