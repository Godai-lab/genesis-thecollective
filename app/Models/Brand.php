<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'country',
        'assistant',
        'status',
        'account_id',
    ];

    public function account(): BelongsTo
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
        if(!auth()->user()->haveFullAccess()){
            return $query->whereHas('account', function ($query) {
                $query->whereIn('id',auth()->user()->accounts->pluck('id'));
            });
        }
    }
}
