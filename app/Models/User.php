<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the department that the user belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the material stock-ins recorded by this user
     */
    public function materialStockIns(): HasMany
    {
        return $this->hasMany(MaterialStockIn::class, 'received_by');
    }

    /**
     * Get the material stock-outs issued by this user
     */
    public function materialStockOuts(): HasMany
    {
        return $this->hasMany(MaterialStockOut::class, 'issued_by');
    }

    /**
     * Get the finished goods produced by this user
     */
    public function finishedGoods(): HasMany
    {
        return $this->hasMany(FinishedGood::class, 'produced_by');
    }

    /**
     * Get the scrap/waste records by this user
     */
    public function scrapWaste(): HasMany
    {
        return $this->hasMany(ScrapWaste::class, 'recorded_by');
    }

    /**
     * Get the downtime records by this user
     */
    public function downtimeRecords(): HasMany
    {
        return $this->hasMany(DowntimeRecord::class, 'recorded_by');
    }

    /**
     * Get the production orders requested by this user
     */
    public function requestedProductionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'requested_by');
    }

    /**
     * Get the production orders approved by this user
     */
    public function approvedProductionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'approved_by');
    }

    /**
     * Get the production orders managed by this user as plant manager
     */
    public function managedProductionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'plant_manager_id');
    }

    /**
     * Get the deliveries made by this user
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'delivered_by');
    }

    /**
     * Get the payments recorded by this user
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'recorded_by');
    }
}