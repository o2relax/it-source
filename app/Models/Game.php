<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Game
 *
 * @package App\Models
 * @property $finished_at
 */
class Game extends Model
{
    public const MAX_PLAYER_COUNT = 3;

    use HasFactory;

    public function findOpened(): Collection
    {
        return self::query()->whereNull('finished_at')
            ->with('players')
            ->has('players', '<', self::MAX_PLAYER_COUNT)
            ->get();
    }

    public function findFinished(): Collection
    {
        return self::query()->whereNotNull('finished_at')->get();
    }

    public function players(): HasMany
    {
        return $this->hasMany(GameUser::class)->limit(self::MAX_PLAYER_COUNT);
    }

    public function findPlayerByName(string $nickname): ?GameUser
    {
        return $this->players()->where('nickname', $nickname)->first();
    }

}
