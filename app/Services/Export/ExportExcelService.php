<?php

namespace App\Services\Export;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportExcelService implements FromView, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $data = [];

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->data['view'], ['data' => $this->data]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $minWidth = 5;
                $totalColumns = @$this->data['totalColumns'] ?? 26;
                $maxWidth = 100 / $totalColumns;

                foreach (range('A', chr(ord('A') + $totalColumns - 1)) as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    $columnWidth = $event->sheet->getColumnDimension($column)->getWidth();
                    if ($columnWidth < $minWidth) {
                        $event->sheet->getColumnDimension($column)->setWidth($minWidth);
                    } elseif ($columnWidth > $maxWidth) {
                        $event->sheet->getColumnDimension($column)->setWidth($maxWidth);
                    }
                }
            }
        ];
    }
}
