<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FinanzasExport implements FromView, WithEvents, ShouldAutoSize, WithDrawings
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $totalIngresos;
    protected $totalEgresos;
    protected $balanceNeto;
    protected $movimientosIngresos;
    protected $egresos;
    protected $ingresosCount;
    protected $egresosCount;
    protected $logoPath;

    public function __construct(
        string $fechaInicio,
        string $fechaFin,
        float $totalIngresos,
        float $totalEgresos,
        float $balanceNeto,
        $movimientosIngresos,
        $egresos
    ) {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->totalIngresos = $totalIngresos;
        $this->totalEgresos = $totalEgresos;
        $this->balanceNeto = $balanceNeto;
        $this->movimientosIngresos = $movimientosIngresos;
        $this->egresos = $egresos;
        $this->ingresosCount = is_countable($movimientosIngresos) ? count($movimientosIngresos) : 0;
        $this->egresosCount = is_countable($egresos) ? count($egresos) : 0;
        $this->logoPath = $this->resolveLogoPath();
    }

    public function view(): View
    {
        return view('finanzas.finanzas_excel', [
            'appName' => config('app.name'),
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'totalIngresos' => $this->totalIngresos,
            'totalEgresos' => $this->totalEgresos,
            'balanceNeto' => $this->balanceNeto,
            'movimientosIngresos' => $this->movimientosIngresos,
            'egresos' => $this->egresos,
        ]);
    }

    public function drawings()
    {
        if (!$this->logoPath) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo del negocio');
        $drawing->setPath($this->logoPath);
        $drawing->setHeight(42);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $ingresosFilas = max($this->ingresosCount, 1);
                $egresosFilas = max($this->egresosCount, 1);

                $filaInicioIngresos = 11;
                $filaFinIngresos = $filaInicioIngresos + $ingresosFilas - 1;

                $filaTituloEgresos = $filaFinIngresos + 2;
                $filaHeaderEgresos = $filaTituloEgresos + 1;
                $filaInicioEgresos = $filaHeaderEgresos + 1;
                $filaFinEgresos = $filaInicioEgresos + $egresosFilas - 1;

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                $sheet->mergeCells('A5:F5');
                $sheet->mergeCells("A{$filaTituloEgresos}:E{$filaTituloEgresos}");

                $sheet->getRowDimension(1)->setRowHeight(38);

                $sheet->freezePane('A11');

                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E78'],
                    ],
                ]);

                $sheet->getStyle('A2:F3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->getStyle('A5:F5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2F5597'],
                    ],
                ]);

                $sheet->getStyle('A6:F6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E1F2'],
                    ],
                ]);

                $sheet->getStyle('A7:F7')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $sheet->getStyle('B7:D7')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->getStyle('A9:F9')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '00B050'],
                    ],
                ]);

                $sheet->getStyle('A10:F10')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2F0D9'],
                    ],
                ]);

                $sheet->getStyle("A{$filaTituloEgresos}:E{$filaTituloEgresos}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C00000'],
                    ],
                ]);

                $sheet->getStyle("A{$filaHeaderEgresos}:E{$filaHeaderEgresos}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FCE4D6'],
                    ],
                ]);

                $sheet->getStyle("A10:F{$filaFinIngresos}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFBFBF'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A{$filaHeaderEgresos}:E{$filaFinEgresos}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFBFBF'],
                        ],
                    ],
                ]);

                $sheet->getStyle("F{$filaInicioIngresos}:F{$filaFinIngresos}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->getStyle("E{$filaInicioEgresos}:E{$filaFinEgresos}")
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->getStyle("F{$filaInicioIngresos}:F{$filaFinIngresos}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("E{$filaInicioEgresos}:E{$filaFinEgresos}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("A1:F{$filaFinEgresos}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }

    protected function resolveLogoPath()
    {
        $candidatos = [
            public_path('img/logo.jpg'),
            public_path('img/logo.png'),
            public_path('img/acerca_de.png'),
        ];

        foreach ($candidatos as $ruta) {
            if (file_exists($ruta)) {
                return $ruta;
            }
        }

        return null;
    }
}
