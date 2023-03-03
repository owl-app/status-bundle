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

namespace Owl\Bundle\StatusBundle\Updater;

use Doctrine\Persistence\ObjectManager;
use Owl\Component\Status\Calculator\ReviewableRatingCalculatorInterface;
use Owl\Component\Status\Model\ReviewableInterface;
use Owl\Component\Status\Model\ReviewInterface;

class AverageRatingUpdater implements ReviewableRatingUpdaterInterface
{
    /** @var ReviewableRatingCalculatorInterface */
    private $averageRatingCalculator;

    /** @var ObjectManager */
    private $reviewSubjectManager;

    public function __construct(
        ReviewableRatingCalculatorInterface $averageRatingCalculator,
        ObjectManager $reviewSubjectManager
    ) {
        $this->averageRatingCalculator = $averageRatingCalculator;
        $this->reviewSubjectManager = $reviewSubjectManager;
    }

    public function update(ReviewableInterface $reviewSubject): void
    {
        $this->modifyReviewSubjectAverageRating($reviewSubject);
    }

    public function updateFromReview(ReviewInterface $review): void
    {
        $this->modifyReviewSubjectAverageRating($review->getReviewSubject());
    }

    private function modifyReviewSubjectAverageRating(ReviewableInterface $reviewSubject): void
    {
        $averageRating = $this->averageRatingCalculator->calculate($reviewSubject);

        $reviewSubject->setAverageRating($averageRating);

        if (!$this->reviewSubjectManager->getUnitOfWork()->isScheduledForDelete($reviewSubject)) {
            $this->reviewSubjectManager->flush();
        }
    }
}
