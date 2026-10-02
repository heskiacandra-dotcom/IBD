<?php

namespace App\Models\Traits;

use App\Traits\TenantScope as BaseTenantScope;

/**
 * Re-export TenantScope in App\Models\Traits namespace for convenience.
 */
trait TenantScope
{
    use BaseTenantScope;
}
