<?php

namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;

class UserController extends AbstractController
{
    /** @var string */
    protected $bundleName = 'ApdcApdcBundle';

    /** @var string */
    protected $entityName = 'User';

    /** @var array The ACL for each Action */
    protected $acl = [
        'default' => 'ROLE_SUPER_ADMIN'
    ];

    protected $filterable = ['shop', 'type'];

    /**
     * @inheritdoc
     */
    public function init($type = 'default')
    {
        parent::init($type);

        /**
         * - Create password onCreate
         */
        $this->dispatcher->addListener(
            'User.onUpdateBeforeSave',
            function (GenericEvent $event) {
                /** @var \AppBundle\Entity\User $entity */
                if (!$entity = $event->getArgument('entity')) {
                    return;
                }

                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updatePassword($entity);
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function makeForm($modelName)
    {
        return 'Apdc\ApdcBundle\Form\UserType';
    }
}
