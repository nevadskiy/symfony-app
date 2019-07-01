<?php

declare(strict_types=1);

namespace App\Model;

use Doctrine\ORM\EntityNotFoundException as BaseEntityNotFoundException;

class EntityNotFoundException extends BaseEntityNotFoundException
{
}
