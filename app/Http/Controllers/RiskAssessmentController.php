<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskAssessmentController extends Controller
{
    public function index()
    {
        $scenarios = [
            ['id' => 1, 'name' => 'Пожежа'],
            ['id' => 2, 'name' => 'Вибух'],
            ['id' => 3, 'name' => 'Розлив хімічних речовин'],
            ['id' => 4, 'name' => 'Збої в роботі електрообладнання'],
        ];
        return view('risk.index', compact('scenarios'));
    }

    public function calculate(Request $request)
    {
        // Логування вхідних даних для діагностики
        \Log::info('Вхідні дані:', $request->all());

        // Валідація вхідних даних
        $validatedData = $request->validate([
            'scenarios' => 'required|array',
            'enterpriseTypes' => 'required|array',
            'equipmentWears' => 'required|array',
            'maintenanceFrequencies' => 'required|array',
            'trainingHours' => 'required|array',
            'certifiedEmployees' => 'required|array',
        ]);

        // Масив для результатів
        $results = [];

        // Обробка кожного сценарію
        foreach ($validatedData['scenarios'] as $index => $scenario) {
            // Збираємо дані для кожного сценарію з відповідних масивів
            $enterpriseType = $validatedData['enterpriseTypes'][$index] ?? 'Невідомо';
            $equipmentWear = (int) ($validatedData['equipmentWears'][$index] ?? 0);
            $maintenanceFrequency = $validatedData['maintenanceFrequencies'][$index] ?? 'Невідомо';
            $trainingHours = (int) ($validatedData['trainingHours'][$index] ?? 0);
            $certifiedEmployees = (int) ($validatedData['certifiedEmployees'][$index] ?? 0);

            // Початкове значення ймовірності (базове значення для прикладу)
            $baseProbability = 10;

            // Розрахунок ймовірності
            $baseProbability += $equipmentWear * 0.3; // Вплив рівня зносу
            $baseProbability -= $trainingHours * 0.1; // Вплив навчання
            $baseProbability += $certifiedEmployees * 0.2; // Вплив атестації

            // Вплив частоти обслуговування
            if ($maintenanceFrequency === 'Регулярно') {
                $baseProbability -= 10;
            } elseif ($maintenanceFrequency === 'Зрідка') {
                $baseProbability += 15;
            } elseif ($maintenanceFrequency === 'Ніколи') {
                $baseProbability += 30;
            }

            // Обмежуємо значення ймовірності в діапазоні 0-100
            $probability = min(max(round($baseProbability), 0), 100);

            // Зберігаємо результат
            $results[] = [
                'scenario' => $scenario,
                'probability' => $probability,
                'details' => [
                    'enterpriseType' => $enterpriseType,
                    'equipmentWear' => $equipmentWear,
                    'maintenanceFrequency' => $maintenanceFrequency,
                    'trainingHours' => $trainingHours,
                    'certifiedEmployees' => $certifiedEmployees,
                ],
            ];
        }

        // Повертаємо результати у форматі JSON
        return response()->json($results);
    }

}
