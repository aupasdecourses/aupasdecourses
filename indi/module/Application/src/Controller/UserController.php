<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;

class UserController extends AbstractActionController
{
	private function getForm() {
		$form = new Form('login');
		$form->setAttribute('method', 'POST');
		$form->add([
			'type'		=>	'text',
			'name'		=>	'username',
			'options'	=>	[
				'label'	=>	'Username: '
			],
			'attributes'	=>	[
				'class'	=>	'form-control'
			]
		]);
		$form->add([
			'type'		=>	'password',
			'name'		=>	'password',
			'options'	=>	[
				'label'	=>	'Password: '
			],
			'attributes'	=>	[
				'class'	=>	'form-control'
			]
		]);
		$form->add([
			'type'		=>	'submit',
			'name'		=>	'submit',
			'attributes'	=>	[
				'value'	=>	'Login',
				'class'	=>	'btn btn-default'
			]
		]);
		return ($form);
	}

    public function indexAction()
    {
		$this->redirect()->toRoute('user', [ 'action' => 'login' ]);
    }

	public function loginAction() {
		$loginform = $this->getForm();
		if ($this->forward()->dispatch('Application\Controller\Magento', [
			'action'	=>	'isLogged'
		])) {
			$this->redirect()->toRoute('home', [ 'action' => 'index' ]);
		} else {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$loginform->setData($request->getPost());
			}
			if ($this->forward()->dispatch('Application\Controller\Magento', [
				'action'	=>	'login',
				'username'	=>	$request->getPost('username'),
				'password'	=>	$request->getPost('password')
			])) {
				$this->redirect()->toRoute('home', [ 'action' => 'index' ]);
			}
		}
		$loginform->prepare();
		return (new ViewModel([ 'form' => $loginform ]));
	}

	public function logoutAction() {
		$this->forward()->dispatch('Application\Controller\Magento', [
			'action'	=>	'logout'
		]);
		$this->redirect()->toRoute('home', [ 'action' => 'index' ]);
	}
}
