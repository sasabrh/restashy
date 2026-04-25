<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $guarded = ['id'];

    public function chat() {
        return $this->belongsTo(Chat::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
