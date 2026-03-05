<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Category;
use App\Models\Asset;
use App\Models\Manufacturer;

class AssetModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'models';

    protected $fillable = [
        'name',
        'category_id',
        'manufacturer_id',
        'require_serial',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'model_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}