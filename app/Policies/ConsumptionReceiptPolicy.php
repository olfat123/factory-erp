<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ConsumptionReceipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsumptionReceiptPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConsumptionReceipt');
    }

    public function view(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('View:ConsumptionReceipt');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConsumptionReceipt');
    }

    public function update(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('Update:ConsumptionReceipt');
    }

    public function delete(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('Delete:ConsumptionReceipt');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ConsumptionReceipt');
    }

    public function restore(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('Restore:ConsumptionReceipt');
    }

    public function forceDelete(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('ForceDelete:ConsumptionReceipt');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConsumptionReceipt');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConsumptionReceipt');
    }

    public function replicate(AuthUser $authUser, ConsumptionReceipt $consumptionReceipt): bool
    {
        return $authUser->can('Replicate:ConsumptionReceipt');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConsumptionReceipt');
    }

}