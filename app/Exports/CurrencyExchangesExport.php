<?php

namespace App\Exports;

use App\Models\CurrencyExchange;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class CurrencyExchangesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from      = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to        = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status    = isset(request()->status) ? request()->status : null;
        $currency  = isset(request()->currency) ? request()->currency : null;
        $user      = isset(request()->user_id) ? request()->user_id : null;

        $exchanges = (new CurrencyExchange())->getExchangesListForCsvExport($from, $to, $status, $currency, $user)->orderBy('currency_exchanges.id', 'desc');

        return $exchanges;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Amount',
            'Fees',
            'Total',
            'Rate',
            'From',
            'To',
            'Status',
        ];
    }

    public function map($currencyExchange): array
    {
        // Amount
        if ($currencyExchange->type == 'Out') {
            if ($currencyExchange->amount > 0) {
                $amount = formatNumber($currencyExchange->amount);
            }
        } elseif ($currencyExchange->type == 'In') {
            if ($currencyExchange->amount > 0) {
                $amount = formatNumber($currencyExchange->amount);
            }
        }

        //Total Amount
        if ($currencyExchange->type == 'Out') {
            if (($currencyExchange->fee + $currencyExchange->amount) > 0) {
                $total = '-'.formatNumber($currencyExchange->fee + $currencyExchange->amount);
            } else {
                $total = '-';
            }
        } elseif ($currencyExchange->type == 'In') {
            if (($currencyExchange->fee + $currencyExchange->amount) > 0) {
                $total = '-'.formatNumber($currencyExchange->fee + $currencyExchange->amount);
            } else {
                $total = '-';
            }
        }

        // From Currency Code
        if ($currencyExchange->type == 'Out') {
            $fromCurrencyCode = $currencyExchange->fromWallet->currency->code;
        } else {
            $fromCurrencyCode = $currencyExchange->fromWallet->currency->code;
        }

        // To Currency Code
        if ($currencyExchange->type == 'In') {
            $toCurrencyCode = $currencyExchange->toWallet->currency->code;
        } else {
            $toCurrencyCode = $currencyExchange->toWallet->currency->code;
        }

        return [
            dateFormat($currencyExchange->created_at),
            $currencyExchange->user->first_name . ' ' . $currencyExchange->user->last_name,
            $amount,
            ($currencyExchange->fee == 0) ? "-" : formatNumber($currencyExchange->fee),
            $total,
            moneyFormat($currencyExchange->toWallet->currency->symbol, (float)($currencyExchange->exchange_rate)),
            $fromCurrencyCode,
            $toCurrencyCode,
            ($currencyExchange->status == 'Blocked') ? 'Cancelled' : $currencyExchange->status
        ];
    }

    public function styles($currencyExchange)
    {
        $currencyExchange->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $currencyExchange->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $currencyExchange->getStyle('E:F')->getAlignment()->setHorizontal('center');
        $currencyExchange->getStyle('G:H')->getAlignment()->setHorizontal('center');
        $currencyExchange->getStyle('I')->getAlignment()->setHorizontal('center');
        $currencyExchange->getStyle('1')->getFont()->setBold(true);
    }
}
