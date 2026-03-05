<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyPackage extends Model
{
    use HasUuids, SoftDeletes;
    protected $table = 'company_packages';

    protected $fillable = [
        'company_id',
        'package_id',
        'subscribed_at',
        'expires_at',
        'num_of_cars',
        'price',
        'payment_status',
        'price_with_tax',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'subscribed_at' => 'date',
    ];
    
    /**
     * Relationship with the Package model
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())->where('subscribed_at', '<=', now());
    }

     public function scopeFilter($query, $filters)
    {


        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        
        if (isset($filters['package_id'])) {
            $query->where('package_id', $filters['package_id']);
        }
        
        
        if (!empty($filters['expires_at'])) {
            $query->whereDate('expires_at', '=', $filters['expires_at']);
        }
        
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                   $q->where('title->ar', 'like', "%{$search}%")
          ->orWhere('title->en', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }


        return $query;
    }

    
}
