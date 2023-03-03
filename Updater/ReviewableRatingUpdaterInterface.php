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

use Owl\Component\Status\Model\ReviewableInterface;
use Owl\Component\Status\Model\ReviewInterface;

interface ReviewableRatingUpdaterInterface
{
    public function update(ReviewableInterface $reviewSubject): void;

    public function updateFromReview(ReviewInterface $review): void;
}
