<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoneyRequest extends Model
{
    protected $fillable = ['user_id','type','amount_mxn_cents','status','reference','admin_note'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
