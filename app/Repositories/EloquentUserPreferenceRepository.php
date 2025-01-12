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
    public function store(User $user, array $data): ?UserPreference
    {
        return $user->preferences()->updateOrCreate([], $data);
    }

    public function findForUser(User $user): ?UserPreference
    {
        return $user->preferences()->first();
    }
    
}
