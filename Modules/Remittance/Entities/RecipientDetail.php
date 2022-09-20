<?php

namespace Modules\Remittance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecipientDetail extends Model
{
    protected $table   = 'recipient_details';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_number',
        'email',
        'nick_name',
        'city',
        'street',
        'country',
    ];

    public function remittance()
    {
        return $this->hasOne(Remittance::class);
    }
}
