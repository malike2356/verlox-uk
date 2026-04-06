<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->is_admin) {
            return false;
        }
        if ($user->id === $model->id) {
            return false;
        }
        if ($model->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return false;
        }

        return true;
    }
}
