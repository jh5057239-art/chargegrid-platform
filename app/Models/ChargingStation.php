<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargingStation extends Model
{
    protected $fillable = ['name','model','charging_type','power_kw','price_mxn_cents','status','description'];
}
