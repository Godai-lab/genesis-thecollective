<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generated extends Model
{
    use HasFactory;

    // Permitir asignación masiva
    protected $fillable = ['key', 'value', 'account_id', 'name', 'rating'];

    // Definir la relación con el modelo Account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeSearch($query,$search){
        if($search){
            return $query->where('name','like','%'.$search.'%');
        }
    }
    
    public function scopeDate($query, $from, $to){
        if (strtotime($from)&&strtotime($to)) {
            return $query->whereBetween('created_at',[$from.' 00:00:00',$to.' 23:59:59']);
        }
    }

    public function scopeFullaccess($query){
        if(!auth()->user()->haveFullAccess())
            return $query->whereIn('account_id',auth()->user()->accounts->pluck('id'));
    }

    public function scopeType($query, $type){
        if($type){
            return $query->where('key',$type);
        }
    }
    
    public function scopeAccount($query, $account){
        if($account){
            return $query->where('account_id',$account);
        }
    }   
}
