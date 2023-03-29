@php
  base_path('vendor/autoload.php');



  $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $excel->getActiveSheet();

           
            $invoicedata = $data[0]['no_invoice'];
            $namadata = $data[0]['nama'];
            $tgldata = $data[0]['tgl_pembelian'];
            $jeniskelamindata = $data[0]['gender'];
            $saldodata = $data[0]['saldo'];
            $datakosong = '';

            $nama_brg = 8;
            $qty = 8;
            $harga = 8;
            $noBarang = 8;

            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $styleArray3 = [
                
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];
            $styleArray2 = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'font' => [
                    'bold' => true,
                ],
            ];
            $sheet->getStyle('A1')->applyFromArray($styleArray);
            $sheet->getStyle('A2')->applyFromArray($styleArray);
            $sheet->getStyle('A3')->applyFromArray($styleArray);
            $sheet->getStyle('A4')->applyFromArray($styleArray);
            $sheet->getStyle('A5')->applyFromArray($styleArray);
            $sheet->getStyle('A6')->applyFromArray($styleArray);
            $sheet->getStyle('A7')->applyFromArray($styleArray2);
            $sheet->getStyle('B7')->applyFromArray($styleArray2);
            $sheet->getStyle('C7')->applyFromArray($styleArray2);
            $sheet->getStyle('D7')->applyFromArray($styleArray2);
            $sheet->getStyle('C1:C4')->applyFromArray($styleArray3);

            $sheet->setCellValue('A1','No.Invoice');
            $sheet->setCellValue('A2','Customer Name');
            $sheet->setCellValue('A3','Date');
            $sheet->setCellValue('A4','Gender');
            $sheet->setCellValue('A5','Saldo');
            $sheet->setCellValue('A6','');
            $sheet->setCellValue('A7','Nama Barang');
            $sheet->setCellValue('B7','Quantity');
            $sheet->setCellValue('C7','Harga');
            $sheet->setCellValue('D7','Total Harga');

            $sheet->setCellValue('B1',':');
            $sheet->setCellValue('B2',':');
            $sheet->setCellValue('B3',':');
            $sheet->setCellValue('B4',':');
            $sheet->setCellValue('B5',':');
            $sheet->setCellValue('B6','');

            $tgldata_formatted = date('d-m-Y', strtotime($tgldata));

            $sheet->setCellValue('C1',$invoicedata);
            $sheet->setCellValue('C2',$namadata);
            $sheet->setCellValue('C3',$tgldata_formatted);
            $sheet->setCellValue('C4',$jeniskelamindata);
            $sheet->setCellValue('C5',$saldodata);
            $sheet->setCellValue('C6',$datakosong);
            $sheet->getStyle('C5',$saldodata)->getNumberFormat()->setFormatCode("Rp #,##0.00");
            $sheet->getStyle('C3',$tgldata_formatted)->getNumberFormat()->setFormatCode('dd-mm-yyyy');


           foreach($data as $detail)
            {
                
                $brgdata = $detail["nama_brg"];
                $qtydata = $detail["qty"];
                $hargadata = $detail["harga"];

                $sheet->setCellValue('A'.$noBarang,$brgdata); 
                $sheet->setCellValue('B'.$noBarang,$qtydata); 
                $sheet->setCellValue('C'.$noBarang,$hargadata);
                $sheet->getStyle('C'.$noBarang)->getNumberFormat()->setFormatCode("Rp #,##0.00");

               
                $sheet->setCellValue('A'.$noBarang, $brgdata);
                $sheet->setCellValue('B'.$noBarang, $qtydata);
                $sheet->setCellValue('C'.$noBarang, $hargadata);
                $sheet->getStyle('C'.$noBarang)->getNumberFormat()->setFormatCode("Rp #,##0.00");

               
                $sheet->setCellValue('D'.$noBarang, '=B'.$noBarang.'*C'.$noBarang); 
                $sheet->getStyle('D'.$noBarang)->getNumberFormat()->setFormatCode("Rp #,##0.00");


                $noBarang++; 
            }
           
            $sheet->setCellValue('C'.($noBarang+1), 'Total');
            $sheet->getStyle('C'.($noBarang+1))->getFont()->setBold(true);
            $sheet->setCellValue('D'.($noBarang+1), '=SUM(D8:D'.($noBarang-1).')');
            $sheet->getStyle('D'.($noBarang+1))->getNumberFormat()->setFormatCode("Rp #,##0.00");

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);


            ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Data Customer_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
    
            $xlsxWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($excel, 'Xlsx');
            $xlsxWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
            $xlsxWriter->save('php://output');

@endphp


