<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use App\Models\Catalogs\EducationType;
use App\Models\Catalogs\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EducationTypeController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:education-type-list', ['only' => ['index']]);
        $this->middleware('permission:education-type-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:education-type-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:education-type-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $types = EducationType::orderBy('name', 'asc')->paginate(10);
        $this->logActivity(json_encode(['activity' => 'Getting Education Type']), request()->ip(), request()->userAgent(), session('id'));

        return view('Catalogs.EducationTypes.index', compact('types'));
    }

    public function create()
    {
        $catalog = EducationType::get();
        return view('Catalogs.EducationTypes.create', compact('catalog'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:document_types,name',
            'description' => 'required',
            ]);

        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Saving Document Type Failed', 'errors' => json_encode($validator)]), request()->ip(), request()->userAgent(), session('id'));

            return redirect()->back()->withErrors($validator)->withInput();
        }
        $catalog = EducationType::create(['name' => $request->input('name') , 'description'=>$request->input('description')]);

        return redirect()->route('EducationTypes.index')
            ->with('success', 'Document type created successfully');
    }

    public function edit($id)
    {
        $catalog = EducationType::find($id);
        return view('Catalogs.EducationTypes.edit', compact('catalog'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);

        $catalog = EducationType::find($id);
        $catalog->name = $request->input('name');
        $catalog->description = $request->input('description');
        $catalog->status = $request->input('status');
        $catalog->save();

        return redirect()->route('EducationTypes.index')
            ->with('success', 'Document type updated successfully');
    }
    public function show(Request $request)
    {
        $name=$request->name;
        $types=EducationType::where('name','LIKE', "%$name%")->paginate(10);
        $types->appends(request()->query())->links();
        if ($types->isEmpty()){
            return redirect()->route('EducationTypes.index')->withErrors('Not Found!')->withInput();
        }
        return view('Catalogs.EducationTypes.index', compact('types','name'));
    }
}
