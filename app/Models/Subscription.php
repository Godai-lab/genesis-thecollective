<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

   // protected $fillable = ['user_id', 'start_date', 'end_date', 'status'];
   protected $fillable = ['user_id', 'plan_id', 'expires_at'];

   protected $casts = [
    'expires_at' => 'datetime',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relación con el plan
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Verifica si la suscripción está activa
    public function isActive()
    {
        return $this->expires_at && $this->expires_at->isFuture();
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
