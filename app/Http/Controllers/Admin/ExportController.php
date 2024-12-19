<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalculationArchive;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function exportCalculations(Request $request)
    {
        if ($request->has('calculation_id'))
        {
            $calculation = CalculationArchive::with('scenarioCalculations.scenario', 'organization')
                ->findOrFail($request->input('calculation_id'));

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Організація:');
            $sheet->setCellValue('B1', $calculation->organization->name ?? 'Невідомо');
            $sheet->setCellValue('A2', 'Рік:');
            $sheet->setCellValue('B2', $calculation->year);
            $sheet->setCellValue('A3', 'Числова оцінка:');
            $sheet->setCellValue('B3', $calculation->numeric_assessment);
            $sheet->setCellValue('A4', 'Текстова оцінка:');
            $sheet->setCellValue('B4', $calculation->text_assessment);

            $sheet->setCellValue('A6', 'ID сценарію');
            $sheet->setCellValue('B6', 'Назва сценарію');
            $sheet->setCellValue('C6', 'Числова оцінка (сценарій)');
            $sheet->setCellValue('D6', 'Текстова оцінка (сценарій)');

            $row = 7;
            foreach ($calculation->scenarioCalculations as $scenarioCalculation) {
                $sheet->setCellValue('A' . $row, $scenarioCalculation->scenario->id ?? 'Невідомо');
                $sheet->setCellValue('B' . $row, $scenarioCalculation->scenario->name ?? 'Невідомо');
                $sheet->setCellValue('C' . $row, $scenarioCalculation->numeric_assessment);
                $sheet->setCellValue('D' . $row, $scenarioCalculation->text_assessment);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'calculation_' . $calculation->id . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit;
        }

        return redirect()->route('filter.calculations')
            ->with('error', 'Не вказано ID обрахунку для експорту.');
    }


    public function filterCalculations(Request $request)
    {
        $query = CalculationArchive::with('organization');

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('organization_id'))
        {
            $query->where('organization_id', $request->input('organization_id'));
        }

        $calculations = $query->get();

        $organizations = \App\Models\Organization::all();

        return view('admin.calculations.filter', compact('calculations', 'organizations'));
    }
}
