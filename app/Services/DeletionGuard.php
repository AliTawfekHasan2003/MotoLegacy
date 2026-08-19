<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class DeletionGuard
{
    public static function category(Category $category): void
    {
        self::failIfRelated('category', self::collectRelations([
            'cars' => $category->cars()->exists(),
        ]));
    }

    public static function car(Car $car): void
    {
        self::failIfRelated('car', self::collectRelations([
            'purchase_requests' => $car->purchaseRequests()->exists(),
            'rental_requests'   => $car->rentalRequests()->exists(),
            'favorites'         => $car->favoritedBy()->exists(),
        ]));
    }

    public static function user(User $user): void
    {
        self::failIfRelated('user', self::collectRelations([
            'cars'              => $user->cars()->exists(),
            'purchase_requests' => $user->purchaseRequests()->exists(),
            'rental_requests'   => $user->rentalRequests()->exists(),
            'favorites'         => $user->favorites()->exists(),
            'ratings_received'  => $user->ratingsReceived()->exists(),
            'ratings_given'     => $user->ratingsGiven()->exists(),
        ]));
    }

    public static function role(Role $role): void
    {
        $hasAssignedUsers = User::where('role_id', $role->id)->exists()
            || DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', User::class)
                ->exists();

        self::failIfRelated('role', self::collectRelations([
            'users' => $hasAssignedUsers,
        ]));
    }

    public static function deleteUser(User $user): void
    {
        self::user($user);

        $user->tokens()->delete();
        $user->delete();
    }

    private static function collectRelations(array $checks): array
    {
        return array_keys(array_filter($checks));
    }

    private static function failIfRelated(string $entity, array $related): void
    {
        if ($related === []) {
            return;
        }

        $message = 'Cannot delete this ' . $entity . ' because it is linked to: ' . implode(', ', $related) . '.';

        throw ValidationException::withMessages([
            $entity => [$message],
        ]);
    }
}
