<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use HasUuids, SoftDeletes, HasFactory, HasTranslations;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'vehicle_count',
        'active',
        'price',
        'duration',
        'description',
        'offer_percent',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%$search%")
                    ->orWhere('title->ar', 'like', "%$search%");
            });
        }

        if (isset($filters['active']) && $filters['active'] !== null) {
            $query->where('active', $filters['active']);
        }

        return $query;
    }
}


