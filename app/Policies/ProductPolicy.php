<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'inventory', 'cashier']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'inventory']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['admin', 'inventory']);
    }

    public function adjust(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['admin', 'inventory']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }
}
