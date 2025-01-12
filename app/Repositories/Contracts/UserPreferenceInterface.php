<?php
namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\UserPreference;

interface UserPreferenceInterface
{
    public function store(User $user, array $data): ?UserPreference;
    public function findForUser(User $user): ?UserPreference;

}