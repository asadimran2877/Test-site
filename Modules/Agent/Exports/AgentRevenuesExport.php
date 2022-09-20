<?php

namespace Modules\Agent\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class AgentRevenuesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = isset(request()->startfrom) && !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = isset(request()->endto) && !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $type     = isset(request()->type) ? request()->type : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $agent    = isset(request()->user_id) ? request()->user_id : null;

        $revenues = (new Transaction())->getRevenuesList($from, $to, $currency, $type, $agent)->orderBy('transactions.id', 'desc');

        return $revenues;
    }

    public function headings(): array
    {
        return [__('Date'), __('Agent Name'), __('Transaction Type'), __('Agent Percentage'), __('Currency')];
    }

    public function map($revenue): array
    {
        return [
            dateFormat($revenue->created_at),
            isset($revenue->agent->first_name) ? ($revenue->agent->first_name .' '.$revenue->agent->last_name) : '-',
            isset($revenue->transaction_type->name) ? str_replace('_', ' ', $revenue->transaction_type->name) : '-',
            $revenue->agent_percentage == 0 ? '-' : formatNumber($revenue->agent_percentage),
            isset($revenue->currency->code) ? $revenue->currency->code : '-',
        ];
    }

    public function styles($revenue)
    {
        $revenue->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $revenue->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $revenue->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $revenue->getStyle('1')->getFont()->setBold(true);
    }
}
