<?php

namespace App\Http\Controllers;

use App\Models\OrganizationType;
use Illuminate\Http\Request;

class OrganizationTypeController extends Controller
{
    public function index()
    {
        $organizationTypes = OrganizationType::all();
        return view('admin.organization-types.index', compact('organizationTypes'));
    }

    public function create()
    {
        $scenarios = \App\Models\EmergencyScenario::all();
        return view('admin.organization-types.create', compact('scenarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name',
            'emergency_scenarios' => 'nullable|array',
            'emergency_scenarios.*' => 'exists:emergency_scenarios,id',
        ]);

        $organizationType = OrganizationType::create($request->only('name'));

        if ($request->has('emergency_scenarios')) {
            $organizationType->emergencyScenarios()->sync($request->emergency_scenarios);
        }

        return redirect()->route('organization-types.index')
            ->with('success', 'Тип організації успішно створено.');
    }

    public function edit(OrganizationType $organizationType)
    {
        $scenarios = \App\Models\EmergencyScenario::all();
        return view('admin.organization-types.edit', compact('organizationType', 'scenarios'));
    }

    public function update(Request $request, OrganizationType $organizationType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name,' . $organizationType->id,
            'emergency_scenarios' => 'nullable|array',
            'emergency_scenarios.*' => 'exists:emergency_scenarios,id',
        ]);

        $organizationType->update($request->only('name'));

        if ($request->has('emergency_scenarios'))
        {
            $organizationType->emergencyScenarios()->sync($request->emergency_scenarios);
        }
        else
        {
            $organizationType->emergencyScenarios()->detach();
        }

        return redirect()->route('organization-types.index')
            ->with('success', 'Тип організації успішно оновлено.');
    }

    public function destroy(OrganizationType $organizationType)
    {
        $organizationType->delete();

        return redirect()->route('organization-types.index')
            ->with('success', 'Тип організації успішно видалено.');
    }
}
