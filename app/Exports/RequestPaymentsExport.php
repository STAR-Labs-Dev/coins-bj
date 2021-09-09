<?php

namespace App\Exports;

use App\Models\RequestPayment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class RequestPaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $requestpayments = (new RequestPayment())->getRequestPaymentsList($from, $to, $status, $currency, $user)->orderBy('request_payments.id', 'desc');

        return $requestpayments;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Requested Amount',
            'Accepted Amount',
            'Currency',
            'Receiver',
            'Status',
        ];
    }

    public function map($requestPayment): array
    {
        return [
            dateFormat($requestPayment->created_at),
            isset($requestPayment->user) ? $requestPayment->user->first_name . ' ' . $requestPayment->user->last_name : "-",
            '+'.formatNumber($requestPayment->amount),
            ($requestPayment->accept_amount == 0) ? "-" : '+'.formatNumber($requestPayment->accept_amount),
            $requestPayment->currency->code,
            $this->receiver($requestPayment),
            (($requestPayment->status == 'Blocked') ? "Cancelled" : (($requestPayment->status == 'Refund') ? "Refunded" : $requestPayment->status))
        ];
    }

    public function receiver($requestPayment)
    {
        if ($requestPayment->receiver) {
            $receiver = $requestPayment->receiver->first_name . ' ' . $requestPayment->receiver->last_name;
        } elseif ($requestPayment->email) {
            $receiver = $requestPayment->email;
        } elseif ($requestPayment->phone) {
            $receiver = $requestPayment->phone;
        } else {
            $receiver = '-';
        }
        return $receiver;
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('G')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
