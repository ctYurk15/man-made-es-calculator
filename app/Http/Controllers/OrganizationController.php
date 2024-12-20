<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizationTypes = OrganizationType::all();
        $organizations = Organization::with('type')->get();

        return view('organizations.index', compact('organizationTypes', 'organizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'nullable|exists:organizations,id',
            'name' => 'nullable|string|max:255',
            'organization_type_id' => 'nullable|exists:organization_types,id|required_if:organization_id,null',
            'year' => 'required|integer|min:1900|max:2100',
        ]);

        $year = $validated['year'];

        if ($validated['organization_id'])
        {
            $organization = Organization::find($validated['organization_id']);
        }
        else
        {
            $existingOrganization = Organization::where('name', $validated['name'])->first();
            if ($existingOrganization) {
                return response()->json([
                    'errors' => [
                        'name' => ['Організація з такою назвою вже існує.'],
                    ],
                ], 422);
            }

            $organization = Organization::create([
                'name' => $validated['name'],
                'organization_type_id' => $validated['organization_type_id'],
            ]);
        }

        return response()->json([
            'success' => true,
            'organization' => $organization,
            'year' => $year
        ], 200);
    }

    public function getScenarios($organizationId)
    {
        $organization = Organization::with('type.emergencyScenarios')->findOrFail($organizationId);

        // Отримуємо сценарії, пов’язані з типом організації
        $scenarios = $organization->type->emergencyScenarios;

        return response()->json($scenarios);
    }
}
