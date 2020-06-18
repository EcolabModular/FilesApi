<?php

namespace App\Http\Controllers;

use App\PDF;
use App\File;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
    public function makeReport(Request $request)
    {

        $rules = [
            'user_id' => 'required|integer|min:1',
            'user_name' => 'required',
            'user_code' => 'required',
            'user_email' => 'required',
            'user_institution' => 'required|integer|min:1',
            'report_id' => 'required|integer|min:1',
            'item_id' => 'required|integer|min:1',
        ];

        $this->validate($request,$rules);
        $reportInfo = DB::connection('mysql2')->select("SELECT * FROM reports WHERE id = :id", ['id' => $request->report_id]);

        switch($reportInfo[0]->reportType_id){
            case "1": // PREVENTIVO 
                $reportTitle = "Informe de Realización de Mantenimiento Preventivo";
                $tipo = "Preventivo";
            break;
            case "2": // CORRECTIVO
                $reportTitle = "Informe de Realización de Mantenimiento Correctivo";
                $tipo = "Correctivo";
            break;
            case "3": // PREDICTIVO
                $reportTitle = "Informe de Realización de Mantenimiento Predictivo";
                $tipo = "Predictivo";
            break;
            default:
                $reportTitle = "Reporte de Realización de Mantenimiento General";
                $tipo = "General";
            break;
        }
        
        $fields = DB::connection('mysql2')->select("SELECT * FROM report_fields WHERE reportType_id = :id AND isEnabled = :en",['id'=>$reportInfo[0]->reportType_id,'en' => '1']);
        $institutionInfo = DB::connection('mysql3')->select("SELECT * FROM institutions WHERE id = :id",['id'=>$request->user_institution]);


        static $headers = [
            ['#',10],
            ['ITEM',180],
        ];

        $this->pdf->AliasNbPages();
        $this->pdf->AddPage('P', 'letter', 0);
        $this->pdf->SetTitle('Ecolab '.$tipo, true);
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->ln(10);
        $this->pdf->Cell(0, 5, utf8_decode('Sistema Integral de Información y Administración de Laboratorios'), 0, 1, 'C');
        
        $this->pdf->ln(5);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 5,utf8_decode($reportTitle),0, 1, 'C');

        $this->pdf->ln(5);
        $this->pdf->SetFont('Arial', 'B', 12);
        $this->pdf->Cell(0, 5,utf8_decode($institutionInfo[0]->acronym),0, 1, 'C');

        $this->pdf->ln(10);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->MultiCell(175, 4, utf8_decode($reportInfo[0]->description), 0, 'J');
        $this->pdf->ln(8);

        /**DATOS GENERALES */
        $this->pdf->ln(5);
        $this->pdf->InsertHeaderCustom($this->pdf,190,5,'DATOS GENERALES',1,1);
        $this->pdf->insertHeaderVertical($this->pdf,65,5,'UNIVERSIDAD DEL RESPONSABLE');
        $this->pdf->insertRowToRightHeader($this->pdf,125,5,$institutionInfo[0]->name);
        $this->pdf->insertHeaderVertical($this->pdf,65,5,'NOMBRE DEL RESPONSABLE');
        $this->pdf->insertRowToRightHeader($this->pdf,125,5,$request->user_name);
        $this->pdf->insertHeaderVertical($this->pdf,65,5,'CÓDIGO');
        $this->pdf->insertRowToRightHeader($this->pdf,125,5,$request->user_code);
        $this->pdf->insertHeaderVertical($this->pdf,65,5,'CONTACTO');
        $this->pdf->insertRowToRightHeader($this->pdf,125,5,$request->user_email);
        $this->pdf->insertHeaderVertical($this->pdf,65,5,'FECHA DE REALIZACIÓN');
        $this->pdf->insertRowToRightHeader($this->pdf,125,5,Carbon::now());
        /**FIN DE DATOS GENERALES */

        /** LLENADO DINAMICO DEL REPORTE */
        $this->pdf->ln(10);
        $totalItems = 1;
        $this->pdf->InsertHeaderCustom($this->pdf,190,5,'EVALUACIÓN DEL REPORTE',1,1);
        for($i = 0; $i < count($fields); $i++){
            $this->pdf->ln(5);
            $this->pdf->insertHeaderCustom($this->pdf,$headers[0][1],5,$headers[0][0] . $totalItems, 1,0);
            $this->pdf->insertHeaderCustom($this->pdf,$headers[1][1],5,$fields[$i]->title,1,1);
            $this->pdf->InsertMultiCell($this->pdf,190,5,$fields[$i]->description,'C');
            $this->pdf->insertHeaderCustom($this->pdf,190,5,"RESPUESTA",1,1);
            if($request->has("param".$i)){
                $this->pdf->InsertMultiCell($this->pdf,190,5,$request["param".$i],'C');
            }else{
                $this->pdf->InsertMultiCell($this->pdf,190,5,"Ninguna/Observación",'C');
            }
            //$this->pdf->InsertMultiCell($this->pdf,190,5,$fields[$i]->description,'C'); // REMPLAZAR POR REQUEST ($request->field)
            $totalItems++;
        }
        //save file
        $filename_encrypted = str_random(25);

        Storage::disk('files')->put($filename_encrypted.'.pdf', $this->pdf->Output('S'));

        
        $File = File::create([
            'nameOrigin' => 'ecolab',
            'nameEncrypted' => $filename_encrypted,
            'fileExtension' => 'pdf',
            'url' => url('/') . "/reportfiles/" . $filename_encrypted.'.pdf',
            'user_id' => $request->user_id,
            'report_id'=> $request->report_id,
            'item_id' => $request->item_id,
        ]);
        return $this->successResponse($File, Response::HTTP_CREATED);
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
            'user_id' => 'required|integer|min:1',
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
                if($request->has('idItem')){
                    $File->item_id = $request['idItem'];
                }
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
            'user_id' => 'required',
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
                
                if($request->has('idReport')){
                    $File->report_id = $request['idReport'];
                }
                if($request->has('idItem')){
                    $File->item_id = $request['idItem'];
                }

            } else {
                return $this->errorResponse('Cannot upload file', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }else{
            if($request->has('idReport')){
                $File->report_id = $request['idReport'];
            }
            if($request->has('idItem')){
                $File->item_id = $request['idItem'];
            }
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
