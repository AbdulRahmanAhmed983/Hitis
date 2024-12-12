<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\FromCollection;

class RegisterationExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements WithEvents, FromView,
WithCustomValueBinder, ShouldAutoSize
{
    protected $headers;
    protected $heading;
    protected $courses;
    protected $data;

    public function __construct($heading, $data, $courses, $headers)
    {
        $this->headers = $headers;
        $this->heading = $heading;
        $this->data = $data;
        $this->courses = $courses;
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
        return view('reports.export_excel_table_registeration', [
            'data' => $this->data,
            'courses' => $this->courses,
            'headers' => $this->headers,
            'heading' => $this->heading,
        ]);
    }



}
