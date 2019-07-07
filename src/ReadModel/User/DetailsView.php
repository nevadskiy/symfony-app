<?php

declare(strict_types=1);

namespace App\ReadModel\User;

class DetailsView
{
    public $id;
    public $register_date;
    public $email;
    public $first_name;
    public $last_name;
    public $role;
    public $status;
    /**
     * @var SocialNetworkView[]
     */
    public $socialNetworks;
}