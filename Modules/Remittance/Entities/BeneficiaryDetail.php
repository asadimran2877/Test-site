<?php

namespace Modules\Remittance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeneficiaryDetail extends Model
{
    public function remittances()
    {
        return $this->hasMany(Remittance::class, 'beneficiary_detail_id');
    }
}
