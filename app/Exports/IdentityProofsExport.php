<?php

namespace App\Exports;

use App\Models\DocumentVerification;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class IdentityProofsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function query()
    {
        $from   = !empty(request()->startfrom) ? setDateForDb(request()->startfrom) : null;
        $to     = !empty(request()->endto) ? setDateForDb(request()->endto) : null;
        $status = isset(request()->status) ? request()->status : null;

        $identityProofs = (new DocumentVerification())->getDocumentVerificationsList($from, $to, $status)->orderBy('id', 'desc');

        return $identityProofs;
    }

    public function headings(): array
    {
        return [
            'Date',
            'User',
            'Identity Type',
            'Identity Number',
            'Status',
        ];
    }

    public function map($identityProof): array
    {
        return [
            dateFormat($identityProof->created_at),
            isset($identityProof->user) ? $identityProof->user->first_name . ' ' . $identityProof->user->last_name : "-",
            str_replace('_', ' ', ucfirst($identityProof->identity_type)),
            $identityProof->identity_number,
            $this->status($identityProof),
        ];
    }

    public function status($identityProof)
    {
        if ($identityProof->status == 'approved') {
            $status = 'Approved';
        } elseif ($identityProof->status == 'pending') {
            $status = 'Pending';
        } elseif ($identityProof->status == 'rejected') {
            $status = 'Rejected';
        }
        return $status;
    }

    public function styles($transfer)
    {
        $transfer->getStyle('A:B')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('C:D')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('E')->getAlignment()->setHorizontal('center');
        $transfer->getStyle('1')->getFont()->setBold(true);
    }
}
