<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'constraints' =>  [
                    new NotBlank(), // Note: It is not working...
                ]
            ])
            ->add('sku')
            ->add('ref')
            ->add('notes_com')
            ->add('status')  // TODO: ChoiceType, 0 / 1
            ->add('on_selection')  // TODO: ChoiceType, 0 / 1
            ->add('price')
            ->add('unite_prix', null, [
                'empty_data' => '1'
            ])  // TODO: ChoiceType, and get choice form Mage
            ->add('short_description')
            ->add('poids_portion', null, [
                'empty_data' => '500'
            ])
            ->add('nbre_portion', null, [
                'empty_data' => '1'
            ])
            ->add('tax_class_id', null, [
                'empty_data' => '5'
            ]) // TODO: ChoiceType, and get choice form Mage
            ->add('origine')  // TODO: ChoiceType, and get choice form Mage
            ->add('produit_biologique')  // TODO: ChoiceType, and get choice form Mage
            ->add('shop')  // TODO: ChoiceType, and get choice form Mage
            ->add('photoFile')
            ->add('attribute_set_id', null, [
                'data' => '4',
            ])
            ->add('type_id', null, [
                'data' => 'simple'
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
            ]
        );
    }
}
