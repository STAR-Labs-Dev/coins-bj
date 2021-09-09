<?php

namespace App\Exports;

use App\Models\Withdrawal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class WithdrawalsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $pm       = isset(request()->payment_methods) ? request()->payment_methods : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $withdrawals = (new withdrawal())->getWithdrawalsList($from, $to, $status, $currency, $pm, $user)->orderBy('withdrawals.id', 'desc');

        return $withdrawals;
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
            'Method Info',
            'Status',
        ];
    }

    public function map($withdrawal): array
    {
        return [
            dateFormat($withdrawal->created_at),
            isset($withdrawal->user) ? $withdrawal->user->first_name . ' ' . $withdrawal->user->last_name : "-",
            formatNumber($withdrawal->amount),
            ($withdrawal->charge_percentage == 0) && ($withdrawal->charge_fixed == 0) ? '-' : formatNumber($withdrawal->charge_percentage + $withdrawal->charge_fixed),
            '-'.formatNumber($withdrawal->amount + ($withdrawal->charge_percentage + $withdrawal->charge_fixed)),
            $withdrawal->currency->code,
            ($withdrawal->payment_method->name == "Mts") ? getCompanyName() : $withdrawal->payment_method->name,
            $this->paymentMethodInfo($withdrawal),
            ($withdrawal->status == 'Blocked') ? 'Cancelled' : $withdrawal->status
        ];
    }

    public function paymentMethodInfo($withdrawal)
    {
        if ($withdrawal->payment_method->name != "Bank") {
            $payment_method_info_withdrawal = !empty($withdrawal->payment_method_info) ? $withdrawal->payment_method_info : '-';
        } else {
            $payment_method_info_withdrawal = !empty($withdrawal->withdrawal_detail) ?
            $withdrawal->withdrawal_detail->account_name.' '.'('.('*****'.substr($withdrawal->withdrawal_detail->account_number,-4)).')'.' '.$withdrawal->withdrawal_detail->bank_name : '-';
        }
        return $payment_method_info_withdrawal;
    }

    public function styles($withdrawal)
    {
        $withdrawal->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $withdrawal->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $withdrawal->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $withdrawal->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $withdrawal->getStyle('I')->getAlignment()->setHorizontal('center');
        $withdrawal->getStyle('1')->getFont()->setBold(true);
    }
}
