<?php

namespace App;

use Crabbly\Fpdf\Fpdf;

class PDF extends Fpdf
{
    public function Header()
    {

        $this->Image(base_path().'/public/media/logo2.png', 10, 10, 85);

        $this->Image(base_path().'/public/media/logoheader.png', 157, 10, 45);
        // Arial bold 15
        // Salto de línea
        $this->Ln(20);
    }

    public function Footer()
    {
        $domicilio = utf8_decode("Guadalajara, Jalisco, México.");

        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 6);
        $this->SetTextColor(3, 3, 3);
        $this->SetFillColor(240, 255, 240);
        // Número de página
        $this->Cell(0, 3, utf8_decode('Blvd. Gral. Marcelino García Barragán 1421, Olímpica, 44430.'), 0, 1, 'C');

        $this->Cell(0, 3, $domicilio, 0, 1, 'C');
        $this->Cell(0, 3, 'ECOLAB', 0, 0, 'C');
        $this->Cell(0, 3, 'No. pagina:' . $this->PageNo() . '', 0, 0, 'R');
        $this->Image(base_path().'/public/media/logo.png', 10, 260, 20);
    }

    function checkbox($pdf, $checked = TRUE, $checkbox_sizex = 15, $checkbox_sizey = 5){
        $ori_font_family = 'Arial';
        $ori_font_size = '10';
        $ori_font_style = '';
        if ($checked == TRUE)
            $check = "4";
        else
            $check = "8";

        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFont('ZapfDingbats', '', $ori_font_size);
        $pdf->Cell($checkbox_sizex, $checkbox_sizey, $check, 1, 0, 'C');
        $pdf->SetFont($ori_font_family, $ori_font_style, $ori_font_size);
    }

    function insertHeaderVertical($pdf, $x, $y,$title){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(240, 255, 240);
        $pdf->SetFillColor(117, 27, 54);
        $pdf->Cell($x, $y, utf8_decode($title), 1, 0, 'C', true);
    }
    
    function insertRowToRightHeader($pdf, $x, $y,$data,$border = 1, $ln = 1, $align = 'C'){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell($x, $y, utf8_decode($data), $border, $ln, $align, true);
    }

    function insertHeaders($pdf, $x, $y,$data){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(240, 255, 240);
        $pdf->SetFillColor(117, 27, 54);

        foreach($data as $valor){
            $pdf->Cell($x, $y, utf8_decode($valor), 1, 0, 'C', true);
        }
    }

    function insertHeadersCustom($pdf,$y,$data){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(240, 255, 240);
        $pdf->SetFillColor(117, 27, 54);

        foreach ($data as list($h, $x)) {
            $pdf->Cell($x, $y, utf8_decode($h), 1, 0, 'C', true);
        }
    }

    function insertHeadersCustomsizeFont($pdf,$y,$data, $fontSize){
        $pdf->SetFont('Arial', '', $fontSize);
        $pdf->SetTextColor(240, 255, 240);
        $pdf->SetFillColor(117, 27, 54);

        foreach ($data as list($h, $x)) {
            $pdf->Cell($x, $y, utf8_decode($h), 1, 0, 'C', true);
        }
    }

    function insertHeaderCustom($pdf,$x,$y,$data,$border,$ln){
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(240, 255, 240);
        $pdf->SetFillColor(117, 27, 54);

        $pdf->Cell($x, $y, utf8_decode($data), $border, $ln, 'C', true); 
    }

    function insertHeaderSecondaryColor($pdf,$x,$y,$data,$border,$ln){
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(124, 124, 124);

        $pdf->Cell($x, $y, utf8_decode($data), $border, $ln, 'C', true); 
    }

    function insertRows($pdf, $x, $y,$data){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);

        foreach($data as $valor){
            $pdf->Cell($x, $y, utf8_decode($valor), 1, 0, 'C', true);
        }
    }

    function insertRowCustom($pdf,$x, $y,$data,$border = 1,$ln = 0){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);

        $pdf->Cell($x, $y, utf8_decode($data), $border, $ln, 'C', true);
        
    }

    function insertRowCustomSizeFont($pdf,$x, $y,$data,$sizeFont){
        $pdf->SetFont('Arial', '', $sizeFont);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);

        $pdf->Cell($x, $y, utf8_decode($data), 1, 0, 'C', true);
        
    }

    function insertRowsCustom($pdf, $y,$data){
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);

        foreach ($data as list($h, $x)) {
            $pdf->Cell($x, $y, utf8_decode($h), 1, 0, 'C', true);
        }
    }

    function insertMulticell($pdf,$x,$y,$data,$align = 'J'){
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetTextColor(3, 3, 3);
        $pdf->SetFillColor(255, 255, 255);
        
        $pdf->MultiCell($x, $y, utf8_decode($data), 1, $align, true);

    }

    function insertItemMulticell($pdf, $num, $titulo, $opcion, $observacion, $headers, $headersCheck, $checkBoxSizeX = 10){
        
        $pdf->insertHeaderCustom($pdf,$headers[0][1],5,$headers[0][0] . $num, 1,0);
        $pdf->insertHeaderCustom($pdf,$headers[1][1],5,$headers[1][0],1,1);

        $pdf->InsertMultiCell($pdf,190,5,$titulo,'C');
        foreach ($headersCheck as list($h, $x)) {
            $pdf->insertHeaderCustom($pdf,$x,5,$h,1,0);
        }
        $pdf->ln(5);
        if($opcion == "N/A"){
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
        }else if($opcion == 1){
            $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
        }else if($opcion == 0){
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
        }else{
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
        }
        $pdf->ln(5);
        $pdf->insertHeaderSecondaryColor($pdf,190,5,'OBSERVACIONES',1,1);
        $pdf->InsertMultiCell($pdf,190,5,$observacion,'C');
    }

    function insertItem($pdf, $num, $titulo, $opcion, $observacion, $headers, $headersCheck, $checkBoxSizeX = 10){
        if(strlen($titulo) < 90){
            $pdf->insertHeadersCustom($pdf,5,$headers);
            $pdf->insertHeadersCustomsizeFont($pdf,5,$headersCheck,6);
            $pdf->ln(5);
            $pdf->insertRowCustom($pdf,10,5,$num);
            $pdf->insertRowCustom($pdf,140,5,$titulo);
            if($opcion == "N/A"){
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
            }else if($opcion == 1){
                $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            }else if($opcion == 0){
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, TRUE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            }else{
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
                $pdf->checkbox($pdf, FALSE, $checkBoxSizeX);
            }
            $pdf->ln(5);
            $pdf->insertHeaderSecondaryColor($pdf,190,5,'OBSERVACIONES',1,1);
            $pdf->InsertMultiCell($pdf,190,5,$observacion);
        }else{
            $pdf->insertItemMulticell($pdf, $num, $titulo, $opcion, $observacion, $headers, $headersCheck);
        }
    }
}