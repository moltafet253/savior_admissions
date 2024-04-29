<?php

namespace App\Http\Controllers;

use App\Models\Branch\StudentApplianceStatus;
use App\Models\Catalogs\BloodGroup;
use App\Models\Catalogs\DocumentType;
use App\Models\Catalogs\GuardianStudentRelationship;
use App\Models\Country;
use App\Models\Document;
use App\Models\StudentInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:document-list', ['only' => ['index','showUserDocuments']]);
        $this->middleware('permission:document-create', ['only' => ['createDocument','createDocumentForUser']]);
        $this->middleware('permission:document-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:document-delete', ['only' => ['destroy']]);
    }


    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $documentTypes = DocumentType::where('status', '1')->orderBy('name')->get();
        $myDocuments = Document::with('documentType')->where('user_id', session('id'))->orderBy('id', 'desc')->get();
        $myDocumentTypes = Document::with('documentType')->where('user_id', session('id'))->pluck('document_type_id')->all();
        $myDocumentTypes = array_unique($myDocumentTypes);

        return view('Documents.index', compact('documentTypes', 'myDocuments', 'myDocumentTypes'));
    }

    public function createDocument(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'document_type' => 'exists:document_types,id',
            'document_file' => 'required|mimes:png,jpg,jpeg,pdf,bmp|max:2048',
        ]);
        $path = $request->file('document_file')->store('public/uploads/Documents/'.session('id'));
        $document = new Document();
        $document->user_id = session('id');
        $document->document_type_id = $request->document_type;
        $document->src = $path;
        $document->save();
        $this->logActivity(json_encode(['activity' => 'User Document Added', 'document id' => $document->id, 'user_id' => $document->user_id]), request()->ip(), request()->userAgent());

        return response()->json(['success' => 'Document added!'], 200);
    }

    public function createDocumentForUser(Request $request, $user_id): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'document_type' => 'exists:document_types,id',
            'document_file' => 'required|mimes:png,jpg,jpeg,pdf,bmp|max:2048',
            'user_id' => Rule::exists('users', 'id')->where(function ($query) use ($user_id) {
                $query->where('id', (int) $user_id);
            }),
        ]);
        $path = $request->file('document_file')->store('public/uploads/Documents/'.session('id'));
        $document = new Document();
        $document->user_id = $user_id;
        $document->document_type_id = $request->document_type;
        $document->src = $path;
        $document->save();
        $this->logActivity(json_encode(['activity' => 'User Document Added', 'document id' => $document->id, 'user_id' => $document->user_id]), request()->ip(), request()->userAgent());

        return response()->json(['success' => 'Document added!'], 200);
    }

    public function showUserDocuments($user_id): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $documentTypes = DocumentType::orderBy('name', 'asc')->get();
        $myDocuments = Document::with('documentType')->where('user_id', $user_id)->orderBy('id', 'desc')->get();
        $documentOwner = User::find($user_id);
        $myDocumentTypes = Document::with('documentType')->where('user_id', $user_id)->pluck('document_type_id')->all();

        return view('Documents.index', compact('documentTypes', 'myDocuments', 'myDocumentTypes', 'documentOwner', 'user_id'));

    }

    public function uploadStudentDocumentByParent($student_id): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $studentInformation = StudentInformation::with('generalInformations')->where('student_id', $student_id)->where('guardian', session('id'))->first();
        if (empty($studentInformation)) {
            abort(403);
        }
        $checkStudentApplianceStatus = StudentApplianceStatus::where('student_id', $student_id)->where('documents_uploaded', 0)->first();
        if (empty($checkStudentApplianceStatus)) {
            return redirect()->back()->withErrors('Student documents are uploaded or under review');
        }

        $bloodGroups=BloodGroup::get();
        $guardianStudentRelationships=GuardianStudentRelationship::get();
        $countries=Country::orderBy('en_short_name','asc')->get();
        $nationalities=Country::orderBy('nationality','asc')->get();
        return view('Documents.UploadDocumentsParent.create', compact('studentInformation','bloodGroups','guardianStudentRelationships','countries','nationalities'));

    }

    public function uploadStudentDocuments(Request $request): \Illuminate\Http\RedirectResponse
    {
        $studentInformation = StudentInformation::where('student_id', $request->student_id)->where('guardian', session('id'))->first();
        if (empty($studentInformation)) {
            abort(403);
        }
        $checkStudentApplianceStatus = StudentApplianceStatus::where('student_id', $request->student_id)->where('documents_uploaded', 0)->first();
        if (empty($checkStudentApplianceStatus)) {
            return redirect()->back()->withErrors('Student documents are uploaded or under review');
        }

        $studentAppliance = StudentApplianceStatus::where('student_id', $request->student_id)->first();
        $studentAppliance->documents_uploaded = 1;
        $studentAppliance->save();

        return redirect()->route('dashboard')->with('success', 'Documents Uploaded successfully!');

    }
}
