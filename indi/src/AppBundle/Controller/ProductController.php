<?php

namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;
use AutoBundle\Controller\EmailTrait;
use FOS\RestBundle\Controller\Annotations\FileParam;
use FOS\RestBundle\Controller\Annotations\View as ViewTemplate;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    use EmailTrait;

    protected $entityName = 'Product';

    /** @var array The ACL for each Action */
    protected $acl = [
        'default' => 'ROLE_SUPER_ADMIN',
        'list'    => 'ROLE_USER',
        'get'     => 'ROLE_USER',
        'post'    => 'ROLE_USER',
        'put'     => 'ROLE_USER',
        'patch'   => 'ROLE_USER',
        'delete'  => 'ROLE_USER',
    ];

    /** @var array */
    protected $orderable = ['status', 'name', 'origine', 'produit_biologique'];

    /** @var array */
    protected $filterable = ['commercant'];

    /** @var null|array The name of the changed fields */
    private $original = null;

    /**
     * @inheritdoc
     */
    public function init($type = 'default')
    {
        parent::init($type);

        /**
         * - Force shop for non-admin
         * - Autogenerated SKU
         * - Add generated fields
         */
        $this->dispatcher->addListener(
            'Product.onCreateBeforeSubmit',
            function (GenericEvent $event) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                if (!$request = $event->getArgument('request')) {
                    return;
                }

                if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                    $shop = $this->getDoctrine()->getManager()
                        ->getRepository('AppBundle:Shop')->findOneBy(['productMerchant' => $request->get('commercant')]);
                } else {
                    $shop = $this->getUser()->getShop();
                    $request->request->add(['commercant' => $shop->getProductMerchant()]);
                }

                $sku = $shop->getCode()
                    .'-'.strtoupper(substr($request->get('name'), 0, 3))
                    .'-'.($shop->getIncremental() + 1);

                $unit   = $request->request->get('unite_prix', 'kg');
                $public = $request->request->get('prix_public');

                $public = str_replace(',', '.', $public);

                if ($unit == 'kg') {
                    $price = $public * $request->request->get('poids_portion', 1) * $request->request->get('nbre_portion', 1);
                } else {
                    $price = $public * $request->request->get('nbre_portion', 1);
                }

                $weight = $request->request->get('nbre_portion', 1) * $request->request->get('poids_portion', 1);
                $status = $status = $request->request->get('status') ? 1 : 2;

                $request->request->add([
                    'sku'               => $sku,
                    'status'            => $status,
                    'prix_kilo_site'    => $public . '€/' . $unit,
                    'prix_public'       => $public,
                    'weight'            => $weight,
                    'price'             => $price,
                    'meta_title'        => $request->request->get('name') . ' - Au Pas De Courses',
                    'meta_description'  => $request->request->get('name') . ' - Au Pas De Courses',
                    'image_label'       => $request->request->get('name'),
                    'small_image_label' => $request->request->get('name'),
                    'thumbnail_label'   => $request->request->get('name'),
                    // Default attribute
                    'attribute_set_id'  => '4',
                    'type_id'           => 'simple',
                ]);
            }
        );

        /**
         * - Send email when a product is created
         */
        $this->dispatcher->addListener(
            'Product.onCreateAfterSave',
            function (GenericEvent $event) {
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                /** @var \AppBundle\Repository\ShopRepository $shopModel */
                $shop = $this->getDoctrine()->getManager()
                        ->getRepository('AppBundle:Shop')->findOneBy(['productMerchant' => $entity['commercant']]);
                $shopModel = $this->getModel('Shop', false)->load($shop->getMerchant());
                $shopModel->increment();

                if (!$this->getParameter('enabled_email')) {
                    return;
                }

                $title = 'Appli V2 - Produit créé chez le commerçant avec l\'attribut commercant n° : '.$entity['commercant'];
                $body  = 'Le produit suivant a été créé par '.$this->getUser()->getUsername()
                    .', le '.date("Y-m-d").' :'.PHP_EOL
                    .'SKU : '.$entity['sku'].PHP_EOL
                    .'Nom du produit : '.$entity['name'].PHP_EOL.PHP_EOL
                    .'Référence : '.$entity['reference_interne_magasin'].PHP_EOL
                    .'Disponible : '.($entity['status'] ? 'Oui' : 'Non').PHP_EOL
                    .'Sélection APDC : '.($entity['on_selection']  ? 'Oui' : 'Non').PHP_EOL
                    .'Prix : '.$entity['prix_public'].PHP_EOL
                    .'Unit : '.$entity['unite_prix'].PHP_EOL
                    .'Description : '.$entity['short_description'].PHP_EOL
                    .'Poids portion : '.$entity['poids_portion'].PHP_EOL
                    .'Nombre portion : '.$entity['nbre_portion'].PHP_EOL
                    .'Tax : '.$entity['tax_class_id'].PHP_EOL
                    .'Origin : '.$entity['origine'].PHP_EOL
                    .'Bio : '.($entity['produit_biologique'] ? 'Oui' : 'Non').PHP_EOL
                    .'Notes du commerçant : '.$entity['notes_com'].PHP_EOL
                ;

                $from = $this->getParameter('from_email');
                $to   = $this->getParameter('to_email');

                $message = $this->prepareEmail(
                    $title,
                    $body,
                    $from,
                    $to
                );

                if (!$result = $this->get('mailer')->send($message)) {
                    // TODO: Maybe log error later?
                    // TODO: Maybe we can also add some notification to the user
                }
            }
        );

        /**
         * - Force shop for non-admin
         * - Add generated fields
         */
        $this->dispatcher->addListener(
            'Product.onUpdateBeforeSubmit',
            function (GenericEvent $event) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                if (!($request = $event->getArgument('request')) || !$entity = $event->getArgument('entity')) {
                    return;
                }

                if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                    $user = $this->getUser();

                    $request->request->add(['commercant' => $user->getShop()->getProductMerchant()]);
                }

                // TODO - Note: Check if we can do it directly on the form (prob not, Magento entity)
                // TODO - Note: If cannot check if we can do it on the repository
                // TODO - Note: Else find a way to avoid to do it twice (here and on onCreateBeforeSubmit)

                // Note: If we are from the grid, we get the value from the entity
                $public = $request->request->get('prix_public');
                $unit   = $request->request->get('unite_prix', isset($entity['unite_prix']) ? $entity['unite_prix'] : 'kg');

                // Note: We have no validator yet on the list, we need to check here
                if (!preg_match('/[0-9.,]/', $public)) {
                    $public = $entity['prix_public'];
                } else {
                    $public = str_replace(',', '.', $public);
                }

                if ($unit == 'kg') {
                    $price = $public
                        * $request->request->get('poids_portion', isset($entity['poids_portion']) ? $entity['poids_portion'] : 1)
                        * $request->request->get('nbre_portion', isset($entity['nbre_portion']) ? $entity['nbre_portion'] : 1);
                } else {
                    $price = $public * $request->request->get('nbre_portion', 1);
                }

                $weight = $request->request->get('nbre_portion', 1)
                    * $request->request->get('poids_portion', isset($entity['poids_portion']) ? $entity['poids_portion'] : 1);
                $name   = $request->request->get('name', $entity['name']);

                if (null === $status = $request->request->get('status')) {
                    $status = $entity['status'];
                } else {
                    $status = $status ? 1 : 2;
                }

                $request->request->add([
                    'status'            => $status,
                    'prix_kilo_site'    => $public . '€/' . $unit,
                    'prix_public'       => $public,
                    'weight'            => $weight,
                    'price'             => $price,
                    'meta_title'        => $name . ' - Au Pas De Courses',
                    'meta_description'  => $name . ' - Au Pas De Courses',
                    'image_label'       => $name,
                    'small_image_label' => $name,
                    'thumbnail_label'   => $name,
                ]);
            }
        );

        /**
         * - Send email when a product is updated and create history
         */
        $this->dispatcher->addListener(
            'Product.onUpdateBeforeSave',
            function (GenericEvent $event) {
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $this->original = $entity;
            }
        );
        $this->dispatcher->addListener(
            'Product.onUpdateAfterSave',
            function (GenericEvent $event) {
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $entity  = $entity->getData();
                $changes = array_diff_assoc($entity, $this->original);

                $this->getModel('ProductHistory')->addHistory($entity);

                if (!$this->getParameter('enabled_email')) {
                    return;
                }

                $photo = null;

                if (isset($entity['image_tmp'])) {
                    $photo = $this->get('request_stack')->getMasterRequest()
                        ->getUriForPath('/' . $entity['image_tmp']);
                }

                $bodyChanges = [
                    'name'                      => 'Nom : '.$entity['name'].' (ancienne valeur: '.$this->original['name'].')',
                    'reference_interne_magasin' => 'Référence : ' . $entity['reference_interne_magasin'].' (ancienne valeur: '.$this->original['reference_interne_magasin'].')',
                    'status'                    => 'Disponible : ' . ($entity['status'] ? 'Oui' : 'Non').' (ancienne valeur: '.($this->original['status'] ? 'Oui' : 'Non').')',
                    'on_selection'              => 'Sélection APDC : ' . ($entity['on_selection'] ? 'Oui' : 'Non').' (ancienne valeur: '.($this->original['on_selection'] ? 'Oui' : 'Non').')',
                    'prix_public'                     => 'Prix : ' . $entity['prix_public'].' (ancienne valeur: '.$this->original['prix_public'].')',
                    'unite_prix'                => 'Unit : ' . $entity['unite_prix'].' (ancienne valeur: '.$this->original['unite_prix'].')',
                    'short_description'         => 'Description : ' . $entity['short_description'].' (ancienne valeur: '.$this->original['short_description'].')',
                    'poids_portion'             => 'Poids portion : ' . $entity['poids_portion'].' (ancienne valeur: '.$this->original['poids_portion'].')',
                    'nbre_portion'              => 'Nombre portion : ' . $entity['nbre_portion'].' (ancienne valeur: '.$this->original['nbre_portion'].')',
                    'tax_class_id'              => 'Tax Class Id : ' . $entity['tax_class_id'].' (ancienne valeur: '.$this->original['tax_class_id'].')',
                    'origine'                   => 'Origine : ' . $entity['origine'].' (ancienne valeur: '.$this->original['origine'].')',
                    'produit_biologique'        => 'Bio : ' . ($entity['produit_biologique'] ? 'Oui' : 'Non').' (ancienne valeur: '.($this->original['produit_biologique'] ? 'Oui' : 'Non').')',
                    'image_tmp'                 => 'Photo proposée par le commerçant : <a href="' . $photo.'">'.$photo.'</a>',
                    'notes_com'                 => 'Notes Commerçants : ' . $entity['notes_com'].' (ancienne valeur: '.$this->original['notes_com'].')',
                    'commercant'                => 'Commercant Id : '. $entity['commercant'].' (ancienne valeur: '.$this->original['commercant'].')',
                ];

                $title = 'Appli V2 - Produit '.$entity['sku'].' mis à jour (commerçant ID n°'.$this->original['commercant'].', nom catégorie commerçant: '.$entity['nom_catcommercant'];
                $body  = 'Le produit suivant a été mis à jour sur l\'APPLI V2:'.PHP_EOL
                    .'- par '.$this->getUser()->getUsername().PHP_EOL
                    .'- le '.date("Y-m-d").PHP_EOL
                    .'- SKU : '.$entity['sku'].PHP_EOL
                    .'- Commerçant original: attribut commerçant n°'.$this->original['commercant'].' / Cat Commercant : '.$this->original['nom_catcommercant'].PHP_EOL
                    .'- Nom original du produit : '.$this->original['name'].PHP_EOL
                    .'- Modifications apportées aux produits:'.PHP_EOL.PHP_EOL
                    .implode(PHP_EOL, array_intersect_key($bodyChanges, $changes))
                ;

                $from = $this->getParameter('from_email');
                $to   = $this->getParameter('to_email');

                $message = $this->prepareEmail(
                    $title,
                    $body,
                    $from,
                    $to
                );

                if (!$result = $this->get('mailer')->send($message)) {
                    // TODO: Maybe log error later?
                    // TODO: Maybe we can also add some notification to the user
                }
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getModel($name = null, $setForm = true)
    {
        if (isset($name)) {
            // Note: Well, not good, if the needed model has a service like here, it will not work

            return parent::getModel($name, $setForm);
        }

        if (!isset($this->modelInstances['Product'])) {
            $this->modelInstances['Product'] = $this->get('apdc_apdc.repository.products');

            if ($setForm) {
                $this->modelInstances['Product']->setFormBuilder(
                    $this->get('form.factory')->createBuilder(
                        $this->makeForm('Product'),
                        null,
                        [
                            'attr' => ['id' => 'form-Main']
                        ]
                    )
                );
            }
        }

        return $this->modelInstances['Product'];
    }

    /**
     * @inheritdoc
     */
    protected function getFilterBy(Request $request)
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return parent::getFilterBy($request);
        }

        $filters = parent::getFilterBy($request);
        $filters['commercant'] = $this->getUser()->getShop()->getProductMerchant();

        return $filters;
    }
    // TODO : Security, check user on GET and PUT for non-admin

    /**
     * @inheritdoc
     */
    protected function getFilters()
    {
        $array = [];

        foreach (['origine', 'tax_class_id'] as $code) {
            if ($code == 'tva_class_id') {
                foreach (\Mage::getModel('tax/class')->getCollection() as $tax) {
                    if ($tax['class_name'] != '') {
                        $array['tva_class_id'][$tax['class_id']] = $tax['class_name'];
                    }
                }
            } else {
                $attribute = \Mage::getModel('eav/config')->getAttribute('catalog_product', $code);

                foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
                    $array[$code][$option['value']] = $option['label'];
                }
            }
        }

        return $array + [
            'produit_de_saison'  => [
                448 => 'Oui',
                447 => 'Non'
            ],
            'produit_biologique' => [
                276 => 'Oui',
                76  => 'Non',
                34  => 'AB'
            ],
            'status' => [
                1 => 1,
                2 => 0,
            ]
        ];
    }

    /**
     * @inheritdoc
     *
     * @ViewTemplate()
     * @ApiDoc(output={})
     */
    public function getAction($id, Request $request)
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return parent::getAction($id, $request);
        }

        $entity = parent::getAction($id, $request);

        if ($entity['commercant'] != $this->getUser()->getShop()->getProductMerchant()) {
            return $this->notFound();
        }

        return $entity;
    }

    /**
     * Set the status to false and send an email to the admin
     *
     * @inheritdoc
     *
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function deleteAction($id, Request $request)
    {
        $this->init('delete');

        if (!$entity = $this->getModel()->find($id)) {
            return $this->notFound();
        }

        $title = 'Suppression d\'un produit chez'.$this->getUser()->getUsername();
        $body  = 'La suppression du produit suivant a été demandé par '.$this->getUser()->getUsername()
            .', le '.date("Y-m-d").' :'.PHP_EOL
            .'ID : '.$entity['entity_id'].PHP_EOL
            .'SKU : '.$entity['sku'].PHP_EOL
        ;

        $from = $this->getParameter('from_email');
        $to   = $this->getParameter('to_email');

        $message = $this->prepareEmail(
            $title,
            $body,
            $from,
            $to
        );

        if (!$result = $this->get('mailer')->send($message)) {
            // TODO: Maybe log error later?
            // TODO: Maybe we can also add some notification to the user
        }

        $request->request->replace(['status' => false]);

        return $this->putPatch($id, $request, false);
    }

    /**
     * Patch an existing entity
     *
     * @param integer      $id      The entity id
     * @param Request      $request The Request
     * @param ParamFetcher $paramFetcher
     *
     * @return object|Form|JsonResponse
     * @FileParam(name="photoFile", nullable=true)
     * @ViewTemplate()
     * @ApiDoc()
     */
    public function postUploadAction($id, Request $request, ParamFetcher $paramFetcher)
    {
        $this->init('patch');

        /** @var UploadedFile $image */
        $image = $paramFetcher->get('photoFile');
        $image->move('uploads/products/' . $id, $image->getClientOriginalName());

        $request->request->replace(['image_tmp' => 'uploads/products/' . $id . '/' . $image->getClientOriginalName()]);

        return $this->putPatch($id, $request, false);
    }
}
