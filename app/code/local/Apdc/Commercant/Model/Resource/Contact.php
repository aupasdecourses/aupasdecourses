<?php

/**
 * Class Apdc_Commercant_Model_Resource_Contact
 */
class Apdc_Commercant_Model_Resource_Contact extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('apdc_commercant/contact', 'id_contact');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $roleIds = $this->lookupRoleIds($object->getId());
            $object->setData('role_id', $roleIds);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldRoles = $this->lookupRoleIds($object->getId());
        $newRoles = (array)$object->getRoles();
        if (empty($newRoles)) {
            $newRoles = (array)$object->getRoleId();
        }
        $table  = $this->getTable('apdc_commercant/contact_role_assigned');
        $insert = array_diff($newRoles, $oldRoles);
        $delete = array_diff($oldRoles, $newRoles);

        if ($delete) {
            $where = array(
                'contact_id = ?'     => (int) $object->getId(),
                'role_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $roleId) {
                $data[] = array(
                    'contact_id'  => (int) $object->getId(),
                    'role_id' => (int) $roleId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'contact_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('apdc_commercant/contact_role_assigned'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * @param int $contactId
     *
     * @return array
     */
    public function lookupRoleIds($contactId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('apdc_commercant/contact_role_assigned'), 'role_id')
            ->where('contact_id = ?',(int)$contactId);

        return $adapter->fetchCol($select);
    }

}
