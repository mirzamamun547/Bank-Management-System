<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display a list of branches with manager information.
     */
    public function index()
    {
        $branches = DB::table('branches')
            ->leftJoin('USERS', 'branches.manager_employee_id', '=', 'USERS.customer_id')
            ->select('branches.*', 'USERS.first_name', 'USERS.last_name')
            ->orderBy('branches.branch_id', 'asc')
            ->get();

        // Potential managers (all active employees)
        $employees = DB::table('USERS')
            ->where('ROLE', 'EMPLOYEE')
            ->where('STATUS', 'ACTIVE')
            ->get();

        return view('admin.dashboard', compact('branches', 'employees'))->with('section', 'branches');
    }

    /**
     * Store a new branch using the Oracle PL/SQL package procedure.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:100|unique:branches,branch_name',
            'location' => 'required|string|max:255',
            'manager_employee_id' => 'nullable|string'
        ]);

        try {
            DB::statement("
                BEGIN
                    BANK_ADMIN_PKG.ADD_BRANCH(
                        :branch_name,
                        :location,
                        :manager_employee_id
                    );
                END;
            ", [
                'branch_name' => $request->branch_name,
                'location' => $request->location,
                'manager_employee_id' => $request->manager_employee_id
            ]);

            return redirect()->back()->with('success', 'Branch created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing branch using the Oracle PL/SQL package procedure.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_name' => 'required|string|max:100|unique:branches,branch_name,' . $id . ',branch_id',
            'location' => 'required|string|max:255',
            'manager_employee_id' => 'nullable|string',
            'status' => 'required|string|in:ACTIVE,INACTIVE'
        ]);

        try {
            DB::statement("
                BEGIN
                    BANK_ADMIN_PKG.UPDATE_BRANCH(
                        :branch_id,
                        :branch_name,
                        :location,
                        :manager_employee_id,
                        :status
                    );
                END;
            ", [
                'branch_id' => $id,
                'branch_name' => $request->branch_name,
                'location' => $request->location,
                'manager_employee_id' => $request->manager_employee_id,
                'status' => $request->status
            ]);

            return redirect()->back()->with('success', 'Branch updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a branch using the Oracle PL/SQL package procedure.
     */
    public function destroy($id)
    {
        try {
            DB::statement("
                BEGIN
                    BANK_ADMIN_PKG.DELETE_BRANCH(:branch_id);
                END;
            ", ['branch_id' => $id]);

            return redirect()->back()->with('success', 'Branch deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Oracle PL/SQL Error: ' . $e->getMessage());
        }
    }
}
