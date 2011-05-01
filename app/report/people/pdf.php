<?php
require ABSPATH .'includes/fpdf/fpdf.php'; 

  $pdf=new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','B',16);
  $pdf->Cell(40,10,'People List');
  $pdf->Ln(); 
  
  $pdf->SetFont('Helvetica', 'B', 10);
  $pdf->Cell(100,7,'NAME',1); 
  $pdf->Cell(30,7,'BIRTDAY',1); 
  $pdf->Cell(20,7,'HEIGTH',1); 
  $pdf->Ln(); 
  
  $pdf->SetFont('Helvetica', '', 10);
     
  while ($row=$rs->FetchNextObject()) {
    $pdf->Cell(100,7,$row->NAME,1); 
    $pdf->Cell(30,7,$row->BIRTHDAY,1); 
    $pdf->Cell(20,7,$row->HEIGHT,1); 
    $pdf->Ln();    
  }
  
  $pdf->Output();
?>