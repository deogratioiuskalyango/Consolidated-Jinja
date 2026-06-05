<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accountant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'owner_user_id', 'accountant_id', 'license_number',
        'designation', 'status', 'force_password_change', 'notes',
    ];

    protected $casts = ['force_password_change' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function owner() { return $this->belongsTo(User::class, 'owner_user_id'); }
    public function collections() { return $this->hasMany(RentCollection::class); }
    public function expenses() { return $this->hasMany(AccountantExpense::class); }
    public function reports() { return $this->hasMany(FinancialReport::class); }

    public function getFullNameAttribute(): string
    {
        return trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? ''));
    }
}
