<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class CryptoReceivesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $getCryptoReceivedTransactions = (new Transaction())->getCryptoReceivedTransactions($from, $to, $currency, $user)->orderBy('transactions.id', 'desc');

        return $getCryptoReceivedTransactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Sender',
            'Amount',
            'Crypto Currency',
            'Receiver',
        ];
    }

    public function map($getCryptoReceivedTransactions): array
    {
        return [
            dateFormat($getCryptoReceivedTransactions->created_at),
            !empty($getCryptoReceivedTransactions->end_user) ? $getCryptoReceivedTransactions->end_user->first_name . ' ' . $getCryptoReceivedTransactions->end_user->last_name : "-",
            '+' . $getCryptoReceivedTransactions->subtotal,
            $getCryptoReceivedTransactions->currency->code,
           !empty($getCryptoReceivedTransactions->user) ? $getCryptoReceivedTransactions->user->first_name . ' ' . $getCryptoReceivedTransactions->user->last_name : "-",
        ];
    }

    public function styles($getCryptoReceivedTransactions)
    {
        $getCryptoReceivedTransactions->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $getCryptoReceivedTransactions->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $getCryptoReceivedTransactions->getStyle('E')->getAlignment()->setHorizontal('center');
        $getCryptoReceivedTransactions->getStyle('1')->getFont()->setBold(true);
    }
}
