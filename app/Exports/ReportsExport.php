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
      if (isset($this->headings[0]) && is_array($this->headings[0])) {
        return $this->headings[0];
      }
      return $this->headings;
    }

    public function array(): array
    {
        if ($this->invoices instanceof \Illuminate\Support\Collection) {
            return $this->invoices->toArray();
        }
        if (isset($this->invoices[0])) {
            if ($this->invoices[0] instanceof \Illuminate\Support\Collection) {
                return $this->invoices[0]->toArray();
            }
            if (is_array($this->invoices[0]) && isset($this->invoices[0][0]) && is_array($this->invoices[0][0])) {
                return $this->invoices[0];
            }
        }
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
