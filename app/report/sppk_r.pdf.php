<?php
	include_once(ABSPATH."includes/php-pdf/class.ezpdf.php");
	$cols = Array(
				'jenis'=>'Jenis',
				'tanggal'=>'Tanggal',
				'perihal'=>'perihal',
				'kode'=>'Kode',
				'tahun'=>'Tahun'			);
	$options = Array(
				'shaded'=>1,
				'width'=>500,
				'fontSize'=>8,
				'titleFontSize'=>10
			); 

	$data = Array(); 	
	while ($row=$rs->FetchNextObject()) {
		$data[] = Array(
						'jenis'=>$row->JENIS,
						'tanggal'=>formatDate2($row->TANGGAL),
						'perihal'=>$row->PERIHAL,
						'kode'=>$row->KODE,
						'tahun'=>$row->TAHUN
					); 
	}
	
	$pdf = new Cezpdf('a4','potrait');
	$pdf->selectFont('includes/php-pdf/fonts/Helvetica.afm');
	$pdf->ezText('<b>Sample Report</b>',14,Array('justification'=>'center'));
	$pdf->ezSetDy(-20); 
	$pdf->ezTable($data,$cols,'',$options); 
	$pdf->ezStream();
?>