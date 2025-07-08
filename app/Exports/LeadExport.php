<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LeadExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected $search;
    protected $from;
    protected $to;

    public function __construct($search,$from,$to)
    {
        $this->search = $search;
        $this->from = $from;
        $this->to = $to;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $Requests = Lead::fullaccess()
        ->search($this->search)
        ->date($this->from, $this->to)
        ->orderBy('id', 'desc')
        ->get();

        $formattedData = $Requests->map(function ($request) {
            return [
                'Nombre' => $request->name,
                'Apellido' => $request->last_name,
                'Email' => $request->email,
                'Teléfono' => $request->phone,
                'Fecha' => $request->created_at,
            ];
        });
        return $formattedData;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Email',
            'Teléfono',
            'Fecha'
        ];
    }
    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_NUMBER,
            // 'E' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
