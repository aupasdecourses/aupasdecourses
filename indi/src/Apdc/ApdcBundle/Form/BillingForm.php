<?php

namespace Apdc\ApdcBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BillingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('discount_shop_HT', MoneyType::class, [
                    'label' => 'Remise Commerciale',
                    'label_attr' => ['style' => 'margin-right:10px;'],
                    'attr' => ['placeholder' => 'Valeur HT','style'=>'width:100px;'],
                    'divisor' => 100,
                    'data' => 0,
        ]);

        $builder->add('discount_shop_TVA_percent', PercentType::class, [
                    'scale' => 2,
                    'label_attr' => ['class' => 'sr-only'],
                    'attr' => ['placeholder' => '% TVA', 'style' => 'margin-left:10px;width:100px;','readonly' => true],
                    'type' => 'fractional',
                    'data' => 0.2,
                    //'disabled' => true,
        ]);

        $builder->add('comments_discount_shop', TextType::class, [
                    'label_attr' => ['class' => 'sr-only'],
                    'attr' => ['placeholder' => 'Commentaire', 'style' => 'margin-left:10px;width:200px;'],
                    'required' => false,
        ]);

        $builder->add('processing_fees_HT', MoneyType::class, [
                    'label' => 'Frais bancaires',
                    'label_attr' => ['style' => 'margin-left: 20px;margin-right:10px'],
                    'attr' => ['placeholder' => 'Valeur HT','style'=>'width:100px;'],
                    'divisor' => 100,
                    'data' => 50,
                    //'disabled' => true,
        ]);

        $builder->add('processing_fees_TVA_percent', PercentType::class, [
                    'scale' => 2,
                    'label_attr' => ['class' => 'sr-only'],
                    'attr' => ['placeholder' => '%TVA','style' => 'margin-left:10px;width:100px;', 'readonly' => true],
                    'type' => 'fractional',
                    'data' => 0,
                    //'disabled' => true,
        ]);
        $builder->add('Finaliser Facture', SubmitType::class, [
        			'attr' => ['class'=>'btn btn-success btn-lg right', 'style'=>'margin-left:10px;'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => ['class' => 'form-inline'],
            'data_class' => 'Apdc\ApdcBundle\Entity\Billing',
            'csrf_protection' => true,
        ));
    }
}
