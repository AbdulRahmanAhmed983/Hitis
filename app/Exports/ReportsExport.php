<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class ReportsExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements WithEvents, FromView,
    WithCustomValueBinder, ShouldAutoSize
{
    protected $headers;
    protected $heading;
    protected $data;

    public function __construct($heading, $headers, $data)
    {
        $this->headers = $headers;
        $this->heading = $heading;
        $this->data = $data;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $event->sheet->getDelegate()->getParent()->getCellXfCollection()[0]->getAlignment()
                    ->setHorizontal('center')->setVertical('center');
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setAutoSize(true);
                $event->getDelegate()->setRightToLeft(true);
            },
        ];
    }

    public function view(): View
    {
        return view('reports.export_excel_table', [
            'data' => $this->data,
            'headers' => $this->headers,
            'heading' => $this->heading,
        ]);
    }
}
