<?php

namespace App\Http\Controllers\CompanyAPI;

use App\Http\Controllers\Controller;
use App\Mail\CompanyRegistered;
use App\Models\Companies;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class CompanyController extends Controller
{
    public function dashboardInfo()
    {
        $companies = Companies::count();
        $employees = Employees::count();

        $status_code = 200;

        return response([
            'message' => "Dashboard info displayed successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'companies_count' => $companies,
            'employees_count' => $employees,
        ], $status_code);
    }

    //READ ALL COMPANY
    public function index()
    {
        $companies = Companies::get();

        $status_code = 200;

        return response([
            'message' => "Companies displayed successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $companies
        ], $status_code);
    }

    //CREATE COMPANY
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:companies',
            'email' => 'nullable|email|unique:companies',
            'logo' => 'nullable|mimes:jpeg,jpg,png|dimensions:min_width=100,min_height=100',
            'website' => 'nullable|url',
        ];
        $validator = Validator::make($request->all(), $rules, $messages = [
            'logo.dimensions' => 'Minimum company logo size is 100x100.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->validate([
            'name' => 'required|unique:companies',
            'email' => 'nullable',
            'website' => 'nullable',
        ]);

        $companies = Companies::create($data);
        $status_code = 201;

        if ($request->hasFile('logo')) {

            $uploaded_files = $request->logo->store('public/CompanyLogo/');
            $companies->logo = $request->logo->hashName();

            $results = $companies->save();
        }

        if ($request->email) {
            try {
                Mail::to($request->email)->send(new CompanyRegistered($request->email, $request->name));
            } catch (\Exception $e) {
                return response([
                    'message' => 'Email was not sent. An error occured.',

                ], 400);
            }
        }

        return response([
            'message' => "Company created successfully",
            'status' => 'CREATED',
            'status_code' => $status_code,
            'results' => $companies
        ], $status_code);
    }

    //READ ALL COMPANY BY ID
    public function show($id)
    {
        $companies = Companies::find($id);

        if (!$companies) {

            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Company not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $status_code = 200;

        return response([
            'message' => "Company displayed successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $companies
        ], $status_code);
    }

    //UPDATE COMPANY BY ID
    public function update(Request $request, $id)
    {
        $companies = Companies::find($id);
        if (!$companies) {

            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Company not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }

        $rules = [
            'name' => 'required|unique:companies,name,' . $companies->id,
            'email' => 'nullable|email|unique:companies,email,' . $companies->id,
            'logo' => 'nullable|mimes:jpeg,jpg,png|dimensions:min_width=100,min_height=100',
            'website' => 'nullable|url',
        ];
        $validator = Validator::make($request->all(), $rules, $messages = [
            'logo.dimensions' => 'Minimum company logo size is 100x100.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->validate([
            'name' => 'required',
            'email' => 'nullable',
            'website' => 'nullable',
        ]);

        if ($request->hasFile('logo')) {
            if ($companies->logo != null) {
                if (file_exists(storage_path('app/public/CompanyLogo/' . $companies->logo))) {
                    unlink(storage_path('app/public/CompanyLogo/' . $companies->logo));
                }
            }

            $uploaded_files = $request->logo->store('public/CompanyLogo/');
            $companies->logo = $request->logo->hashName();

            $results = $companies->save();
        }

        $companies->update($data);
        $status_code = 200;

        return response([
            'message' => "Company updated successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $companies
        ], $status_code);
    }

    //DELETE Company
    public function destroy($id)
    {
        $companies = Companies::find($id);

        if (!$companies) {
            $error_status_code = 404;

            abort(
                response()->json([
                    'message' => "Company not found!",
                    'status' => 'NOT FOUND',
                    'status_code' => $error_status_code,
                    'results' => []
                ], $error_status_code)
            );
        }
        if ($companies->logo != null) {
            unlink(storage_path('app/public/CompanyLogo/' . $companies->logo));
        }

        $companies->delete();
        $status_code = 200;

        return response([
            'message' => "Company deleted successfully",
            'status' => 'OK',
            'status_code' => $status_code,
            'results' => $companies
        ], $status_code);
    }

    public function fileLogoImage($fileName)
    {
        $path = public_path('storage') . '/companyLogo/' . $fileName;
        // return Response::display($path);        

        return response()->file($path);
    }
}
