<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PayoutType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('reference', TextType::class);

		$builder->add('ownerName', TextType::class);

		$builder->add('iban', TextType::class);

		$builder->add('shopperEmail', EmailType::class)
				->add('shopperReference', TextType::class);

		$builder->add('value', MoneyType::class, [
			'divisor'	=> 100,
			'label'		=> 'Montant',
		]);

		$builder->add('date', DateTimeType::class, [
			'label' => false,
			'attr'	=> [
				'style' => 'visibility:hidden'
			]
		]);

		$builder->add('submit', SubmitType::class, [
			'label' => 'Confirmer',
			'attr'	=> [
				'onclick' => 'return confirm("Confirmer le virement commercant ?")' 
			]
		]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'data_class'		=> 'Apdc\ApdcBundle\Entity\IndiPayout',
			'csrf_protection'	=> true
        ));
    }
}
