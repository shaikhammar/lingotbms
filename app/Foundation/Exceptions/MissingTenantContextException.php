<?php

declare(strict_types=1);

namespace App\Foundation\Exceptions;

use Exception;

class MissingTenantContextException extends Exception
{
    public function __construct()
    {
        parent::__construct('Missing tenant context. Ensure that the user is authenticated and has a tenant_id.');
    }
}
