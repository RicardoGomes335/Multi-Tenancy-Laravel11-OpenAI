<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Tratar o filtro na query para exibir apenas as empresas relacionadas á um único usuário
        if (session()->has('company_id')) {
            $builder->where('company_id', session()->get('company_id'));
        }

    }
}
