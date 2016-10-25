<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

require dirname(dirname(__DIR__)).'/traits/Forms.phtml';

class OrdersController extends AbstractActionController
{
	use \Forms;

	private function _setGlobalHeader(ViewModel $view, $dataFT = null, $dataID = null, $action = '') {
		$menu = new ViewModel();
		$menu->setTemplate('application/menu/default');

		$formto = $this->getForm_fromTo();
		if (isset($dataFT))
			$formto->setData($dataFT);
		$formto->setAttribute('action', $action);
		$formto->prepare();

		$formFT = new ViewModel([ 'formto' => $formto ]);
		$formFT->setTemplate('application/form/fromto');

		$formid = $this->getForm_input();
		if (isset($dataID) && !isset($dataFT['from']))
			$formid->setData($dataID);
		$formid->setAttribute('action', $action);
		$formid->prepare();

		$formID = new ViewModel([ 'formid' => $formid ]);
		$formID->setTemplate('application/form/id');

		$view->addChild($menu, 'menu');
		$view->addChild($formFT, 'formto');
		$view->addChild($formID, 'formid');
		return ($view);
	}

    public function indexAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($request->getPost('id') <> '') {
				$this->redirect()->toRoute('orders_one', [
					'id'		=>	$request->getPost('id'),
				]);
			} else {
				$this->redirect()->toRoute('orders_all', [
					'from'		=>	$request->getPost('from'),
					'to'		=>	$request->getPost('to')
				]);
			}
		}

		$view = new ViewModel();
		$view->setTemplate('application/orders/index');
		return ($this->_setGlobalHeader($view, ['from' => date('Y-m-d')]));
	}

	public function ordersOneAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$orders = $this->forward()->dispatch('Application\Controller\Magento', [
				'action'		=>	'getOrders',
				'id'			=>	$this->params('id')
			]);

		$view = new ViewModel([ 'orders' => ($orders instanceof \Zend\View\Model\ViewModel) ? array() : $orders ]);
		$view->setTemplate('application/orders/one');

		return ($this->_setGlobalHeader($view, ['from' => $this->params('from'), 'to' => $this->params('to')], ['id' => $this->params('id')], '/indi/orders'));
	}

	public function ordersAllAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$orders = $this->forward()->dispatch('Application\Controller\Magento', [
				'action'		=>	'getOrders',
				'dfrom'			=>	$this->params('from'),
				'dto'			=>	$this->params('to')
			]);

		$view = new ViewModel([ 'orders' => ($orders instanceof \Zend\View\Model\ViewModel) ? array() : $orders ]);
		$view->setTemplate('application/orders/all');
		return ($this->_setGlobalHeader($view, ['from' => $this->params('from'), 'to' => $this->params('to')], ['id' => $this->params('id')], '/indi/orders'));
	}
}
