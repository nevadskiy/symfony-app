<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Confirm;

use Symfony\Component\Validator\Constraints as Assert;


class Command
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $token;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}
