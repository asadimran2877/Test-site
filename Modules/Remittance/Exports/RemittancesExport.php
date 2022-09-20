<?php

namespace Modules\Remittance\Exports;

use Modules\Remittance\Entities\Remittance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RemittancesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $pm       = isset(request()->payment_methods) ? request()->payment_methods : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $remittances = (new Remittance())->getRemittancesList($from, $to, $status, $currency, $pm, $user)->orderBy('id', 'desc');

        return $remittances;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Send Amount',
            'Fees',
            'Total',
            'Exchange Rate',
            'Received Amount',
            'Currency',
            'Payment Method',
            'Status',
        ];
    }

    public function map($remittance): array
    {
        return [
            dateFormat($remittance->created_at),
            isset($remittance->sender) ? $remittance->sender->first_name . ' ' . $remittance->sender->last_name : "-",
            formatNumber($remittance->transferred_amount) ?? '',
            formatNumber($remittance->fees) ?? '',
            formatNumber($remittance->total),
            formatNumber($remittance->exchange_rate) ?? '',
            formatNumber($remittance->received_amount) ?? '',
            $remittance->currency->code ?? '',
            ($remittance->payment_method->name == 'Mts' ? getCompanyName() : $remittance->payment_method->name),
            ($remittance->status == 'Blocked') ? 'Cancelled' : $remittance->status
        ];
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
