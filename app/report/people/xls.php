<?php

  require_once ABSPATH .'includes/php-excel/PHPExcel.php';
  
    // Create new PHPExcel object
  $objPHPExcel = new PHPExcel();
  
  // Set properties
  $objPHPExcel->getProperties()->setCreator("indowebit")
  							 ->setLastModifiedBy("indowebit")
  							 ->setTitle("Office 2007 XLSX Test Report Document")
  							 ->setSubject("Office 2007 XLSX Test Report Document")
  							 ->setDescription("Test Report for Office 2007 XLSX, generated using PHP classes.")
  							 ->setKeywords("office 2007 openxml php")
  							 ->setCategory("Test Report result file");
  
  

  $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('A1', 'Name')
              ->setCellValue('B1', 'Birthday')
              ->setCellValue('C1', 'Heigth'); 

  $index = 2;             
  while ($row=$rs->FetchNextObject()) {
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$index, $row->NAME)
                ->setCellValue('B'.$index, $row->BIRTHDAY)
                ->setCellValue('C'.$index, $row->HEIGHT);  
    $index++;                              
  }
              
              
              
  // Rename sheet
  $objPHPExcel->getActiveSheet()->setTitle('Report');
  
  
  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $objPHPExcel->setActiveSheetIndex(0);
  
  
  // Redirect output to a client’s web browser (Excel2007)
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="people.xlsx"');
  header('Cache-Control: max-age=0');
  
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;
  
  

