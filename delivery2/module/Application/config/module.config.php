<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
	'router' => [
		'routes' => [
			'home' 	=> [
				'type'		=> Literal::class,
				'options'	=> [
					'route'		=> '/',
					'defaults'	=> [
						'controller'	=> Controller\IndexController::class,
						'action'		=> 'index',
					],
				],
			],

			'application' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/application[/[:action]]',
					'defaults'	=> [
						'controller'	=> Controller\IndexController::class,
						'action'		=> 'index',
					],
				],
			],

			'user' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/user[/[:action]]',
					'defaults'	=> [
						'controller'	=> Controller\UserController::class,
						'action'		=> 'index',
					],
				],
			],

			'orders_one' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/orders/:id',
					'defaults'	=> [
						'controller'	=> Controller\OrdersController::class,
						'action'		=> 'ordersOne',
					],
					'constraints'	=>	[
						'id'		=>	'[0-9]+'
					],
				],
			],

			'orders_all' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'    => '/orders/:from[/[:to]]',
					'defaults' => [
						'controller' => Controller\OrdersController::class,
						'action'     => 'ordersAll',
					],
					'constraints'	=>	[
						'from'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}',
						'to'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}'
					],
				],
			],

			'orders_index' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/orders[/]',
					'defaults'	=> [
						'controller'	=> Controller\OrdersController::class,
						'action'		=> 'index',
					],
				],
			],

			'merchants_one' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/merchants/:id[/[:from[/[:to]]]]',
					'defaults'	=> [
						'controller'	=> Controller\MerchantsController::class,
						'action'		=> 'merchantsOne',
					],
					'constraints'	=>	[
						'id'	=>	'[0-9]+',
						'from'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}',
						'to'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}'
					],
				],
			],

			'merchants_all' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/merchants/:from[/[:to]]',
					'defaults'	=> [
						'controller'	=> Controller\MerchantsController::class,
						'action'		=> 'merchantsAll',
					],
					'constraints'	=>	[
						'from'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}',
						'to'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}'
					],
				],
			],

			'merchants_index' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/merchants[/]',
					'defaults'	=> [
						'controller'	=> Controller\MerchantsController::class,
						'action'		=> 'index',
					],
				],
			],

			'picking_all' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/picking/:from',
					'defaults'	=> [
						'controller'	=> Controller\PickingController::class,
						'action'		=> 'pickingAll',
					],
					'constraints'	=>	[
						'from'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}',
					],
				],
			],

			'picking_index' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/picking[/]',
					'defaults'	=> [
						'controller'	=> Controller\PickingController::class,
						'action'		=> 'index',
					],
				],
			],

			'shipping_index' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/shipping[/]',
					'defaults'	=> [
						'controller'	=> Controller\ShippingController::class,
						'action'		=> 'index',
					],
				],
			],

			'shipping_all' => [
				'type'		=> Segment::class,
				'options'	=> [
					'route'		=> '/shipping/:from',
					'defaults'	=> [
						'controller'	=> Controller\ShippingController::class,
						'action'		=> 'shippingAll',
					],
					'constraints'	=>	[
						'from'	=>	'[0-9]{4}-[0-9]{2}-[0-9]{2}',
					],
				],
			],

		],
	],
	'controllers' => [
		'factories' => [
			Controller\IndexController::class => InvokableFactory::class,
			Controller\UserController::class => InvokableFactory::class,
			Controller\OrdersController::class => InvokableFactory::class,
			Controller\MerchantsController::class => InvokableFactory::class,
			Controller\PickingController::class => InvokableFactory::class,
			Controller\ShippingController::class => InvokableFactory::class,
		],
		'invokables' => [
			'Application\Controller\Magento' => 'Application\Controller\MagentoController',
		]
	],
	'view_manager' => [
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
		'template_map' => [
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
			'application/home/index'  => __DIR__ . '/../view/application/home/index.phtml',
			'error/404'               => __DIR__ . '/../view/error/404.phtml',
			'error/index'             => __DIR__ . '/../view/error/index.phtml',
		],
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
];
