<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * Semua user terautentikasi boleh melihat daftar kamar.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Semua user terautentikasi boleh melihat detail kamar.
     */
    public function view(User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Hanya admin yang boleh membuat kamar baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Hanya admin yang boleh mengupdate kamar.
     */
    public function update(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Hanya admin yang boleh menghapus kamar.
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Hanya admin yang boleh melakukan restore (soft delete).
     */
    public function restore(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Hanya admin yang boleh hapus permanen.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return $user->role === 'admin';
    }
}
