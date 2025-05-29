<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'name',
        'open',
        'high',
        'low',
        'close'
    ];
    protected $casts = [
        'open' => 'decimal:2',
        'high' => 'decimal:2',
        'low' => 'decimal:2',
        'close' => 'decimal:2',
    ];

    // Accessor to append $ to open
    public function open(): Attribute
    {
        return Attribute::make(
            get: fn($value) => '$' . number_format($value, 2),
        );
    }

    // Accessor to append $ to high
    public function high(): Attribute
    {
        return Attribute::make(
            get: fn($value) => '$' . number_format($value, 2),
        );
    }

    // Accessor to append $ to low
    public function low(): Attribute
    {
        return Attribute::make(
            get: fn($value) => '$' . number_format($value, 2),
        );
    }

    // Accessor to append $ to close
    public function close(): Attribute
    {
        return Attribute::make(
            get: fn($value) => '$' . number_format($value, 2),
        );
    }
    /**
     * Get the user that owns the stock history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
