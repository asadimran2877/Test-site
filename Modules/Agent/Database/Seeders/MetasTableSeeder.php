<?php

namespace Modules\Agent\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meta;

class MetasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $metas = [
            ['url' => 'agent/dashboard', 'title' => 'Agent Dashboard', 'description' => 'Agent Dashboard', 'keywords' => ''],
            ['url' => 'agent/user', 'title' => 'Agent Users', 'description' => 'Agent Users', 'keywords' => ''],
            ['url' => 'agent/user/add', 'title' => 'Agent User Add', 'description' => 'Agent User Add', 'keywords' => ''],
            ['url' => 'agent/deposit', 'title' => 'Agent Deposit', 'description' => 'Agent Deposit', 'keywords' => ''],
            ['url' => 'agent/deposit/success', 'title' => 'Agent Deposit', 'description' => 'Agent Deposit', 'keywords' => ''],
            ['url' => 'agent/payout', 'title' => 'Agent Payout', 'description' => 'Agent Payout', 'keywords' => ''],
            ['url' => 'agent/payout/success', 'title' => 'Agent Payout', 'description' => 'Agent Payout', 'keywords' => ''],
            ['url' => 'agent/transaction', 'title' => 'Agent transaction', 'description' => 'Agent transaction', 'keywords' => ''],
            ['url' => 'agent/wallet', 'title' => 'Agent Wallet', 'description' => 'Agent Wallet', 'keywords' => ''],
            ['url' => 'agent/profile', 'title' => 'Agent Profile', 'description' => 'Agent Profile', 'keywords' => ''],
        ];

        foreach ($metas as $key => $value)
        {
            Meta::create($value);
        }
    }
}
