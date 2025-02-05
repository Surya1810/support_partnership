<?php

namespace App\Http\Controllers;

use App\Imports\DocumentsImport;
use App\Models\Document;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::orderBy('number', 'desc')->get();

        return view('document.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|max:255',
            'type' => 'required|max:255',
            'date' => 'required|',
            'purpose' => 'required|max:255',
            'company' => 'required|max:255',
        ]);

        $old = session()->getOldInput();

        $document = new Document();
        $document->number = $request->number;
        $document->type = $request->type;
        $document->date = $request->date;
        $document->purpose = $request->purpose;
        $document->company = $request->company;
        $document->desc = $request->desc;
        $document->save();

        return redirect()->route('document.index')->with(['pesan' => 'Document created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $document = Document::find($id);
        $document->delete();

        return redirect()->route('document.index')->with(['pesan' => 'Document deleted successfully', 'level-alert' => 'alert-danger']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new DocumentsImport, $request->file('file'));

        return redirect()->route('document.index')->with(['pesan' => 'Document imported successfully', 'level-alert' => 'alert-success']);
    }
}
