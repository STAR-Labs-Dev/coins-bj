<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;
        $currency = isset(request()->currency) ? request()->currency : null;
        $type     = isset(request()->type) ? request()->type : null;
        $user     = isset(request()->user_id) ? request()->user_id : null;

        $transaction = (new Transaction())->getTransactionsList($from, $to, $status, $currency, $type, $user)->orderBy('transactions.id', 'desc')->take(1100);

        return $transaction;
    }

    public function headings(): array
    {
        return [
            'Date',     
            'User',     
            'Type',     
            'Amount',   
            'Fees',   
            'Total',    
            'Currency', 
            'Receiver', 
            'Status', 
        ];
    }

    public function map($transaction): array
    {
        return [
            dateFormat($transaction->created_at),

            // User
            $this->user($transaction),

            ($transaction->transaction_type->name == "Withdrawal") ? "Payout" : str_replace('_', ' ', $transaction->transaction_type->name),
            $transaction->currency->type != 'fiat' ? $transaction->subtotal : formatNumber($transaction->subtotal),
            (($transaction->charge_percentage == 0) && ($transaction->charge_fixed == 0) ? '-' : ($transaction->currency->type != 'fiat' ? $transaction->charge_fixed : formatNumber($transaction->charge_percentage + $transaction->charge_fixed))),

            // Total
            $this->total($transaction),

            $transaction->currency->code,

            //Receiver
            $this->receiver($transaction),

            (($transaction->status == 'Blocked') ? "Cancelled" : (($transaction->status == 'Refund') ? "Refunded" : $transaction->status)),
        ];
    }

    public function user($transaction)
    {
        if (in_array($transaction->transaction_type_id, [Deposit, Transferred, Exchange_From, Exchange_To, Request_From, Withdrawal, Payment_Sent, Crypto_Sent])) {
            $user = !empty($transaction->user) ? $transaction->user->first_name . ' ' . $transaction->user->last_name : "-";
        } elseif (in_array($transaction->transaction_type_id, [Received, Request_To, Payment_Received, Crypto_Sent, Crypto_Received])) {
            $user = !empty($transaction->end_user) ? $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name : "-";
        }

        return $user;
    }

    public function total($transaction)
    {
        if ($transaction->total > 0)
        {
            if ($transaction->currency->type != 'fiat') {
                $total = '+' . $transaction->total;
            } else {
                $total = '+' . formatNumber($transaction->total);
            }
        }
        else
        {
            if ($transaction->currency->type != 'fiat') {
                if ($transaction->transaction_type->name == 'Crypto_Sent') {
                    $total = number_format(($transaction->total - json_decode($transaction->cryptoapi_log->payload)->network_fee), 8, '.', '');
                } else {
                    $total = $transaction->total;
                }
            } else {
                $total = formatNumber($transaction->total);
            }
        }
        return $total;
    }

    public function receiver($transaction)
    {
        switch ($transaction->transaction_type_id)
        {
            case Deposit:
            case Exchange_From:
            case Exchange_To:
            case Withdrawal:
            case Crypto_Sent:
                $receiver = isset($transaction->end_user) ? $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name : "-";
                break;
            case Transferred:
            case Received:
                if ($transaction->transfer->receiver)
                {
                    $receiver = $transaction->transfer->receiver->first_name . ' ' . $transaction->transfer->receiver->last_name;
                }
                elseif ($transaction->transfer->email)
                {
                    $receiver = $transaction->transfer->email;
                }
                elseif ($transaction->transfer->phone)
                {
                    $receiver = $transaction->transfer->phone;
                }
                else
                {
                    $receiver = '-';
                }
                break;
            case Request_From:
            case Request_To:
                $receiver = isset($transaction->request_payment->receiver) ? $transaction->request_payment->receiver->first_name . ' ' . $transaction->request_payment->receiver->last_name : $transaction->request_payment->email;
                break;
            case Payment_Sent:
                $receiver = isset($transaction->end_user) ? $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name : "-";
                break;
            case Payment_Received:
            case Crypto_Received:
                $receiver = isset($transaction->user) ? $transaction->user->first_name . ' ' . $transaction->user->last_name : "-";
                break;
        }
        return $receiver;
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
