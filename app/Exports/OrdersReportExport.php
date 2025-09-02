<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithColumnFormatting, WithMapping
{
    protected $data;
    protected $reportType;
    protected $title;

    public function __construct($data, $reportType, $title)
    {
        $this->data = $data;
        $this->reportType = $reportType;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data['items'];
    }

    public function headings(): array
    {
        switch ($this->reportType) {
            case 'product':
                return [
                    'Product ID',
                    'Product Name',
                    'Quantity',
                    'Revenue ($)',
                    'Orders Count',
                    'Average Price ($)'
                ];
            case 'location':
                return [
                    'Location',
                    'Orders Count',
                    'Revenue ($)',
                    'Customers Count',
                    'Average Order Value ($)'
                ];
            case 'monthly':
            case 'yearly':
                return [
                    'Period',
                    'Orders Count',
                    'Revenue ($)',
                    'Customers Count',
                    'Average Order Value ($)'
                ];
            default:
                return [];
        }
    }

    public function map($row): array
    {
        switch ($this->reportType) {
            case 'product':
                $avgPrice = $row['quantity'] > 0 ? $row['revenue'] / $row['quantity'] : 0;
                return [
                    $row['id'],
                    $row['name'],
                    $row['quantity'],
                    $row['revenue'],
                    $row['orders_count'],
                    $avgPrice
                ];
            case 'location':
                $avgOrderValue = $row['orders_count'] > 0 ? $row['revenue'] / $row['orders_count'] : 0;
                return [
                    $row['location'],
                    $row['orders_count'],
                    $row['revenue'],
                    $row['customers'],
                    $avgOrderValue
                ];
            case 'monthly':
            case 'yearly':
                $avgOrderValue = $row['orders_count'] > 0 ? $row['revenue'] / $row['orders_count'] : 0;
                return [
                    $row['period'],
                    $row['orders_count'],
                    $row['revenue'],
                    $row['customers_count'],
                    $avgOrderValue
                ];
            default:
                return [];
        }
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:' . $this->getLastColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ]);

        // Add a totals row at the bottom
        $lastRow = count($this->data['items']) + 2; // +2 because of header row and 1-based indexing
        $sheet->setCellValue('A' . $lastRow, 'TOTAL');

        // Set format for the totals row
        $sheet->getStyle('A' . $lastRow . ':' . $this->getLastColumn() . $lastRow)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCE6F1'],
            ],
        ]);

        // Add summary values
        switch ($this->reportType) {
            case 'product':
                $sheet->setCellValue('C' . $lastRow, $this->data['total_quantity']);
                $sheet->setCellValue('D' . $lastRow, $this->data['total_revenue']);
                $sheet->setCellValue('E' . $lastRow, $this->data['total_orders']);
                break;
            case 'location':
            case 'monthly':
            case 'yearly':
                $sheet->setCellValue('B' . $lastRow, $this->data['total_orders']);
                $sheet->setCellValue('C' . $lastRow, $this->data['total_revenue']);
                break;
        }

        // Add report metadata
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:' . $this->getLastColumn() . '1');
        $sheet->setCellValue('A1', $this->title);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Add generation date
        $sheet->setCellValue('A2', 'Generated: ' . date('Y-m-d H:i:s'));
        $sheet->mergeCells('A2:' . $this->getLastColumn() . '2');
        $sheet->getStyle('A2')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ],
            'font' => [
                'italic' => true
            ]
        ]);

        return [
            // Set all cells to be aligned center
            'A3:' . $this->getLastColumn() . ($lastRow + 2) => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        switch ($this->reportType) {
            case 'product':
                return [
                    'C' => NumberFormat::FORMAT_NUMBER,
                    'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'E' => NumberFormat::FORMAT_NUMBER,
                    'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                ];
            case 'location':
                return [
                    'B' => NumberFormat::FORMAT_NUMBER,
                    'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'D' => NumberFormat::FORMAT_NUMBER,
                    'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                ];
            case 'monthly':
            case 'yearly':
                return [
                    'B' => NumberFormat::FORMAT_NUMBER,
                    'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                    'D' => NumberFormat::FORMAT_NUMBER,
                    'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                ];
            default:
                return [];
        }
    }

    private function getLastColumn(): string
    {
        switch ($this->reportType) {
            case 'product':
                return 'F';
            case 'location':
            case 'monthly':
            case 'yearly':
                return 'E';
            default:
                return 'E';
        }
    }
}
