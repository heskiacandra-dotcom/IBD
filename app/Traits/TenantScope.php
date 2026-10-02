<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait TenantScope
{
    /**
     * Boot the TenantScope trait for a model.
     *
     * Automatically filters queries by auth()->user()->ID_RT,
     * unless the authenticated user has role 'super_admin'.
     */
    protected static bool $isResolvingTenantUser = false;

    protected static function bootTenantScope(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (static::$isResolvingTenantUser) {
                return;
            }

            static::$isResolvingTenantUser = true;
            try {
                $user = auth('web')->user() ?? auth('warga')->user();
            } catch (\Throwable $e) {
                $user = null;
            } finally {
                static::$isResolvingTenantUser = false;
            }

            if ($user) {
                // If user has role 'super_admin' (or 'super-admin'), do not apply tenant filter
                if (isset($user->role) && in_array(strtolower($user->role), ['super_admin', 'super-admin'])) {
                    return;
                }

                // Automatically filter by user->ID_RT
                if (isset($user->ID_RT) && !is_null($user->ID_RT)) {
                    $builder->where($builder->getModel()->getTable() . '.ID_RT', $user->ID_RT);
                }
            }
        });

        // Automatically assign ID_RT when creating a new model if user is authenticated and ID_RT is not set
        static::creating(function (Model $model) {
            $user = auth('web')->user() ?? auth('warga')->user();
            if ($user) {
                if (empty($model->ID_RT) && !empty($user->ID_RT)) {
                    $model->ID_RT = $user->ID_RT;
                }
            }
        });
    }

    /**
     * Scope a query to ignore tenant segregation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTenant($query)
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Scope a query to a specific tenant ID_RT.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $idRt
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTenant($query, $idRt)
    {
        return $query->withoutGlobalScope('tenant')->where($this->getTable() . '.ID_RT', $idRt);
    }
}
