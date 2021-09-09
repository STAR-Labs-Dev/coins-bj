<?php

namespace App\Exports;

use App\Models\DocumentVerification;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class AddressProofsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from     = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to       = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status   = isset(request()->status) ? request()->status : null;

        $addressProofs = (new DocumentVerification())->getAddressVerificationsList($from, $to, $status)->orderBy('id', 'desc');

        return $addressProofs;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Status',
        ];
    }

    public function map($addressProof): array
    {
        return [
            dateFormat($addressProof->created_at),
            isset($addressProof->user) ? $addressProof->user->first_name . ' ' . $addressProof->user->last_name : "-",
            $this->status($addressProof),
        ];
    }

    public function status($addressProof)
    {
        if ($addressProof->status == 'approved') {
            $status = 'Approved';
        } elseif ($addressProof->status == 'pending') {
            $status = 'Pending';
        } elseif ($addressProof->status == 'rejected') {
            $status = 'Rejected';
        }

        return $status;
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
