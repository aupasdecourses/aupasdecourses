<?php

/**
 * Class Apdc_Commercant_Model_Resource_Contact_Collection
 */
class Apdc_Commercant_Model_Resource_Contact_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/contact');
    }

    public function toOptionArray()
    {
        $result = [];

        foreach ($this as $item) {
            $data['value'] = $item->getId();
            $data['label'] = $item->getFirstname() . ' ' . $item->getLastname();
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Retreive array of attributes
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray($arrAttributes = array())
    {
        $arr = array();
        foreach ($this->_items as $k => $item) {
            $arr[$k] = $item->toArray($arrAttributes);
        }
        return $arr;
    }

    /**
     * Add filter by role type
     *
     * @param string|Apdc_Commercant_Model_Resource_Contact_Role $role
     *
     * @return Apdc_Commercant_Model_Resource_Contact_Collection
     */
    public function addRoleFilter($role)
    {
        if ($role instanceof Apdc_Commercant_Model_Resource_Contact_Role) {
            $role = array($role->getName());
        }

        if (!is_array($role)) {
            $role = array($role);
        }

        $this->getSelect()->join(
            ['role_table' => $this->getTable('apdc_commercant/contact_role_assigned')],
            'main_table.id_contact = role_table.contact_id',
            []
        )
        ->join(
            ['main_role_table' => $this->getTable('apdc_commercant/contact_role')],
            'role_table.role_id = main_role_table.role_id',
            []
        )
        ->where($this->_getConditionSql('main_role_table.name', ['in' => $role]));

        return $this;
    }
}
