<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Request;

class PresencesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Presence::with('user');
        
        // Apply filters
        if (isset($this->filters['start_date'])) {
            $query->where('date', '>=', $this->filters['start_date']);
        }
        if (isset($this->filters['end_date'])) {
            $query->where('date', '<=', $this->filters['end_date']);
        }
        if (isset($this->filters['unit'])) {
            $query->whereHas('user', function($q) {
                $q->where('unit', $this->filters['unit']);
            });
        }
        if (isset($this->filters['status'])) {
            switch ($this->filters['status']) {
                case 'hadir':
                    $query->whereNotNull('time_in')->where('status', 'Hadir')->where('is_pending', false);
                    break;
                case 'terlambat':
                    $query->where('status', 'Terlambat')->where('is_pending', false);
                    break;
                case 'pending':
                    $query->where('is_pending', true);
                    break;
            }
        }

        if (isset($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('date', 'desc')
                    ->orderBy('time_in', 'desc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            ['Total Kehadiran: ' . $this->collection()->count() . ' Hari'],
            [
                'No',
                'Tanggal',
                'NIP',
                'Nama',
                'Unit',
                'Shift',
                'Jam Masuk',
                'Jam Keluar',
                'Status',
                'Keterangan'
            ]
        ];
    }

    public function map($presence): array
    {
        $this->rowNumber = ($this->rowNumber ?? 0) + 1;
        
        $status = 'Tidak Hadir';
        if ($presence->time_in) {
            $status = $presence->status ?? 'Hadir';
        }
        
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($presence->date)->isoFormat('D MMMM Y'),
            $presence->user->nip ?? '-',
            $presence->user->name ?? 'N/A',
            $presence->user->unit ?? '-',
            $presence->shift_name ?? '-',
            $presence->time_in ? \Carbon\Carbon::parse($presence->time_in)->format('H:i') : '-',
            $presence->time_out ? \Carbon\Carbon::parse($presence->time_out)->format('H:i') : '-',
            $status,
            $presence->note ?? '-'
        ];
    }

    private $rowNumber = 0;

    public function styles(Worksheet $sheet)
    {
        // Merge Total Row
        $sheet->mergeCells('A1:J1');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 15,
            'D' => 25,
            'E' => 15,
            'F' => 15,
            'G' => 12,
            'H' => 12,
            'I' => 15,
            'J' => 30,
        ];
    }
}
