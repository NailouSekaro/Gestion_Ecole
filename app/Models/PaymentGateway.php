<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $fillable = ['site_id', 'api_key', 'secret_key', 'user_id'];
}
