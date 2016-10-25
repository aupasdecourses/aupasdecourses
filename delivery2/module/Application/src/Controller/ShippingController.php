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

class ShippingController extends AbstractActionController
{
	use \Forms;

	private function _setGlobalHeader(ViewModel $view, $dataF = null) {
		$form = $this->getForm();
		if (isset($dataF))
			$form->setData($dataF);
		$form->setAttribute('action', $action);
		$form->prepare();

		$menu = new ViewModel();
		$menu->setTemplate('application/menu/default');

		$formF = new ViewModel([ 'formto' => $form ]);
		$formF->setTemplate('application/form/fromto');

		$view->addChild($menu, 'menu');
		$view->addChild($formF, 'from');

		return ($view);
	}

    public function indexAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$request = $this->getRequest();
		if ($request->isPost()) {
			$this->redirect()->toRoute('shipping_all', [
				'from'		=> $request->getPost('from'),
			]);
		}

		$view = new ViewModel();
		$view->setTemplate('application/shipping/index');

		return ($this->_setGlobalHeader($view, ['from' => date('Y-m-d')]));
	}

    public function shippingAllAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$orders = $this->forward()->dispatch('Application\Controller\Magento', [
			'action' => 'getOrdersByStore',
			'commercantId'	=>	-1,
			'dfrom'			=>	$this->params('from'),
		]);

		if ($orders instanceof \Zend\View\Model\ViewModel)
			$orders = $orders->getVariables();

		$view = new ViewModel([ 'orders' => $orders ]);
		$view->setTemplate('application/shipping/all');

		return ($this->_setGlobalHeader($view, ['from' => $this->params('from')], '/indi/shipping'));
	}
}
