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
            ->add('reference_interne_magasin')
            ->add('notes_com')
            ->add('status')  // TODO: ChoiceType, 1: Enabled / 2 Disabled
            ->add('on_selection')  // TODO: ChoiceType, 0 / 1
            ->add('prix_public')
            ->add('weight')
            ->add('unite_prix', null, [
                'empty_data' => '1'
            ])  // TODO: ChoiceType, and get choice form Mage
            ->add('short_description')
            ->add('poids_portion', null, [
                'empty_data' => '0.5'
            ])
            ->add('nbre_portion', null, [
                'empty_data' => '1'
            ])
            ->add('tax_class_id', null, [
                'empty_data' => '5'
            ]) // TODO: ChoiceType, and get choice form Mage
            ->add('origine')  // TODO: ChoiceType, and get choice form Mage
            ->add('produit_biologique')  // TODO: ChoiceType, and get choice form Mage
            ->add('shop_id')  // TODO: ChoiceType, and get choice form Mage
            ->add('commercant')  // TODO: ChoiceType, and get choice form Mage
            ->add('image_tmp')
            ->add('attribute_set_id', null, [
                'data' => '4',
            ])
            ->add('type_id', null, [
                'data' => 'simple'
            ])
            // Generated fields
            ->add('prix_kilo_site')
            ->add('price')
            ->add('meta_title')
            ->add('meta_description')
            ->add('image_label')
            ->add('small_image_label')
            ->add('thumbnail_label')
            ->add('website_ids')
            ->add('category_ids')
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
