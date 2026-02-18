<?php

namespace App\Exports;

use App\Models\Presence;
use App\Services\SsoApiService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PresencesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    private $presences;

    public function collection()
    {
        if ($this->presences) {
            return $this->presences;
        }

        $query = Presence::query();
        
        if (isset($this->filters['start_date'])) {
            $query->where('date', '>=', $this->filters['start_date']);
        }
        if (isset($this->filters['end_date'])) {
            $query->where('date', '<=', $this->filters['end_date']);
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

        $presences = $query->orderBy('date', 'desc')
                    ->orderBy('time_in', 'desc')
                    ->get();

        $usersMap = app(SsoApiService::class)->getUsersMap();
        $this->presences = $this->applySsoUserFilter($presences, $usersMap);
        $this->attachSsoUser($this->presences, $usersMap);
        
        return $this->presences;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIP',
            'Nama',
            'Unit',
            'Total Kehadiran User',
            'Total Keterlambatan User',
            'Shift',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Keterangan'
        ];
    }

    public function map($presence): array
    {
        $this->rowNumber = ($this->rowNumber ?? 0) + 1;
        
        $status = 'Tidak Hadir';
        if ($presence->time_in) {
            $status = $presence->status ?? 'Hadir';
        }
        
        // Hitung total kehadiran per user (dari collection yang sudah difilter)
        $userPresences = $this->collection()->where('user_id', $presence->user_id);
        $totalKehadiranUser = $userPresences->count();
        $totalTerlambatUser = $userPresences->filter(function ($p) {
            return $p->status === 'Terlambat';
        })->count();
        
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($presence->date)->isoFormat('D MMMM Y'),
            $presence->user->nip ?? '-',
            $presence->user->name ?? 'N/A',
            $presence->user->unit ?? '-',
            $totalKehadiranUser . ' Hari',
            $totalTerlambatUser . ' Hari',
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
        return [
            1 => [
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
            'A' => 5,      // No
            'B' => 18,     // Tanggal
            'C' => 15,     // NIP
            'D' => 25,     // Nama
            'E' => 15,     // Unit
            'F' => 20,     // Total Kehadiran User
            'G' => 22,     // Total Keterlambatan User
            'H' => 15,     // Shift
            'I' => 12,     // Jam Masuk
            'J' => 12,     // Jam Keluar
            'K' => 15,     // Status
            'L' => 30,     // Keterangan
        ];
    }

    private function applySsoUserFilter(Collection $presences, array $usersMap): Collection
    {
        return $presences->filter(function (Presence $presence) use ($usersMap) {
            $user = $usersMap[(string) $presence->user_id] ?? null;

            if (isset($this->filters['unit']) && $this->filters['unit'] !== '') {
                if (($user['unit'] ?? null) !== $this->filters['unit']) {
                    return false;
                }
            }

            if (isset($this->filters['search']) && $this->filters['search'] !== '') {
                $needle = strtolower((string) $this->filters['search']);
                $name = strtolower((string) ($user['name'] ?? ''));
                $nip = strtolower((string) ($user['nip'] ?? ''));
                if (!str_contains($name, $needle) && !str_contains($nip, $needle)) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function attachSsoUser(Collection $presences, array $usersMap): void
    {
        foreach ($presences as $presence) {
            $user = $usersMap[(string) $presence->user_id] ?? null;
            $presence->setRelation('user', (object) [
                'id' => $presence->user_id,
                'name' => $user['name'] ?? 'N/A',
                'nip' => $user['nip'] ?? '-',
                'unit' => $user['unit'] ?? '-',
            ]);
        }
    }
}
