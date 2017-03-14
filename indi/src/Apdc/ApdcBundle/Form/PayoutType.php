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
		$merchants = new Magento();

		$merchantChoices = [];
		foreach ($merchants->getApdcBankFields() as $key => $content) {
			$merchantChoices[$content['name']] = $content['name'];
		}
		//ksort($merchantChoices);

		$builder->add('ownerName', ChoiceType::class, [
			'label'		=> 'Magasin',
			'choices'	=> $merchantChoices,
			'required'	=> true,
			]
		);

		$builder->add('iban', TextType::class);

		$builder->add('reference', TextType::class)
				->add('shopperEmail', EmailType::class)
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
