<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Employee;
use App\Modules\Accounting\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->paginate(15);

        return view('modules.accounting.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();

        return view('modules.accounting.payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'regular_hours' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $payroll = Payroll::create($validated);
        $payroll->calculatePayroll();

        return redirect()->route('modules.accounting.payroll.index')
            ->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        return view('modules.accounting.payroll.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'active')->get();

        return view('modules.accounting.payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'regular_hours' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $payroll->update($validated);
        $payroll->calculatePayroll();

        return redirect()->route('modules.accounting.payroll.index')
            ->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('modules.accounting.payroll.index')
            ->with('success', 'Payroll deleted successfully.');
    }

    public function calculate(Payroll $payroll)
    {
        $payroll->calculatePayroll();

        return redirect()->route('modules.accounting.payroll.show', $payroll)
            ->with('success', 'Payroll calculated successfully.');
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update(['status' => 'approved']);

        return redirect()->route('modules.accounting.payroll.show', $payroll)
            ->with('success', 'Payroll approved successfully.');
    }

    public function process(Payroll $payroll)
    {
        $payroll->update(['status' => 'processed']);

        return redirect()->route('modules.accounting.payroll.show', $payroll)
            ->with('success', 'Payroll processed successfully.');
    }

    // Employee management methods
    public function employees()
    {
        $employees = Employee::paginate(15);

        return view('modules.accounting.payroll.employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        return view('modules.accounting.payroll.employees.create');
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'pay_rate' => 'required|numeric|min:0',
            'pay_type' => 'required|in:hourly,salary',
        ]);

        Employee::create($validated);

        return redirect()->route('modules.accounting.payroll.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function showEmployee(Employee $employee)
    {
        return view('modules.accounting.payroll.employees.show', compact('employee'));
    }

    public function editEmployee(Employee $employee)
    {
        return view('modules.accounting.payroll.employees.edit', compact('employee'));
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,'.$employee->id,
            'pay_rate' => 'required|numeric|min:0',
            'pay_type' => 'required|in:hourly,salary',
        ]);

        $employee->update($validated);

        return redirect()->route('modules.accounting.payroll.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroyEmployee(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('modules.accounting.payroll.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
