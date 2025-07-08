<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'name', 'url', 'content', 'status', 'read_from_db'];

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

    public function scopeByAccount($query, $account_id = null)
    {
        if ($account_id) {
            return $query->where('account_id', $account_id);
        }
        
        return $query;
    }
}
