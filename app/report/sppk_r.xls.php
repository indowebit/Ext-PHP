<?php
	require_once (ABSPATH."includes/excel-2.0/Writer.php");
	require_once(ABSPATH."includes/xls.php");
	$workbook = new xls();
	$workbook->send('sample_report.xls');
	$worksheet =& $workbook->addWorksheet("sample_report");
	
	$format_title =& $workbook->addFormat(array(
			'Align'=>'center',
			'Bold'=> '1',
			'Size'=>'10'
	));
	$format_title_field =& $workbook->addFormat(array(
			'Align'=>'center',
			'Bold'=> '1',
			'Border'=> '0',
			'Size'=>'8'
	)); 
	$format_text =& $workbook->addFormat(array(		
			'Border'=> '0',
			'Size'=>'8'
	));

	$worksheet->setLandscape();
	$worksheet->setPaper(1);
	
	$workbook->writeMerge($worksheet, 1, 0, 5, 1, 'Sample Report',$format_title);
	$col = 0; 
	$row = 2; 
	$cols = Array(
				Array('name' =>'Jenis','width'=>20),
				Array('name'=>'Tanggal','width'=>20),
				Array('name'=>'Perihal','width'=>70),
				Array('name'=>'Kode','width'=>40),
				Array('name'=>'Tahun','width'=>20)
			); 
	foreach ($cols as $field) {
		$worksheet->writeString($row, $col,$field['name'],$format_title_field);
		//$worksheet->setColumn($row, $col,$field['width']+0);
		//$format_title_field->setTextWrap();
		$col++; 
	}; 
	$row++; 

	$data = Array(); 	
	while ($rowx=$rs->FetchNextObject()) {
		$data[] = Array(							 
							$rowx->JENIS,
							formatDate2($rowx->TANGGAL),
							$rowx->PERIHAL,
							$rowx->KODE,
							$rowx->TAHUN
		); 
	}
	
	foreach ($data as $r) {
			$col =0; 
			foreach ($r as $c) {
				$worksheet->writeString($row, $col,$c,$format_text);
				$col++; 
			}
			$row++; 
	}
	
	$workbook->close();

	if (set_magic_quotes_runtime(1)==false)
		die("Set Magic Quote Runtime Failed.");
		
	exit();	
?>