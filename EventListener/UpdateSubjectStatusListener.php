<?php

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Owl\Component\Status\Model\StatusInterface;
use Owl\Component\Status\Model\StatusableInterface;

final class UpdateSubjectStatusListener
{
    /** @var ObjectManager */
    private $statusSubjectManager;

    public function __construct(ObjectManager $statusSubjectManager)
    {
        $this->statusSubjectManager = $statusSubjectManager;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->saveStatus($args);
    }

    public function saveStatus(LifecycleEventArgs $args): void
    {
        /** @var StatusInterface $subject */
        $subject = $args->getObject();

        if (!$subject instanceof StatusInterface) {
            return;
        }

        /** @var StatusableInterface $statusSubject */
        $statusSubject = $subject->getStatusSubject();
        $statusSubject->setStatus($subject->getStatus());

        $this->statusSubjectManager->flush();
    }
}
