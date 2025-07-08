<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminApproval extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'subscription_request_id', 'approved'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function subscriptionRequest()
    {
        return $this->belongsTo(SubscriptionRequest::class);
    }
}
