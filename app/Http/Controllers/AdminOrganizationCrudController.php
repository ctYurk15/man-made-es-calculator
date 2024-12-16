<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationType;
use Illuminate\Http\Request;

class AdminOrganizationCrudController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('type')->get();
        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        $organizationTypes = OrganizationType::all();
        return view('admin.organizations.create', compact('organizationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name',
            'organization_type_id' => 'required|exists:organization_types,id',
        ]);

        Organization::create($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Організація успішно створена.');
    }

    public function edit(Organization $organization)
    {
        $organizationTypes = OrganizationType::all();
        return view('admin.organizations.edit', compact('organization', 'organizationTypes'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $organization->id,
            'organization_type_id' => 'required|exists:organization_types,id',
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.index')
            ->with('success', 'Організація успішно оновлена.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Організація успішно видалена.');
    }
}
