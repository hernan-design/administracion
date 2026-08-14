<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportsDownloadExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
  use Exportable;
    protected $invoices;

    public function __construct(array $headings, array $invoices)
    {
      $this->headings = $headings;
      $this->invoices = $invoices;
    }

    public function headings(): array
    {
      return $this->headings;
    }

    public function array(): array
    {
        return $this->invoices;
    }

    public function styles(Worksheet $sheet)
{

    $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ]);

    // Configuración de la fuente (font)
    $sheet->getStyle($sheet->calculateWorksheetDimension())->getFont()->setName('Calibri');

    // Configuración de alineación vertical
    $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
}
}
