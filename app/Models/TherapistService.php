<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistService extends Model
{
    use HasFactory;
    public function therapist(){
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }
    public function category(){
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
    public function subCategory(){
        return $this->belongsTo(ServiceSubCategory::class, 'service_sub_category_id');
    }
}
