<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\StatusBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Webmozart\Assert\Assert;

final class LoadMetadataSubscriber implements EventSubscriber
{
    /** @var array */
    private $subjects;

    public function __construct(array $subjects)
    {
        $this->subjects = $subjects;
    }

    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments): void
    {
        $metadata = $eventArguments->getClassMetadata();

        $metadataFactory = $eventArguments->getEntityManager()->getMetadataFactory();

        foreach ($this->subjects as $subject => $class) {
            if ($class['status']['classes']['model'] === $metadata->getName()) {
                $statusableEntity = $class['subject'];
                $ownerEntity = $class['owner'];
                $statusableEntityMetadata = $metadataFactory->getMetadataFor($statusableEntity);
                $ownerEntityMetadata = $metadataFactory->getMetadataFor($ownerEntity);

                $metadata->mapManyToOne($this->createSubjectMapping($statusableEntity, $subject, $statusableEntityMetadata));
                $metadata->mapManyToOne($this->createReviewerMapping($ownerEntity, $ownerEntityMetadata));
            }

            if ($class['subject'] === $metadata->getName()) {
                $statusEntity = $class['status']['classes']['model'];

                $metadata->mapOneToMany($this->createReviewsMapping($statusEntity));
            }
        }
    }

    private function createSubjectMapping(
        string $statusableEntity,
        string $subject,
        ClassMetadata $statusableEntityMetadata
    ): array {
        return [
            'fieldName' => 'statusSubject',
            'targetEntity' => $statusableEntity,
            'inversedBy' => 'statuses',
            'joinColumns' => [[
                'name' => $subject . '_id',
                'referencedColumnName' => $statusableEntityMetadata->fieldMappings['id']['columnName'] ?? $statusableEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];
    }

    private function createReviewerMapping(string $ownerEntity, ClassMetadata $ownerEntityMetadata): array
    {
        return [
            'fieldName' => 'owner',
            'targetEntity' => $ownerEntity,
            'joinColumns' => [[
                'name' => 'owner_id',
                'referencedColumnName' => $ownerEntityMetadata->fieldMappings['id']['columnName'] ?? $ownerEntityMetadata->fieldMappings['id']['fieldName'],
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ];
    }

    private function createReviewsMapping(string $statusEntity): array
    {
        return [
            'fieldName' => 'statuses',
            'targetEntity' => $statusEntity,
            'mappedBy' => 'statusSubject',
            'cascade' => ['all'],
        ];
    }
}
