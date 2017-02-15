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

class PayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('value',				MoneyType::class, array(
					'divisor' => 100,
				))
				->add('iban',				TextType::class)
				->add('ownerName',			TextType::class)
				->add('reference',			TextType::class)
				->add('shopperEmail',		EmailType::class)
				->add('shopperReference',	TextType::class)
				->add('date',				DateTimeType::class,
					array('label' => false,
					'attr'=>array('style' => 'visibility:hidden'	
				)))
				->add('submit',				SubmitType::class);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'data_class'		=> 'Apdc\ApdcBundle\Entity\Payout',
			'csrf_protection'	=> true
        ));
    }
}
