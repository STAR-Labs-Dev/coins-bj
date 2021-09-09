<?php

namespace App\Exports;

use App\Models\Transfer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class TransfersExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $transfers = (new Transfer())->getTransfersList($from, $to, $status, $currency, $user)->orderBy('transfers.id', 'desc');

        return $transfers;
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
            'Receiver',
            'Status',
        ];
    }

    public function map($transfer): array
    {
        return [
            dateFormat($transfer->created_at),
            isset($transfer->sender) ? $transfer->sender->first_name . ' ' . $transfer->sender->last_name : "-",
            formatNumber($transfer->amount),
            ($transfer->fee == 0) ? '-' : formatNumber($transfer->fee),
            '-' . formatNumber($transfer->amount + $transfer->fee),
            $transfer->currency->code,
            $this->receiver($transfer),
            $this->statusCheck($transfer)
        ];
    }

    public function receiver($transfer)
    {
        if ($transfer->receiver) {
            $receiver = $transfer->receiver->first_name . ' ' . $transfer->receiver->last_name;
        } elseif ($transfer->email) {
            $receiver = $transfer->email;
        } elseif ($transfer->phone) {
            $receiver = $transfer->phone;
        } else {
            $receiver = '-';
        }

        return $receiver;
    }

    public function statusCheck($transfer) {
        if ($transfer->status == 'Blocked') {
            $status = 'Cancelled';
        } elseif ($transfer->status == 'Refund') {
            $status = 'Refunded';
        } else if ($transfer->status == 'Success') {
            $status = 'Success';
        }
        return $status;
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
