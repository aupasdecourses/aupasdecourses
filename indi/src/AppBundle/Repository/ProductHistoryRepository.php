<?php
namespace AppBundle\Repository;

use AppBundle\Entity\ProductHistory;
use AutoBundle\Repository\AbstractRepository;
use Symfony\Component\HttpFoundation\Request;

class ProductHistoryRepository extends AbstractRepository
{
    public function addHistory($data)
    {
        $this->entity = new ProductHistory();

        // TODO - Note: We only need those fields, maybe we should filter prior
        $fields = [
            'sku'                       => true,
            'name'                      => true,
            'reference_interne_magasin' => true,
            'status'                    => true,
            'on_selection'              => true,
            'price'                     => true,
            'unite_prix'                => true,
            'short_description'         => true,
            'poids_portion'             => true,
            'nbre_portion'              => true,
            'tax_class_id'              => true,
            'origine'                   => true,
            'produit_biologique'        => true,
//            'photo'                     => 'Photo : ' . $photo,
        ];

        $newData = array_intersect_key($data, $fields);

        $request = new Request();
        $request->request->add($newData);

        if ($this->isValid($request, false, false)) {
            $this->save();
        }
    }
}
