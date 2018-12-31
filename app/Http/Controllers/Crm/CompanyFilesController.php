<?php

namespace App\Http\Controllers\Crm;

use App\Models\Crm\CompanyFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CompanyFilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:read', ['only' => ['index', 'show']]);
        $this->middleware('role:insert', ['only' => ['store']]);
        $this->middleware('role:update', ['only' => ['update', 'multipleUpdate']]);
        $this->middleware('role:delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return CompanyFile
            ::orderBy('company_id', 'asc')
            ->with('company')
            ->get();
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'file' => 'required|string',
            'file_2' => 'string|nullable',
            'description' => 'string|nullable',
        ]);
        if ($validator->fails()) {
            return ['status' => -1, 'msg' => $validator->errors()];
        }
        $result = CompanyFile::create($request->all());
        return ['status' => 0, 'id' => $result->id];
    }
    public function show(CompanyFile $companyFile)
    {
        return $companyFile;
    }
    public function edit(CompanyFile $companyFile)
    {
        //
    }
    public function update(Request $request, CompanyFile $companyFile)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'exists:companies,id',
            'file' => 'string',
            'file_2' => 'string|nullable',
            'description' => 'string|nullable',
            'active' => 'boolean',
        ]);
        if ($validator->fails()) {
            return ['status' => -1, 'msg' => $validator->errors()];
        }
        $companyFile->update($request->all());
        return ['status' => 0, 'id' => $companyFile->id];
    }
    public function destroy(CompanyFile $companyFile)
    {
        //
    }
}
