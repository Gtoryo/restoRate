<?php

namespace App\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    public function postSubmit(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        // Si la case "Roles" est cochée, ajoutez le rôle "restaurateur"
        if ($form->get('roles')->getData()) {
            $user->setRoles(['ROLE_RESTAURATEUR']);
        }
    }
}
