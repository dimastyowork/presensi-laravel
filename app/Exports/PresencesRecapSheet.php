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
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PresencesRecapSheet implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents, WithTitle
{
    protected array $filters;
    private ?Collection $presences = null;
    private ?Collection $summaryRows = null;
    private array $shiftsByName = [];
    private array $overtimeHoursByUser = [];
    private int $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = is_array($filters) ? $filters : [];
    }

    public function title(): string
    {
        return 'Rekap HRD';
    }

    public function collection()
    {
        if ($this->summaryRows !== null) {
            return $this->summaryRows;
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
        $this->presences = $this->applySsoUserFilter($presences, $usersMap);
        $this->attachSsoUser($this->presences, $usersMap);

        $this->prepareShiftMap();
        $this->prepareOvertimeMap($this->presences->pluck('user_id')->unique()->all());
        $this->summaryRows = $this->buildSummaryRows($this->presences);

        return $this->summaryRows;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama',
            'Unit',
            'Total Kehadiran (Hari)',
            'Total Terlambat (Hari)',
            'Total Keterlambatan (Menit)',
            'Total Lembur Disetujui (Jam)',
            'Pending Verifikasi (Hari)',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['nip'],
            $row['name'],
            $row['unit'],
            $row['attendance_days'],
            $row['late_days'],
            $row['late_minutes'],
            number_format($row['overtime_hours'], 2, ',', '.'),
            $row['pending_days'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16,
            'C' => 28,
            'D' => 22,
            'E' => 22,
            'F' => 22,
            'G' => 24,
            'H' => 28,
            'I' => 22,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = Coordinate::stringFromColumnIndex(count($this->headings()));

                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->mergeCells("A3:{$lastColumn}3");

                $sheet->setCellValue('A1', 'LAPORAN REKAP KEHADIRAN HRD');
                $sheet->setCellValue('A2', 'Periode: ' . $this->buildPeriodLabel());
                $sheet->setCellValue('A3', 'Dicetak pada: ' . now()->format('d-m-Y H:i:s'));

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E40AF'],
                    ],
                ]);

                $sheet->getStyle("A2:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DBEAFE'],
                    ],
                ]);

                $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $lastRow = max(4, $sheet->getHighestRow());
                $tableRange = "A4:{$lastColumn}{$lastRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFDBFE'],
                        ],
                    ],
                ]);

                for ($row = 5; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('EFF6FF');
                    }
                }

                $sheet->setAutoFilter("A4:{$lastColumn}4");
                $sheet->freezePane('A5');
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

    private function buildSummaryRows(Collection $presences): Collection
    {
        return $presences
            ->groupBy('user_id')
            ->map(function (Collection $items, $userId) {
                $first = $items->first();
                $lateMinutes = $items->sum(fn(Presence $presence) => $this->calculateLateMinutes($presence));

                return [
                    'user_id' => (int) $userId,
                    'nip' => $first->user->nip ?? '-',
                    'name' => $first->user->name ?? 'N/A',
                    'unit' => $first->user->unit ?? '-',
                    'attendance_days' => $items->count(),
                    'late_days' => $items->where('status', 'Terlambat')->count(),
                    'late_minutes' => $lateMinutes,
                    'overtime_hours' => (float) ($this->overtimeHoursByUser[(int) $userId] ?? 0),
                    'pending_days' => $items->where('is_pending', true)->count(),
                ];
            })
            ->sortBy(function (array $row) {
                return mb_strtolower($row['unit'] . '|' . $row['name']);
            })
            ->values();
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

        if ($shiftEndRaw->lt($shiftStartRaw) && $clockIn->lt($shiftStart)) {
            $clockIn->addDay();
        }

        if ($clockIn->gt($shiftStart)) {
            return (int) $clockIn->diffInMinutes($shiftStart);
        }

        return 0;
    }

    private function prepareOvertimeMap(array $userIds): void
    {
        if (empty($userIds)) {
            $this->overtimeHoursByUser = [];
            return;
        }

        $query = Overtime::query()
            ->where('status', 'approved')
            ->whereIn('user_id', $userIds);

        if ($this->hasFilter('start_date')) {
            $query->whereDate('date', '>=', $this->filters['start_date']);
        }
        if ($this->hasFilter('end_date')) {
            $query->whereDate('date', '<=', $this->filters['end_date']);
        }

        $this->overtimeHoursByUser = $query
            ->get(['user_id', 'date', 'start_time', 'end_time'])
            ->groupBy('user_id')
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

        if ($end->lte($start)) {
            $end->addDay();
        }

        return $end->diffInMinutes($start);
    }

    private function buildPeriodLabel(): string
    {
        if ($this->hasFilter('start_date') && $this->hasFilter('end_date')) {
            return Carbon::parse($this->filters['start_date'])->isoFormat('D MMMM Y') . ' s/d ' .
                Carbon::parse($this->filters['end_date'])->isoFormat('D MMMM Y');
        }

        if ($this->hasFilter('start_date')) {
            return 'Mulai ' . Carbon::parse($this->filters['start_date'])->isoFormat('D MMMM Y');
        }

        if ($this->hasFilter('end_date')) {
            return 'Sampai ' . Carbon::parse($this->filters['end_date'])->isoFormat('D MMMM Y');
        }

        return 'Semua Periode';
    }

    private function hasFilter(string $key): bool
    {
        return array_key_exists($key, $this->filters)
            && $this->filters[$key] !== null
            && $this->filters[$key] !== '';
    }
}
