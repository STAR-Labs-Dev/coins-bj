<?php

namespace App\Exports;

use App\Models\MerchantPayment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class MerchantPaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $pm       = isset(request()->payment_methods) ? request()->payment_methods : null;
        $currency = isset(request()->currency) ? request()->currency : null;

        $merchantPayments = (new MerchantPayment())->getMerchantPaymentsList($from, $to, $status, $currency, $pm)->orderBy('merchant_payments.id', 'desc');

        return $merchantPayments;
    }

    public function headings(): array
    {
        return [
            'Date',           
            'Merchant',       
            'User',       
            'Amount',         
            'Fees',         
            'Total',          
            'Currency',       
            'Payment Method', 
            'Status', 
        ];
    }

    public function map($merchantPayment): array
    {
        return [
            dateFormat($merchantPayment->created_at),
            isset($merchantPayment->merchant) ? $merchantPayment->merchant->user->first_name . ' ' . $merchantPayment->merchant->user->last_name : "-",
            isset($merchantPayment->user) ? $merchantPayment->user->first_name . ' ' . $merchantPayment->user->last_name : "-",
            formatNumber($merchantPayment->amount),
            ($merchantPayment->charge_percentage == 0) && ($merchantPayment->charge_fixed == 0) ? '-' : formatNumber($merchantPayment->charge_percentage + $merchantPayment->charge_fixed),
            '+'.formatNumber($merchantPayment->amount + ($merchantPayment->charge_percentage + $merchantPayment->charge_fixed)),
            $merchantPayment->currency->code,

            ($merchantPayment->payment_method->name == "Mts") ? "Pay Money" : $merchantPayment->payment_method->name,

            ($merchantPayment->status == 'Blocked') ? 'Cancelled' : $merchantPayment->status,
        ];
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('I')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
