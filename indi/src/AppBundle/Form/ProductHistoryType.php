<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductHistoryType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sku')
            ->add('reference_interne_magasin', null, [
                'property_path' => 'ref',
            ])
            ->add('status', null, [
                'property_path' => 'available',
            ])  // TODO: ChoiceType, 0 / 1
            ->add('on_selection', null, [
                'property_path' => 'selected',
            ])  // TODO: ChoiceType, 0 / 1
            ->add('prix_public', null, [
                'property_path' => 'prixPublic',
            ])
            ->add('unite_prix', null, [
                'empty_data' => '1',
                'property_path' => 'priceUnit',
            ])  // TODO: ChoiceType, and get choice form Mage
            ->add('short_description')
            ->add('poids_portion', null, [
                'empty_data' => '0.5',
                'property_path' => 'portionWeight',
            ])
            ->add('nbre_portion', null, [
                'empty_data' => '1',
                'property_path' => 'portionNumber',
            ])
            ->add('tax_class_id', null, [
                'empty_data' => '5',
                'property_path' => 'tax',
            ]) // TODO: ChoiceType, and get choice form Mage
            ->add('origine', null, [
                'property_path' => 'origin',
            ])  // TODO: ChoiceType, and get choice form Mage
            ->add('produit_biologique', null, [
                'property_path' => 'bio',
            ])  // TODO: ChoiceType, and get choice form Mage
            ->add('commercant')
            ->add('image_tmp', null, [
                'property_path' => 'photo',
            ])
            ->add('price')
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\ProductHistory',
                'csrf_protection' => false,
                'em'              => null,
            ]
        );
    }
}
