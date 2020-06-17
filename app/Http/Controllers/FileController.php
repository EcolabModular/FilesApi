<?php

namespace App\Http\Controllers;

use App\PDF;
use App\File;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $pdf;

    public function __construct(PDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Return files list
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $files = File::all();

        return $this->showAll($files);
    }

    /**
     * Return files list
     * @return Illuminate\Http\Response
     */
    public function test()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('Courier', 'B', 18);
        $this->pdf->Cell(50, 25, 'Hello World!');

        //save file
        Storage::disk('files')->put('test.pdf', $this->pdf->Output('S'));
        
        return $this->successResponse('test.pdf', Response::HTTP_CREATED);
    }

    /**
     * Create an instance of File
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request);

        $rules = [
            'file' => 'required|mimes:pdf,doc,docx',
            'report_id' => 'required|min:1',
        ];
        
        $this->validate($request, $rules);

        if($request->hasFile('file')){
            $original_filename = $request->file('file')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $filename_encrypted = str_random(25);
            $destination_path = './reportfiles/';
            $fileReport = $filename_encrypted . "." . $file_ext;

            if ($request->file('file')->move($destination_path, $fileReport)) {
                $File = File::create([
                    'nameOrigin' => $original_filename_arr[0],
                    'nameEncrypted' => $filename_encrypted,
                    'fileExtension' => $file_ext,
                    'url' => url('/') . "/reportfiles/" . $fileReport,
                    'report_id'=> $request['idReport'],
                ]);
                return $this->successResponse($File, Response::HTTP_CREATED);
            } else {
                return $this->errorResponse('Cannot upload file', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        return $this->errorResponse('Cannot upload file', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return an specific File
     * @return Illuminate\Http\Response
     */
    public function show($File)
    {
        $File = File::findOrFail($File);

        return $this->successResponse($File);
    }

    /**
     * Update the information of an existing File
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $file)
    {
        //dd($request);

        $rules = [
            'file' => 'mimes:pdf,doc,docx',
            'report_id' => 'required|min:1',
        ];

        $this->validate($request, $rules);

        $File = File::findOrFail($file);

        if($request->hasFile('file')){
            
            $original_filename = $request->file('file')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $filename_encrypted = str_random(25);
            $destination_path = './reportfiles/';
            $fileReport = $filename_encrypted . "." . $file_ext;

            if ($request->file('file')->move($destination_path, $fileReport)) {

                /** ELIMINAR ARCHIVO */
                unlink('./reportfiles/' . $File->nameEncrypted . "." . $File->fileExtension);

                $File->nameOrigin = $original_filename_arr[0];
                $File->nameEncrypted = $filename_encrypted;
                $File->fileExtension = $file_ext;
                $File->url = url('/') . "/reportfiles/" . $fileReport;
                $File->report_id = $request['idReport'];

            } else {
                return $this->errorResponse('Cannot upload file', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            $File->report_id = $request['idReport'];
        }

        $File->save();

        return $this->successResponse($File);
    }

    /**
     * Removes an existing File
     * @return Illuminate\Http\Response
     */
    public function destroy($File)
    {

        $File = File::findOrFail($File);

        /** ELIMINAR ARCHIVO */
        unlink('./reportfiles/' . $File->nameEncrypted . "." . $File->fileExtension);

        $File->delete();

        return $this->successResponse($File);
    }
}
