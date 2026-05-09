<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WorkerSalary;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerSalaryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkerSalary');
    }

    public function view(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('View:WorkerSalary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkerSalary');
    }

    public function update(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('Update:WorkerSalary');
    }

    public function delete(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('Delete:WorkerSalary');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WorkerSalary');
    }

    public function restore(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('Restore:WorkerSalary');
    }

    public function forceDelete(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('ForceDelete:WorkerSalary');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkerSalary');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkerSalary');
    }

    public function replicate(AuthUser $authUser, WorkerSalary $workerSalary): bool
    {
        return $authUser->can('Replicate:WorkerSalary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkerSalary');
    }

}