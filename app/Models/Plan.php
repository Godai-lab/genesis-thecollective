<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'duration_days','status'];
     // Relación con las suscripciones
     public function subscriptions()
     {
         return $this->hasMany(Subscription::class);
     }
     public function scopeSearch($query,$search){
        if($search){
            return $query->whereHas('user',function($query) use($search){
                $query->where('name','like','%'.$search.'%')->orWhere('username','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%');
            });
        }
    }
    
    public function scopeDate($query, $from, $to){
        if (strtotime($from)&&strtotime($to)) {
            return $query->whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59']);
        }
        
    }

    public function scopeFullaccess($query){
        if(!auth()->user()->haveFullAccess())
            return $query->whereIn('id',auth()->user()->accounts->pluck('id'));
    }
}
