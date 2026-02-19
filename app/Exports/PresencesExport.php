<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PresencesExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct($filters = [])
    {
        $this->filters = is_array($filters) ? $filters : [];
    }

    public function sheets(): array
    {
        return [
            new PresencesRecapSheet($this->filters),
            new PresencesDetailSheet($this->filters),
        ];
    }
}
