<?php

namespace App\Http\Controllers;

use App\Models\CalculationArchive;
use App\Models\EmergencyScenario;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\ScenarioCalculation;
use Illuminate\Http\Request;

class RiskAssessmentController extends Controller
{
    public function index()
    {
        $scenarios = EmergencyScenario::all();
        $organizationTypes = OrganizationType::all();
        $organizations = Organization::with('type')->get();

        return view('index', compact('scenarios', 'organizationTypes', 'organizations'));
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
        ],
        [
            'equipmentWear.*.required' => 'Рівень зношеності є обов’язковим.',
            'equipmentWear.*.numeric' => 'Рівень зношеності повинен бути числом.',
            'equipmentWear.*.min' => 'Рівень зношеності не може бути меншим за :min.',
            'equipmentWear.*.max' => 'Рівень зношеності не може перевищувати :max.',
        ]
        );

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

        // Отримуємо організацію та рік з $request
        $organizationId = $request->input('organization_id');
        $year = $request->input('year');

        if (!$organizationId || !$year) {
            return response()->json([
                'success' => false,
                'message' => 'Organization ID and year are required.',
            ], 422);
        }

        // Масив для результатів сценаріїв
        $scenarioResults = [];

        // Загальні оцінки
        $totalNumericAssessment = 0;

        foreach ($validatedData['scenarios'] as $index => $scenarioName) { // Очікуємо назву
            // Знаходимо ID сценарію за назвою
            $scenario = \App\Models\EmergencyScenario::where('name', $scenarioName)->first();

            if (!$scenario) {
                return response()->json([
                    'success' => false,
                    'message' => "Сценарій з назвою '{$scenarioName}' не знайдено.",
                ], 422);
            }

            $scenarioId = $scenario->id; // Отримуємо ID сценарію

            // Розрахунок ймовірності
            $equipmentWear = $validatedData['equipmentWear'][$index] ?? 0;
            $maintenanceFrequency = $validatedData['maintenanceFrequency'][$index] ?? 0;
            $trainingCount = $validatedData['trainingCount'][$index] ?? 0;
            $certifiedEmployees = $validatedData['certifiedEmployees'][$index] ?? 0;

            $baseProbability = 10;
            $baseProbability += $equipmentWear * 0.3;
            $baseProbability -= $maintenanceFrequency * 0.2;
            $baseProbability -= $trainingCount * 0.1;
            $baseProbability -= $certifiedEmployees * 0.2;

            $probability = min(max(round($baseProbability), 0), 100);

            $numericAssessment = (string)$probability;
            $textAssessment = $probability > 80 ? 'Високий ризик' : ($probability > 50 ? 'Середній ризик' : 'Низький ризик');

            $scenarioResults[] = [
                'scenario_id' => $scenarioId, // Тепер використовується ID сценарію
                'numeric_assessment' => $numericAssessment,
                'text_assessment' => $textAssessment,
            ];

            $totalNumericAssessment += $probability;
        }

        // Усереднена оцінка
        $averageNumericAssessment = (string)round($totalNumericAssessment / count($validatedData['scenarios']));
        $averageTextAssessment = $averageNumericAssessment > 80 ? 'Високий рівень ризику' :
            ($averageNumericAssessment > 50 ? 'Середній рівень ризику' : 'Низький рівень ризику');

        // Збереження в calculations_archive
        $calculation = CalculationArchive::create([
            'organization_id' => $organizationId,
            'year' => $year,
            'numeric_assessment' => $averageNumericAssessment,
            'text_assessment' => $averageTextAssessment,
        ]);

        // Збереження результатів сценаріїв
        foreach ($scenarioResults as $result) {
            ScenarioCalculation::create([
                'calculation_id' => $calculation->id,
                'scenario_id' => $result['scenario_id'],
                'numeric_assessment' => $result['numeric_assessment'],
                'text_assessment' => $result['text_assessment'],
            ]);
        }

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
            'scenarios' => $scenarioResults,
        ], 201);
    }

    public function validateOrganizationYear(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'year' => 'required|integer|min:1900|max:2100',
        ]);

        $existingCalculation = CalculationArchive::where('organization_id', $validated['organization_id'])
            ->where('year', $validated['year'])
            ->first();

        if ($existingCalculation) {
            return response()->json([
                'success' => false,
                'message' => 'Для цієї організації вже виконувалася перевірка в зазначеному році.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Організація та рік доступні для перевірки.',
        ]);
    }

}
