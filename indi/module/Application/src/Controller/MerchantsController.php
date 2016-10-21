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

class MerchantsController extends AbstractActionController
{
	use \Forms;

	private function _setGlobalHeader(ViewModel $view, $dataFT = null, $cid = -1, $action = '') {
		$commercants =  $this->forward()->dispatch('Application\Controller\Magento', [
			'action' => 'getMerchants'
		]);
		$opts = ['-1' => 'All'];
		foreach ($commercants as $id => $info) {
			$opts[$id] = $info['name'];
		}
		$opts = [
			'label'	=>	'Merchant: ',
			'value_options'	=>	$opts
		];

		$formtoS = $this->getForm_fromTo_select($opts, $cid);
		if (isset($dataFT))
			$formtoS->setData($dataFT);
		$formtoS->setAttribute('action', $action);
		$formtoS->prepare();

		$menu = new ViewModel();
		$menu->setTemplate('application/menu/default');

		$formFTS = new ViewModel([ 'formto' => $formtoS ]);
		$formFTS->setTemplate('application/form/fromto');

		$view->addChild($menu, 'menu');
		$view->addChild($formFTS, 'fromtoS');

		return ($view);
	}

    public function indexAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($request->getPost('commercantId') == -1) {
				$this->redirect()->toRoute('merchants_all', [
					'from'		=> $request->getPost('from'),
					'to'		=> $request->getPost('to')
				]);
			} else {
				$this->redirect()->toRoute('merchants_one', [
					'id'		=> $request->getPost('commercantId'),
					'from'		=> $request->getPost('from'),
					'to'		=> $request->getPost('to')
				]);
			}
		}

		$view = new ViewModel();
		$view->setTemplate('application/merchants/index');

		return ($this->_setGlobalHeader($view, ['from' => date('Y-m-d')]));
	}

    public function merchantsOneAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$orders = $this->forward()->dispatch('Application\Controller\Magento', [
			'action'		=> 'getMerchantsOrders',
			'commercantId'	=>	$this->params('id'),
			'dfrom'			=>	$this->params('from'),
			'dto'			=>	$this->params('to')
		]);

		$view = new ViewModel([ 'orders' => $orders ]);
		$view->setTemplate('application/merchants/one');

		return ($this->_setGlobalHeader($view, ['from' => $this->params('from'), 'to' => $this->params('to')], $this->params('id'), '/indi/merchants'));
	}

    public function merchantsAllAction() {
		if (!$this->forward()->dispatch('Application\Controller\Magento', [ 'action' => 'isLogged']))
			$this->redirect()->toRoute('user', [ 'action' => 'login' ]);

		$orders = $this->forward()->dispatch('Application\Controller\Magento', [
			'action' => 'getMerchantsOrdersbyStore',
			'commercantId'	=>	-1,
			'dfrom'			=>	$this->params('from'),
			'dto'			=>	$this->params('to')
		]);

		if ($orders instanceof \Zend\View\Model\ViewModel)
			$orders = $orders->getVariables();

		$view = new ViewModel([ 'orders' => $orders ]);
		$view->setTemplate('application/merchants/all');

		return ($this->_setGlobalHeader($view, ['from' => $this->params('from'), 'to' => $this->params('to')], -1, '/indi/merchants'));
	}
}
