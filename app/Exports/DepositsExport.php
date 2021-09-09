<?php

namespace App\Exports;

use App\Models\Deposit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class DepositsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $pm       = isset(request()->payment_methods) ? request()->payment_methods : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;
        $deposits = (new Deposit())->getDepositsList($from, $to, $status, $currency, $pm, $user)->orderBy('id', 'desc');

        return $deposits;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Amount',
            'Fees',
            'Total',
            'Currency',
            'Payment Method',
            'Status',
        ];
    }

    public function map($deposit): array
    {
        return [
            dateFormat($deposit->created_at),
            isset($deposit->user) ? $deposit->user->first_name . ' ' . $deposit->user->last_name : "-",
            formatNumber($deposit->amount),
            ($deposit->charge_percentage == 0) && ($deposit->charge_fixed == 0) ? '-' : formatNumber($deposit->charge_percentage + $deposit->charge_fixed),
            "+".formatNumber($deposit->amount + ($deposit->charge_percentage + $deposit->charge_fixed)),
            $deposit->currency->code,
            ($deposit->payment_method->name == 'Mts' ? getCompanyName() : $deposit->payment_method->name),
            ($deposit->status == 'Blocked') ? 'Cancelled' : $deposit->status
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
