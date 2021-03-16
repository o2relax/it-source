<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GameUser
 *
 * @package App\Models
 * @property string $nickname
 * @property int $points
 * @property ?int $turn
 */
class GameUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function getTurnAttribute($value): ?int
    {
        return $value === null ? null : (int)$value;
    }
}
