<?php

namespace app\Repositories;

use app\DTOs\NewsArticleDto;
use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use App\Repositories\Contracts\UserPreferenceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserPreferenceRepository implements UserPreferenceInterface
{
    /**
     * Stores user preferences.
     *
     * @param User $user The user instance.
     * @param array $data The user preference data.
     * @return UserPreference|null The created or updated user preference.
     */
    public function store(User $user, array $data): ?UserPreference
    {
        return $user->preferences()->updateOrCreate([], $data);
    }

    /**
     * Finds user preferences for the given user.
     *
     * @param User $user The user instance.
     * @return UserPreference|null The user preferences or null if not found.
     */
    public function findForUser(User $user): ?UserPreference
    {
        return $user->preferences()->first();
    }
    
}
