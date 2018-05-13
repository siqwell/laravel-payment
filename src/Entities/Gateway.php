<?php

namespace Siqwell\Payment\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Siqwell\Payment\Contracts\GatewayContract;
use Watson\Rememberable\Rememberable;

/**
 * Class Gateway
 *
 * @package App\Entities
 * @method static Gateway|Builder active()
 * @method static Gateway|Builder key(string $key)
 * @method static Gateway|Builder remember($minutes)
 * @property Currency $currency
 */
class Gateway extends Model implements GatewayContract
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
        'key',
        'name',
        'driver'
    ];

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query)
    {
        $query->where('is_active', true);
    }

    /**
     * @param Builder $query
     * @param string  $key
     */
    public function scopeKey(Builder $query, string $key)
    {
        $query->where('key', $key);
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->getAttribute('driver');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getAttribute('key');
    }
}
