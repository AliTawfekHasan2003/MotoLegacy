<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CarPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Car $car): bool
    {
        return $car->status !== 'hidden' || ($user && ($user->id === $car->user_id || $user->hasRole('admin')));
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('seller') || $user->hasRole('user'); // User can add too (requirement said user can عرض سيارة)
    }

    public function update(User $user, Car $car): bool
    {
        return $user->id === $car->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Car $car): bool
    {
        return $user->id === $car->user_id || $user->hasRole('admin');
    }
}
