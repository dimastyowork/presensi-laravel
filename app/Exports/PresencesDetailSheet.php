<?php

namespace App\Exports;

use App\Models\Overtime;
use App\Models\Presence;
use App\Models\Shift;
use App\Services\SsoApiService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresencesDetailSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected array $filters;
    private ?Collection $rows = null;
    private array $shiftsByName = [];
    private array $overtimeByUserDate = [];
    private int $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = is_array($filters) ? $filters : [];
    }

    public function title(): string
    {
        return 'Detail Harian';
    }

    public function collection()
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        $query = Presence::query();

        if ($this->hasFilter('start_date')) {
            $query->where('date', '>=', $this->filters['start_date']);
        }
        if ($this->hasFilter('end_date')) {
            $query->where('date', '<=', $this->filters['end_date']);
        }
        if ($this->hasFilter('status')) {
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

        if ($this->hasFilter('user_id')) {
            $query->where('user_id', $this->filters['user_id']);
        }

        $presences = $query->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get();

        $usersMap = app(SsoApiService::class)->getUsersMap();
        $presences = $this->applySsoUserFilter($presences, $usersMap);
        $this->attachSsoUser($presences, $usersMap);

        $this->prepareShiftMap();
        $this->prepareOvertimeMap($presences);

        $this->rows = $presences->values();

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIP',
            'Nama',
            'Unit',
            'Shift',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
            'Keterlambatan (Menit)',
            'Lembur Disetujui (Jam)',
            'Keterangan',
        ];
    }

    public function map($presence): array
    {
        $this->rowNumber++;
        $key = $this->buildUserDateKey((int) $presence->user_id, (string) $presence->date);

        return [
            $this->rowNumber,
            Carbon::parse($presence->date)->format('d-m-Y'),
            $presence->user->nip ?? '-',
            $presence->user->name ?? 'N/A',
            $presence->user->unit ?? '-',
            $presence->shift_name ?? '-',
            $presence->time_in ? Carbon::parse($presence->time_in)->format('H:i') : '-',
            $presence->time_out ? Carbon::parse($presence->time_out)->format('H:i') : '-',
            $presence->time_in ? ($presence->status ?? 'Hadir') : 'Tidak Hadir',
            $this->calculateLateMinutes($presence),
            number_format((float) ($this->overtimeByUserDate[$key] ?? 0), 2, ',', '.'),
            $presence->note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1D4ED8'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 18,
            'C' => 16,
            'D' => 28,
            'E' => 22,
            'F' => 18,
            'G' => 12,
            'H' => 12,
            'I' => 14,
            'J' => 22,
            'K' => 22,
            'L' => 30,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'L';
                $lastRow = max(1, $sheet->getHighestRow());

                // Show Excel filter dropdown triangles on Detail Harian header row.
                $sheet->setAutoFilter("A1:{$lastColumn}{$lastRow}");
                $sheet->freezePane('A2');
            },
        ];
    }

    private function applySsoUserFilter(Collection $presences, array $usersMap): Collection
    {
        return $presences->filter(function (Presence $presence) use ($usersMap) {
            $user = $usersMap[(string) $presence->user_id] ?? null;

            if ($this->hasFilter('unit')) {
                if (($user['unit'] ?? null) !== $this->filters['unit']) {
                    return false;
                }
            }

            if ($this->hasFilter('search')) {
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

    private function prepareShiftMap(): void
    {
        $this->shiftsByName = Shift::query()
            ->get(['name', 'start_time', 'end_time'])
            ->keyBy(fn(Shift $shift) => mb_strtolower(trim((string) $shift->name)))
            ->all();
    }

    private function calculateLateMinutes(Presence $presence): int
    {
        if (!$presence->time_in || !$presence->shift_name) {
            return 0;
        }

        $shiftKey = mb_strtolower(trim((string) $presence->shift_name));
        $shift = $this->shiftsByName[$shiftKey] ?? null;

        if (!$shift) {
            return 0;
        }

        // Use explicit date string to avoid Carbon __toString() issues
        $dateStr = Carbon::parse($presence->date)->toDateString();
        $shiftStart = Carbon::parse($dateStr . ' ' . $shift->start_time);
        $clockIn = Carbon::parse($dateStr . ' ' . $presence->time_in);

        // Handle night shifts crossing midnight
        $shiftStartRaw = Carbon::parse($shift->start_time);
        $shiftEndRaw = Carbon::parse($shift->end_time);

        if ($shiftEndRaw->lt($shiftStartRaw) && $clockIn->lt($shiftStart) && $clockIn->hour < 12) {
            $clockIn->addDay();
        }

        if ($clockIn->gt($shiftStart)) {
            return (int) abs($clockIn->diffInMinutes($shiftStart));
        }

        return 0;
    }

    private function prepareOvertimeMap(Collection $presences): void
    {
        $userIds = $presences->pluck('user_id')->unique()->all();
        if (empty($userIds)) {
            $this->overtimeByUserDate = [];
            return;
        }

        $dates = $presences->pluck('date')->unique()->values();
        $minDate = $dates->min();
        $maxDate = $dates->max();

        $query = Overtime::query()
            ->where('status', 'approved')
            ->whereIn('user_id', $userIds);

        if ($minDate) {
            $query->whereDate('date', '>=', $minDate);
        }
        if ($maxDate) {
            $query->whereDate('date', '<=', $maxDate);
        }

        $this->overtimeByUserDate = $query
            ->get(['user_id', 'date', 'start_time', 'end_time'])
            ->groupBy(function (Overtime $overtime) {
                $date = $overtime->date instanceof Carbon
                    ? $overtime->date->toDateString()
                    : (string) $overtime->date;
                return $this->buildUserDateKey((int) $overtime->user_id, $date);
            })
            ->map(function (Collection $items) {
                $minutes = $items->sum(fn(Overtime $overtime) => $this->calculateOvertimeMinutes($overtime));
                return round($minutes / 60, 2);
            })
            ->all();
    }

    private function calculateOvertimeMinutes(Overtime $overtime): int
    {
        $date = $overtime->date instanceof Carbon
            ? $overtime->date->toDateString()
            : (string) $overtime->date;

        $start = Carbon::parse($date . ' ' . $overtime->start_time);
        $end = Carbon::parse($date . ' ' . $overtime->end_time);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return (int) abs($end->diffInMinutes($start));
    }

    private function buildUserDateKey(int $userId, string $date): string
    {
        return $userId . '|' . $date;
    }

    private function hasFilter(string $key): bool
    {
        return array_key_exists($key, $this->filters)
            && $this->filters[$key] !== null
            && $this->filters[$key] !== '';
    }
}
