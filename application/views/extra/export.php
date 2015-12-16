<?php
/**
 * This view builds an Excel5 file containing the list of overtime requests declared by the connected employee.
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('extra_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('extra_export_thead_id'));
$sheet->setCellValue('B1', lang('extra_export_thead_date'));
$sheet->setCellValue('C1', lang('extra_export_thead_duration'));
$sheet->setCellValue('D1', lang('extra_export_thead_cause'));
$sheet->setCellValue('E1', lang('extra_export_thead_status'));
$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$extras = $this->overtime_model->getExtrasOfEmployee($this->user_id);

$line = 2;
foreach ($extras as $extra) {
    $date = new DateTime($extra['date']);
    $startdate = $date->format(lang('global_date_format'));
    $sheet->setCellValue('A' . $line, $extra['id']);
    $sheet->setCellValue('B' . $line, $startdate);
    $sheet->setCellValue('C' . $line, $extra['duration']);
    $sheet->setCellValue('D' . $line, $extra['cause']);
    $sheet->setCellValue('E' . $line, lang($extra['status_name']));
    $line++;
}

//Autofit
foreach(range('A', 'E') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

$filename = 'extra.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
$objWriter->save('php://output');
