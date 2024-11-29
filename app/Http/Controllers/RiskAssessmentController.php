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

    public function validateSlide(Request $request)
    {
        // Нормалізація даних
        $normalizedData = [];
        foreach ($request->all() as $key => $value) {
            if (str_ends_with($key, '[]')) {
                $normalizedKey = rtrim($key, '[]'); // Видаляємо квадратні дужки
                $normalizedData[$normalizedKey] = $value;
            } else {
                $normalizedData[$key] = $value;
            }
        }

        $request->replace($normalizedData); // Замінюємо дані в запиті

        // Валідація
        $validatedData = $request->validate([
            'equipmentWear' => 'array',
            'equipmentWear.*' => 'numeric|min:0|max:100',
            'maintenanceFrequency' => 'array',
            'maintenanceFrequency.*' => 'numeric|min:0',
            'equipmentType' => 'array',
            'equipmentType.*' => 'string',
            'lastCheck' => 'array',
            'lastCheck.*' => 'date',
            'trainingCount' => 'array',
            'trainingCount.*' => 'numeric|min:0',
            'certifiedEmployees' => 'array',
            'certifiedEmployees.*' => 'numeric|min:0|max:100',
            'knowledgeScore' => 'array',
            'knowledgeScore.*' => 'numeric|min:0|max:100',
            'trainingCategories' => 'array',
            'trainingCategories.*' => 'string',
            'weatherConditions' => 'array',
            'weatherConditions.*' => 'string',
            'geographicalFeatures' => 'array',
            'geographicalFeatures.*' => 'string',
            'naturalThreats' => 'array',
            'naturalThreats.*' => 'string',
            'normative.limits' => 'array',
            'normative.limits.*' => 'string',
            'normative.standards' => 'array',
            'normative.standards.*' => 'string',
            'normative.controls' => 'array',
            'normative.controls.*' => 'string',
        ]);

        return response()->json(['message' => 'Validation successful.', 'data' => $validatedData]);
    }


    public function calculate(Request $request)
    {
        // Логування вхідних даних для діагностики
        \Log::info('Вхідні дані:', $request->all());

        // Оновлена валідація
        $validatedData = $request->validate([
            'scenarios' => 'required|array|min:1',
            'scenarios.*' => 'required|string',

            'equipmentWear' => 'required|array|min:1',
            'equipmentWear.*' => 'required|numeric|min:0|max:100',
            'maintenanceFrequency' => 'required|array|min:1',
            'maintenanceFrequency.*' => 'required|numeric|min:0',
            'equipmentType' => 'required|array|min:1',
            'equipmentType.*' => 'required|string',
            'lastCheck' => 'required|array|min:1',
            'lastCheck.*' => 'required|date',

            'trainingCount' => 'required|array|min:1',
            'trainingCount.*' => 'required|numeric|min:0',
            'certifiedEmployees' => 'required|array|min:1',
            'certifiedEmployees.*' => 'required|numeric|min:0|max:100',
            'knowledgeScore' => 'required|array|min:1',
            'knowledgeScore.*' => 'required|numeric|min:0|max:100',
            'trainingCategories' => 'required|array|min:1',
            'trainingCategories.*' => 'required|string',

            'weatherConditions' => 'required|array|min:1',
            'weatherConditions.*' => 'required|string',
            'geographicalFeatures' => 'required|array|min:1',
            'geographicalFeatures.*' => 'required|string',
            'naturalThreats' => 'required|array|min:1',
            'naturalThreats.*' => 'required|string',

            'normative.limits' => 'required|array|min:1',
            'normative.limits.*' => 'required|string',
            'normative.standards' => 'required|array|min:1',
            'normative.standards.*' => 'required|string',
            'normative.controls' => 'required|array|min:1',
            'normative.controls.*' => 'required|string',
        ]);

        // Масив для результатів
        $results = [];

        foreach ($validatedData['scenarios'] as $index => $scenario) {
            // Дані про технічний стан обладнання
            $equipmentWear = (int) ($validatedData['equipment']['wear'][$index] ?? 0);
            $maintenanceFrequency = (int) ($validatedData['equipment']['maintenance'][$index] ?? 0);
            $equipmentType = $validatedData['equipment']['type'][$index] ?? 'Невідомо';
            $lastCheckDate = $validatedData['equipment']['last_check'][$index] ?? 'Невідомо';

            // Дані про навчання персоналу
            $trainingCount = (int) ($validatedData['training']['count'][$index] ?? 0);
            $certificationRate = (int) ($validatedData['training']['certified'][$index] ?? 0);
            $knowledgeScore = (int) ($validatedData['training']['knowledge'][$index] ?? 0);
            $trainingCategories = $validatedData['training']['categories'][$index] ?? 'Невідомо';

            // Зовнішні фактори
            $weatherConditions = $validatedData['external']['weather'][$index] ?? 'Сприятливі';
            $geographicalFeatures = $validatedData['external']['geo'][$index] ?? 'Невідомо';
            $naturalThreats = $validatedData['external']['threats'][$index] ?? 'Відсутні';

            // Нормативні параметри
            $limitValues = $validatedData['normative']['limits'][$index] ?? 'Не вказано';
            $standards = $validatedData['normative']['standards'][$index] ?? 'Не вказано';
            $controlValues = $validatedData['normative']['controls'][$index] ?? 'Не вказано';

            // Початкове значення ймовірності (базове значення для прикладу)
            $baseProbability = 10;

            // Розрахунок ймовірності
            $baseProbability += $equipmentWear * 0.3; // Вплив рівня зносу
            $baseProbability -= $maintenanceFrequency * 0.2; // Вплив частоти обслуговування
            $baseProbability -= $trainingCount * 0.1; // Вплив навчань
            $baseProbability -= $certificationRate * 0.2; // Вплив сертифікації
            $baseProbability += $knowledgeScore * 0.1; // Вплив знань

            // Вплив зовнішніх факторів
            if ($weatherConditions === 'Несприятливі') {
                $baseProbability += 15;
            }
            if ($naturalThreats !== 'Відсутні') {
                $baseProbability += 10;
            }

            // Обмежуємо значення ймовірності в діапазоні 0-100
            $probability = min(max(round($baseProbability), 0), 100);

            // Зберігаємо результат
            $results[] = [
                'scenario' => $scenario,
                'probability' => $probability,
                'details' => [
                    'equipment' => [
                        'wear' => $equipmentWear,
                        'maintenance' => $maintenanceFrequency,
                        'type' => $equipmentType,
                        'last_check' => $lastCheckDate,
                    ],
                    'training' => [
                        'count' => $trainingCount,
                        'certified' => $certificationRate,
                        'knowledge' => $knowledgeScore,
                        'categories' => $trainingCategories,
                    ],
                    'external' => [
                        'weather' => $weatherConditions,
                        'geo' => $geographicalFeatures,
                        'threats' => $naturalThreats,
                    ],
                    'normative' => [
                        'limits' => $limitValues,
                        'standards' => $standards,
                        'controls' => $controlValues,
                    ],
                ],
            ];
        }

        // Повертаємо результати у форматі JSON
        return response()->json($results);
    }

}
