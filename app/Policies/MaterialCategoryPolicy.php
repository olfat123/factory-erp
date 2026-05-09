<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MaterialCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MaterialCategory');
    }

    public function view(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('View:MaterialCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MaterialCategory');
    }

    public function update(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('Update:MaterialCategory');
    }

    public function delete(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('Delete:MaterialCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MaterialCategory');
    }

    public function restore(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('Restore:MaterialCategory');
    }

    public function forceDelete(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('ForceDelete:MaterialCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MaterialCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MaterialCategory');
    }

    public function replicate(AuthUser $authUser, MaterialCategory $materialCategory): bool
    {
        return $authUser->can('Replicate:MaterialCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MaterialCategory');
    }

}