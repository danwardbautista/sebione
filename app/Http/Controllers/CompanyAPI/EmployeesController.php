<?php

namespace App\Http\Controllers\CompanyAPI;

use App\Http\Controllers\Controller;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    //
    public function index()
    {
        $employees = Employees::Join('companies','companies.id', '=', 'FK_employees_companies')
        ->select('employees.*','companies.name')
        ->get();

        $status_code = 200;

        return response([
            'message' => "Employees displayed successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $employees
        ], $status_code);
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'FK_employees_companies' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:employees',
            'phone' => 'nullable|regex:(^09\d{9}$)|unique:employees'
        ];
        $validator = Validator::make($request->all(), $rules, $messages= [
            'FK_employees_companies.exists' => 'Company does not exist on the database.',
            'FK_employees_companies.required' => 'Company is required.'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'FK_employees_companies' => 'required',
            'email' => 'nullable',
            'phone' => 'nullable'
        ]);

        $employees = Employees::create($data);
        $status_code = 201;

        return response([
            'message' => "Employee created successfully", 
            'status' => 'CREATED',
            'status_code' => $status_code,
            'results' => $employees
        ], $status_code);
    }

    public function show($id)
    {
        $employees = Employees::find($id);

        if (!$employees) {

            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Employee not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $status_code = 200;

        return response([
            'message' => "Employee displayed successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $employees
        ], $status_code);
    }

    public function update(Request $request, $id)
    {
        $employees = Employees::find($id);
        if (!$employees) {

            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Employee not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'FK_employees_companies' => 'required|exists:companies,id',
            'email' => 'nullable|email|unique:employees,email,' . $employees->id,
            'phone' => 'nullable|regex:(^(09)\\d{9})|unique:employees,phone,' . $employees->id,
        ]
        ;
        $validator = Validator::make($request->all(), $rules, $messages= [
            'FK_employees_companies.exists' => 'Company does not exist on the database.',
            'FK_employees_companies.required' => 'Company is required.'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'FK_employees_companies' => 'required',
            'email' => 'nullable',
            'phone' => 'nullable'
        ]);

        $employees->update($data);
        $status_code = 200;

        return response([
            'message' => "Employee updated successfully", 
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $employees
        ], $status_code);
    }

    public function destroy($id)
    {
        $employees = Employees::find($id);

        if (!$employees) {
            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Employee not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $employees->delete();
        $status_code = 200;

        return response([
            'message' => "Employee deleted successfully", 
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $employees
        ], $status_code);
    }

}
