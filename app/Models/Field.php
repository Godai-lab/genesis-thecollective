<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    // Permitir asignación masiva
    protected $fillable = ['key', 'value', 'account_id'];

    // Definir la relación con el modelo Account
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
