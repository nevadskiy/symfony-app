<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use DateInterval;

class ResetPasswordTokenizerFactory
{
    public function create(string $interval): ResetPasswordTokenizer
    {
        return new ResetPasswordTokenizer(new DateInterval($interval));
    }
}
