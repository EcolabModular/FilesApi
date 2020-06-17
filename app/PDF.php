<?php

namespace App;

use Crabbly\Fpdf\Fpdf;

class PDF extends Fpdf
{
    public function Header()
    {
        $this->Image(base_path().'/public/media/logo2.png', 10, 10, 75);

        $this->Image(base_path().'/public/media/bgai-03.png', 157, 10, 45);
        // Arial bold 15
        // Salto de línea
        $this->Ln(20);
    }

    public function Footer()
    {
        $domicilio = utf8_decode("Guadalajara, Jalisco, México. Tels. [52] (33) 3942 4100 Ext. 14135");

        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 6);
        $this->SetTextColor(3, 3, 3);
        $this->SetFillColor(240, 255, 240);
        // Número de página
        $this->Cell(0, 3, 'Liceo No.496. Piso 6,Colonia Centro C.P. 44100.', 0, 1, 'C');

        $this->Cell(0, 3, $domicilio, 0, 1, 'C');
        $this->Cell(0, 3, 'www.sems.udg.mx', 0, 0, 'C');
        $this->Cell(0, 3, 'No. pagina:' . $this->PageNo() . '', 0, 0, 'R');
        $this->Image(base_path().'/public/media/LogoSEMS.jpg', 10, 260, 50);
    }
}