<?php

namespace Modules\Remittance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RemittancePayoutMethod extends Model
{

    protected $table = 'remittance_payout_methods';

    protected $fillable = ['payout_type'];
}
