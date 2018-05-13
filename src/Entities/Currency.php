<?php

namespace Siqwell\Payment\Entities;

use App\Services\Payment\Contracts\CurrencyContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Watson\Rememberable\Rememberable;

/**
 * Class Currency
 * @package App\Entities
 * @method static Currency|Builder active()
 * @method static Currency|Builder code(string $code)
 * @method static Currency|Builder remember(int $minutes)
 */
class Currency extends Model implements CurrencyContract
{
    use Rememberable;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'code',

        'symbol_left',
        'symbol_right',

        'decimal_place',
        'decimal_point',
        'thousand_point',

        'is_active',
    ];

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    /**
     * @param Builder $query
     * @param string  $code
     */
    public function scopeCode(Builder $query, string $code)
    {
        $query->where('code', Str::upper($code));
    }

    /**
     * @return HasMany
     */
    public function course(): HasMany
    {
        return $this->hasMany(Course::class, 'from');
    }
}
