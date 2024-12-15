<?php

namespace App\Http\Controllers;

use App\Helpers\FuzzyLogic;
use App\Models\CalculationArchive;
use App\Models\EmergencyScenario;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\ScenarioCalculation;
use DateTime;
use Illuminate\Http\Request;

class RiskAssessmentController extends Controller
{
    private function calculateEmergencyProbability($equipmentWear, $maintenanceFrequency, $lastInspectionDate, $trainingsCount, $certificationRate, $knowledgeScore) {
        // Перевірка часу з останньої перевірки (в місяцях)
        $currentDate = new DateTime();
        $lastInspection = new DateTime($lastInspectionDate);
        $monthsSinceLastInspection = $currentDate->diff($lastInspection)->m + ($currentDate->diff($lastInspection)->y * 12);

        // Вагові коефіцієнти
        $equipmentWeight = 0.5; // Вплив стану обладнання
        $personnelWeight = 0.5; // Вплив навчання персоналу

        // Компонент стану обладнання
        $equipmentScore = (
            ($equipmentWear / 100) * 0.6 + // Зношеність
            max(0, (1 - $maintenanceFrequency / 12)) * 0.3 + // Рідкість обслуговування
            min(1, $monthsSinceLastInspection / 12) * 0.1 // Час з останньої перевірки
        );

        // Компонент навчання персоналу
        $personnelScore = (
            max(0, (1 - $trainingsCount / 10)) * 0.4 + // Недостатня кількість навчань
            max(0, (1 - $certificationRate / 100)) * 0.4 + // Низький рівень атестації
            max(0, (1 - $knowledgeScore / 100)) * 0.2 // Недостатній рівень знань
        );

        // Загальний ризик
        $probability = ($equipmentScore * $equipmentWeight) + ($personnelScore * $personnelWeight);

        // Результат у відсотках
        return round(min(100, $probability * 100));
    }

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
            'lastCheck' => 'array',
            'lastCheck.*' => 'date',
            'trainingCount' => 'array',
            'trainingCount.*' => 'numeric|min:0',
            'certifiedEmployees' => 'array',
            'certifiedEmployees.*' => 'numeric|min:0|max:100',
            'knowledgeScore' => 'array',
            'knowledgeScore.*' => 'numeric|min:0|max:100',
        ],
        [
            'equipmentWear.*.required' => 'Рівень зношеності є обов’язковим.',
            'equipmentWear.*.numeric' => 'Рівень зношеності повинен бути числом.',
            'equipmentWear.*.min' => 'Рівень зношеності не може бути меншим за :min.',
            'equipmentWear.*.max' => 'Рівень зношеності не може перевищувати :max.',

            'maintenanceFrequency.*.required' => 'Частота обслуговування є обов’язковою.',
            'maintenanceFrequency.*.numeric' => 'Частота обслуговування повинна бути числом.',
            'maintenanceFrequency.*.min' => 'Частота обслуговування не може бути меншою за :min.',

            'lastCheck.*.required' => 'Дата останньої перевірки є обов’язковою.',
            'lastCheck.*.date' => 'Дата останньої перевірки повинна бути датою.',

            'trainingCount.*.required' => 'Кількість навчань є обов’язковою.',
            'trainingCount.*.numeric' => 'Кількість навчань повинна бути числом.',
            'trainingCount.*.min' => 'Кількість навчань не може бути меншою за :min.',

            'certifiedEmployees.*.required' => 'Відсоток атестації є обов’язковим.',
            'certifiedEmployees.*.numeric' => 'Відсоток атестації повинен бути числом.',
            'certifiedEmployees.*.min' => 'Відсоток атестації не може бути меншим за :min.',
            'certifiedEmployees.*.max' => 'Відсоток атестації не може перевищувати :max.',

            'knowledgeScore.*.required' => 'Оцінка знань є обов’язковою.',
            'knowledgeScore.*.numeric' => 'Оцінка знань повинна бути числом.',
            'knowledgeScore.*.min' => 'Оцінка знань не може бути меншою за :min.',
            'knowledgeScore.*.max' => 'Оцінка знань не може перевищувати :max.',
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
            'lastCheck' => 'required|array|min:1',
            'lastCheck.*' => 'required|date',

            'trainingCount' => 'required|array|min:1',
            'trainingCount.*' => 'required|numeric|min:0',
            'certifiedEmployees' => 'required|array|min:1',
            'certifiedEmployees.*' => 'required|numeric|min:0|max:100',
            'knowledgeScore' => 'required|array|min:1',
            'knowledgeScore.*' => 'required|numeric|min:0|max:100',
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
            $knowledgeScore = $validatedData['knowledgeScore'][$index] ?? 0;
            $lastCheck = $validatedData['lastCheck'][$index];

            $probability = $this->calculateEmergencyProbability(
                $equipmentWear,
                $maintenanceFrequency,
                $lastCheck,
                $trainingCount,
                $certifiedEmployees,
                $knowledgeScore
            );

            $es_probability_string = FuzzyLogic::parseValue($probability, 'es_probability').' ризику';

            $currentDate = new DateTime();
            $lastInspection = new DateTime($lastCheck);
            $monthsSinceLastInspection = $currentDate->diff($lastInspection)->m + ($currentDate->diff($lastInspection)->y * 12);

            $last_check_str_raw = FuzzyLogic::parseValue(min(12, (int) $monthsSinceLastInspection), 'last_check', true);
            $last_check_str = '';
            switch ($last_check_str_raw)
            {
                case 'low': $last_check_str = 'Найближчим часом перевіка не потрібна'; break;
                case 'moderate': $last_check_str = 'Скоро потрібна перевірка'; break;
                case 'high': $last_check_str = 'Негайно потрібна перевірка'; break;
            }

            $scenarioResults[] = [
                'scenario_id' => $scenarioId, // Тепер використовується ID сценарію
                'name' => $scenarioName, // Тепер використовується ID сценарію
                'numeric_assessment' => (string) $probability,
                'text_assessment' => $es_probability_string,
                'single_dimensions' => [
                    'equipment_wear' => FuzzyLogic::parseValue($equipmentWear, 'equipment_wear'),
                    'maintenance_frequency' => FuzzyLogic::parseValue(min(12, $maintenanceFrequency), 'maintenance_frequency'),
                    'last_check' => $last_check_str,
                    'training_count' => FuzzyLogic::parseValue(min(10, $trainingCount), 'training_count'),
                    'certified_employees' => FuzzyLogic::parseValue($certifiedEmployees, 'certified_employees'),
                    'knowledge_score' => FuzzyLogic::parseValue($knowledgeScore, 'knowledge_score'),
                ]
            ];

            $totalNumericAssessment += $probability;
        }

        // Усереднена оцінка
        $averageNumericAssessment = round($totalNumericAssessment / count($validatedData['scenarios']));
        $averageTextAssessment = FuzzyLogic::parseValue((int) $averageNumericAssessment, 'es_probability').' ризику';

        // Збереження в calculations_archive
        $calculation = CalculationArchive::create([
            'organization_id' => $organizationId,
            'year' => $year,
            'numeric_assessment' => (string) $averageNumericAssessment,
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
