<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PibScale extends Model
{
    use HasFactory, SoftDeletes;

    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function patient(){
        return $this->belongsTo(User::class, 'patient_id')->withTrashed();
    }
    public function pibformula(){
        return $this->belongsTo(PibFormula::class, 'pib_formula_id')->withTrashed();
    }
    public function question(){
        return $this->belongsTo(Question::class, 'question_id')->withTrashed();
    }
}
