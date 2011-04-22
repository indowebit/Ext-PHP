<?php
class xls extends Spreadsheet_Excel_Writer {
	public $startRow = 0;

	public function writeMerge($sheet, $row, $col, $w, $h, $text, $format) {
		$sheet->write($row, $col, $text, $format);
		for ($i=$row; $i<$row+$h; $i++) {	
			for ($j=$col; $j<$col+$w; $j++) {
				if (!(($i==$row) && ($j==$col))) {
					$sheet->writeBlank($i, $j, $format);
				}
			}
		}
		$sheet->mergeCells($row, $col, $row+$h-1, $col+$w-1);
	}
	
	
}
?>