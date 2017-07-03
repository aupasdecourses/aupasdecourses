<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Adminhtml_Neighborhood_IndexController 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Adminhtml_Controller_Action
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Adminhtml_Neighborhood_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * initActions 
     * 
     * @return void
     */
    protected function initActions()
    {
        $this->loadLayout()->_setActiveMenu('neighborhoods/apdc_neighborhood');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Gestion des quartiers'), Mage::helper('adminhtml')->__('Gestion des quartiers'));

        return $this;
    }
    /**
     * indexAction 
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Gestion des quartiers'));
        $this->initActions();

        $this->renderLayout();
    }

    /**
     * newAction 
     * 
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * editAction 
     * 
     * @return void
     */
    public function editAction()
    {
        $this->initNeighborhood();
        $this->initActions();

        $productMessage = Mage::registry('current_neighborhood');
        $this->_title($productMessage->getId() ? $this->__('Edit Neighborhood') : $this->__('New Neighborhood'));
        $this->renderLayout();
    }

    /**
     * deleteAction 
     * 
     * @return void
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('apdc_neighborhood/neighborhood')->load($id);

                $oldImage = $model->getImage();
                if (!empty($oldImage)) {
                    Mage::helper('apdc_neighborhood/media')->deleteMedia($oldImage);
                }

                $model->delete();
                $this->_getSession()->addSuccess($this->__('Le quartier a été supprimé'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('entity_id' => $id));
                return;
            }
        }
        $this->_getSession()->addError($this->__('Impossible de trouver le quartier à supprimer'));
        $this->_redirect('*/*/');
        return;
    }

    /**
     * saveAction 
     * 
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('apdc_neighborhood/neighborhood');

            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }

            // get file upload
            if (isset($data['image']['delete'])) {
                $oldImage = $model->getImage();
                if (!empty($oldImage)) {
                    Mage::helper('apdc_neighborhood/media')->deleteMedia($oldImage);
                }
                $data['image'] = '';
            } elseif ($_FILES['image']['name']) {
                $oldImage = $model->getImageName();
                if (!empty($oldImage)) {
                    Mage::helper('apdc_neighborhood/media')->deleteMedia($oldImage);
                }
                $image = Mage::helper('apdc_neighborhood/media')->uploadMedia('image');
                $data['image'] = $image['file'];
            } else {
            	unset($data['image']);
            }

            $model->setData($data);

            try {
                $model->save();

                $this->_getSession()->addSuccess(Mage::helper('apdc_neighborhood')->__('Le quartier à bien été enregistré'));
                $this->_getSession()->setFormData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('apdc_neighborhood')->__('Une erreur est survenue lors de l\'enregistrement du quartier'));
            }

            $this->_getSession()->setFormData($data);
            if ($id = $this->getRequest()->getParam('id')) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirect('*/*/new');
            }
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * initNeighborhood 
     * 
     * @return this
     */
    protected function initNeighborhood()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getModel('apdc_neighborhood/neighborhood');

        if ($id) {
            $model->load($id);
        }
        Mage::register('current_neighborhood', $model);
        return $this;
    }
}
