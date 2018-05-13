<?php

namespace Siqwell\Payment\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Course
 * @package App\Entities
 * @method static Course|Builder actual(Currency $to, Carbon $date = null)
 */
class Course extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'date',
        'value',
        'from',
        'to'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_id');
    }

    /**
     * @return BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_id');
    }

    /**
     * @param Builder     $query
     * @param Currency    $to
     * @param Carbon|null $date
     */
    public function scopeActual(Builder $query, Currency $to, Carbon $date = null)
    {
        if ($date instanceof Carbon) {
            $query->where('date', '<=', $date->toDateString())->where('to', $to->getKey())->orderByDesc('date');
        } else {
            $query->where('to', $to->getKey())->orderByDesc('date');
        }
    }
}
