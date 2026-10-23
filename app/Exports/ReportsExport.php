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

class ReportsExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $headings;
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

    public function styles(Worksheet $sheet): ?array
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri');

        $dimension = $sheet->calculateWorksheetDimension();
        $sheet->getStyle($dimension)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return null;
    }
}
