<?php

namespace App\Http\Controllers;

use App\Models\EmergencyScenario;
use Illuminate\Http\Request;

class EmergencyScenarioController extends Controller
{
    public function index()
    {
        $scenarios = EmergencyScenario::all();
        return view('admin.emergency-scenarios.index', compact('scenarios'));
    }

    public function create()
    {
        return view('admin.emergency-scenarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:emergency_scenarios,name',
            'description' => 'nullable|string',
        ]);

        EmergencyScenario::create($request->only('name', 'description'));

        return redirect()->route('emergency-scenarios.index')
            ->with('success', 'Сценарій успішно створено.');
    }

    public function edit(EmergencyScenario $emergencyScenario)
    {
        return view('admin.emergency-scenarios.edit', compact('emergencyScenario'));
    }

    public function update(Request $request, EmergencyScenario $emergencyScenario)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:emergency_scenarios,name,' . $emergencyScenario->id,
            'description' => 'nullable|string',
        ]);

        $emergencyScenario->update($request->only('name', 'description'));

        return redirect()->route('emergency-scenarios.index')
            ->with('success', 'Сценарій успішно оновлено.');
    }

    public function destroy(EmergencyScenario $emergencyScenario)
    {
        $emergencyScenario->delete();

        return redirect()->route('emergency-scenarios.index')
            ->with('success', 'Сценарій успішно видалено.');
    }
}
