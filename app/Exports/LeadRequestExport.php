<?php

namespace App\Exports;

use App\Models\Lead_request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LeadRequestExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
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
        $Requests = Lead_request::fullaccess()
        ->with('lead')
        ->with('city')
        ->with('vehicle_model')
        ->search($this->search)
        ->date($this->from, $this->to)
        ->orderBy('id', 'desc')
        ->get();

        $formattedData = $Requests->map(function ($request) {
            return [
                'Nombre' => $request->lead->name,
                'Apellido' => $request->lead->last_name,
                'Email' => $request->lead->email,
                'Teléfono' => $request->lead->phone,
                'Ciudad' => $request->city->name,
                'Modelo' => $request->vehicle_model->name,
                'Encargado' => $request->in_charge,
                'Origen' => $request->origin,
                'UTM' => $request->utm,
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
            'Ciudad',
            'Modelo',
            'Encargado',
            'Origen',
            'UTM',
            'Fecha'
        ];
    }
    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_NUMBER,
            // 'E' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
