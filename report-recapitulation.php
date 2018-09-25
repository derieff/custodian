<?php
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Doni Romdoni																						=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
= Purpose			: Pembuatan report Rekapitulasi kekurangan Dokumen Pembebasan Lahan yang sudah diterima				=																					=
=========================================================================================================================
*/
session_start(); 
//cek user login session 
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
}

if (!$_POST['btnSubmit']) {
//load View Template
require_once "./include/template.inc";
$page=new Template();
}

//load report class 
require_once "./include/class.report-recapitulation.php";
$reportClass = new reportRekapitulasi();

//connection to database
include ("./config/config_db.php"); 


//get PT
$ptOption = $reportClass->getPTOption( $_POST['optPT']);

//get Area
$areaOption = $reportClass->getAreaOption($_POST['optArea']);

//GET year option
$yearOption = $reportClass->getYearOption( $_POST['optTahun']);

//check if submit data
if ($_POST['btnSubmit']) {
	echo "<link href='./css/style-print-a3.css' rel='stylesheet' type='text/css'>";
	echo "<SCRIPT>
				function printPage(){
				document.getElementById('PrintButton').style.display = 'none'
				window.print()
				document.getElementById('PrintButton').style.display = 'block'
				}
		  </SCRIPT>";
	if ($_POST['optTipe']=="kekurangan") {
		$data= $reportClass->getDataReport($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);
		echo "<title>Laporan Rekapitulasi Kekurangan Pembebasan Lahan</title>";
	}
	else if ($_POST['optTipe']=="ketersediaan"){
		$data= $reportClass->getDataReportRekapitulasi($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);	
		echo "<title>Laporan Rekapitulasi Pembebasan Lahan</title>";
	}
	$table = $reportClass->drawTableHeader($data,$_POST['optTipe']);
	print_r ($table);
}
?>
<title>Custodian System | Laporan Rekapitulasi Pembebasan Lahan</title>
</form>
<?php
$ActionContent = " 
	<form name='list' method='post' target='_blank' >
	<table width='100%'>
	<tr>
		<td width='9%' align='left'>Area</td>
		<td width='1%'>:</td>
		<td width='80%' align='left'>
			 <select name='optArea'>$areaOption</select>
		</td>
		<td width='10%'>
			<input name='btnSubmit' type='submit' value='Cari' class='button-small'>
		</td>
	</tr>
	<tr>
		<td width='9%' align='left'>PT</td>
		<td width='1%'>:</td>
		<td width='80%' align='left'>
			 <select name='optPT'>$ptOption</select>
		</td>
	</tr>
	<tr>
		<td width='9%' align='left'>Tahun</td>
		<td width='1%'>:</td>
		<td width='80%' align='left'>
			 <select name='optTahun'>$yearOption</select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td width='9%' align='left'>Tipe</td>
		<td width='1%'>:</td>
		<td width='80%' align='left'>
			 <select name='optTipe'>
				<option value='kekurangan'>Kekurangan</option>
				<option value='ketersediaan'>Ketersediaan</option>
			 </select>
		</td>
		<td></td>
	</tr>
	</table>
	</form>
";
?>


<?php	
if (!$_POST['btnSubmit']) {
	$page->ActContent($ActionContent);
	$page->Content($MainContent);
	$page->Pagers($Pager);
	$page->ShowWTopMenu();
}
?>