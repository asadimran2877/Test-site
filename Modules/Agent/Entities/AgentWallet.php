<?php

namespace Modules\Agent\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentWallet extends Model
{
    use HasFactory;
    
    protected $table    = 'agent_wallets';
    
    protected $fillable = ['agent_id', 'currency_id', 'available_balance', 'total_paid_balance','is_default'];
    
    protected static function newFactory()
    {
        return \Modules\Agent\Database\factories\AgentWalletFactory::new();
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'currency_id');
    }

    public function activeCurrency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'currency_id')->where('status', 'Active');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function walletBalance()
    {
        $data = $this->leftJoin('currencies', 'currencies.id', '=', 'agent_wallets.currency_id')
            ->select(DB::raw('SUM(agent_wallets.balance) as amount,agent_wallets.currency_id,currencies.type, currencies.code, currencies.symbol'))
            ->groupBy('agent_wallets.currency_id')
            ->get();

        $array_data = [];
        foreach ($data as $row)
        {
            $array_data[$row->code] = $row->type != 'fiat' ? $row->amount : formatNumber($row->amount);
        }
        return $array_data;
    }
}