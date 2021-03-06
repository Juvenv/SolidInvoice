<?php

declare(strict_types=1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) 2013-2017 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\ClientBundle\Listener;

use SolidInvoice\ClientBundle\Entity\Client;
use SolidInvoice\ClientBundle\Model\Status;
use SolidInvoice\ClientBundle\Notification\ClientCreateNotification;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ClientListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Client) {
            return;
        }

        if (!$entity->getId() && !$entity->getStatus()) {
            $entity->setStatus(Status::STATUS_ACTIVE);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Client) {
            return;
        }

        // client is created
        $notification = new ClientCreateNotification(['client' => $entity]);

        $this->container
            ->get('notification.manager')
            ->sendNotification('client_create', $notification);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Client) {
            return;
        }

        $entityChangeSet = $event->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);

        // Only update the currencies when the client currency changed
        if (array_key_exists('currency', $entityChangeSet)) {
            $em = $event->getEntityManager();

            $em->getRepository('SolidInvoiceInvoiceBundle:Invoice')->updateCurrency($entity);
            $em->getRepository('SolidInvoiceQuoteBundle:Quote')->updateCurrency($entity);
            $em->getRepository('SolidInvoicePaymentBundle:Payment')->updateCurrency($entity);
            $em->getRepository('SolidInvoiceClientBundle:Credit')->updateCurrency($entity);
        }
    }
}
