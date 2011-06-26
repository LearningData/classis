<?php 
/** 									column_export.php
 */

$action='class_view.php';

$viewtable=$_SESSION['viewtable'];
$umns=$_SESSION['umns'];

if(!isset($_POST['checkmid'])){
	$error[]='Choose one or more columns to export.';
	}
else{
	require_once 'Spreadsheet/Excel/Writer.php';
  	//$file=fopen('/tmp/class_export.xls', 'w');
	$file='/tmp/class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_head =& $workbook->addFormat();
	$format_head =& $workbook->addFormat(array('Size' => 10,
											   'Align' => 'center',
											   'Color' => 'white',
											   'Pattern' => 1,
											   'Bold' => 1,
											   'FgColor' => 'gray'));
	$format =& $workbook->addFormat(array('Size' => 10,
										  'Align' => 'left',
										  'Bold' => 1
										  ));
	$worksheet =& $workbook->addWorksheet('ClaSS MarkBook Export');

	if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{
		$checkmids=(array)$_POST['checkmid'];

		/*first do the column headers*/
		$worksheet->setColumn(0,0,14);
		$worksheet->setColumn(1,2,25);
		$worksheet->setColumn(3,20,20);
		$worksheet->write(0, 0, 'Enrolment No.', $format_head);
		$worksheet->write(0, 1, 'Surname', $format_head);
		$worksheet->write(0, 2, 'Forename', $format_head);
		$cols=array();
		foreach($checkmids as $checkmid){
			$col=array();
			$col['mid']=$checkmid;
			/* No index for the mid so have to cycle through the MarkBook's umns array every time. */
			for($umnno=0;$umnno<sizeof($umns);$umnno++){
				if($checkmid==$umns[$umnno]['id']){
					$col['scoretype']=$umns[$umnno]['scoretype'];
					$col['head']=$umns[$umnno]['topic'].' '.$umns[$umnno]['component']; 
					//.' '.$umns[$umnno]['entrydate'].
					}
				}
			$cols[]=$col;
			}

		/* Write the column headers */
		foreach($cols as $colno => $col){
			$worksheet->write(0, $colno+3, $col['head'], $format_head);
			}

		/* Now write each student row, looking up the cell values in the MarkBook's viewtable array. */
		$i=1;
		for($rowno=0;$rowno<sizeof($viewtable);$rowno++){
			$worksheet->writenumber($i, 0, $viewtable[$rowno]['sid'], $format);
			$worksheet->write($i, 1, iconv('UTF-8','ISO-8859-1',$viewtable[$rowno]['surname']), $format);
			$worksheet->write($i, 2, iconv('UTF-8','ISO-8859-1',$viewtable[$rowno]['forename']), $format);
			foreach($cols as $colno => $col){
				$value=$viewtable[$rowno][$col['mid']];
				$no=$colno+3;
				if($cols[$colno]['scoretype']=='value'){
					$worksheet->writenumber($i, $no, $value);
					} 
				elseif($cols[$colno]['scoretype']=='percentage'){
					$worksheet->writenumber($i, $no, $value);
					} 
				else{
					$worksheet->write($i, $no, $value);
					}
				}
			$i++;
			}


		/*send the workbook w/ spreadsheet and close them*/ 
		$workbook->close();
		$result[]='Exported table in current view to file.';
?>
		<script>openFileExport('xls');</script>
<?php
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
