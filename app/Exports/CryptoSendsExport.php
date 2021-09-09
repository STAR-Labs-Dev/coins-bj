<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class CryptoSendsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $getCryptoSentTransactions = (new Transaction())->getCryptoSentTransactions($from, $to, $status, $currency, $user)->orderBy('transactions.id', 'desc');

        return $getCryptoSentTransactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Sender',
            'Amount',
            'Fees',
            'Total',
            'Crypto Currency',
            'Receiver',
            'status'
        ];
    }

    public function map($cryptoSentTransaction): array
    {
        return [
            dateFormat($cryptoSentTransaction->created_at),
            !empty($cryptoSentTransaction->user) ? $cryptoSentTransaction->user->first_name . ' ' . $cryptoSentTransaction->user->last_name : "-",
            $cryptoSentTransaction->subtotal,
            $cryptoSentTransaction->charge_fixed,

            // Total
            $this->total($cryptoSentTransaction),
            // $cryptoSentTransaction->total > 0 ? '+' . $cryptoSentTransaction->total : $cryptoSentTransaction->total,
           
            $cryptoSentTransaction->currency->code,
            !empty($cryptoSentTransaction->end_user) ? $cryptoSentTransaction->end_user->first_name . ' ' . $cryptoSentTransaction->end_user->last_name : "-",
            $cryptoSentTransaction->status
        ];
    }

    public function total($transaction)
    {
        if ($transaction->total > 0) {
            $total = '+' . $transaction->total;
        } else {
            $total = $transaction->total -  number_format((json_decode($transaction->cryptoapi_log->payload)->network_fee), 8, '.', '') ;
        }
        return $total;
    }

    public function styles($cryptoSentTransaction)
    {
        $cryptoSentTransaction->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $cryptoSentTransaction->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $cryptoSentTransaction->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $cryptoSentTransaction->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $cryptoSentTransaction->getStyle('1')->getFont()->setBold(true);
    }
}
