<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function vehicleRegistration()
    {
        return $this->belongsTo(VehicleRegistration::class);
    }
}
