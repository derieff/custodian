<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																				=
= Dibuat Tanggal	: 03 Okt 2018																						=
= Update Terakhir	: 																									=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Pencarian</title>
<?PHP include ("./config/config_db.php"); ?>
<style>

</style>
<script language="JavaScript" type="text/JavaScript">
<?php
$assetOwnershipOpt=new stdClass();
$assetOwnershipOpt->company=[];
$assetOwnershipOpt->requester=[];
$assetOwnershipOpt->year=[];
$assetOwnershipOpt->month=[];
$queryAssetOwnership="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throaod.THROAOD_RegistrationDate) docMonth,YEAR(throaod.THROAOD_RegistrationDate) docYear
						FROM TH_RegistrationOfAssetOwnershipDocument throaod
						LEFT JOIN M_User mu
							ON throaod.THROAOD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throaod.THROAOD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(!array_key_exists($dataAssetOwnership["Company_ID"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->company,[$dataAssetOwnership["Company_ID"]=>$dataAssetOwnership["Company_Name"]]);
	}
	if(!in_array($dataAssetOwnership["docMonth"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->month,$dataAssetOwnership["docMonth"]);
	}
	if(!in_array($dataAssetOwnership["docYear"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->year,$dataAssetOwnership["docYear"]);
	}
	if(!array_key_exists($dataAssetOwnership["User_ID"],$assetOwnershipOpt->requester)){
		array_push($assetOwnershipOpt->requester,[$dataAssetOwnership["User_ID"]=>$dataAssetOwnership["User_FullName"]]);
	}
}
$landAcquisitionOpt=new stdClass();
$landAcquisitionOpt->company=[];
$landAcquisitionOpt->requester=[];
$landAcquisitionOpt->year=[];
$landAcquisitionOpt->month=[];
$queryLandAcquisition="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(thrgolad.THRGOLAD_RegistrationDate) docMont,YEAR(thrgolad.THRGOLAD_RegistrationDate) docYear
						FROM TH_RegistrationOfLandAcquisitionDocument thrgolad
						LEFT JOIN M_User mu
							ON thrgolad.THRGOLAD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON thrgolad.THRGOLAD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlLandAcquisition = mysql_query($queryLandAcquisition);
while ($dataLandAcquisition = mysql_fetch_array($sqlLandAcquisition)) {
	if(!array_key_exists($dataLandAcquisition["Company_ID"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->company,[$dataLandAcquisition["Company_ID"]=>$dataLandAcquisition["Company_Name"]]);
	}
	if(!in_array($dataLandAcquisition["docMonth"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->month,$dataLandAcquisition["docMonth"]);
	}
	if(!in_array($dataLandAcquisition["docYear"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->year,$dataLandAcquisition["docYear"]);
	}
	if(!array_key_exists($dataLandAcquisition["User_ID"],$landAcquisitionOpt->requester)){
		array_push($landAcquisitionOpt->requester,[$dataLandAcquisition["User_ID"]=>$dataLandAcquisition["User_FullName"]]);
	}
}
$legalOpt=new stdClass();
$legalOpt->type=[];
$legalOpt->company=[];
$legalOpt->requester=[];
$legalOpt->year=[];
$legalOpt->month=[];
$licenseOpt=new stdClass();
$licenseOpt->type=[];
$licenseOpt->company=[];
$licenseOpt->requester=[];
$licenseOpt->year=[];
$licenseOpt->month=[];
$queryLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
					throld.THROLD_DocumentGroupID,mdt.DocumentType_ID,mdt.DocumentType_Name,
					MONTH(throld.THROLD_RegistrationDate) docMont,YEAR(throld.THROLD_RegistrationDate) docYear
				FROM TH_RegistrationOfLegalDocument throld
				LEFT JOIN M_User mu
					ON throld.THROLD_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				LEFT JOIN M_Company mc
					ON throld.THROLD_CompanyID=mc.Company_ID
					AND mc.Company_Delete_Time IS NULL
				LEFT JOIN TD_RegistrationOfLegalDocument tdrold
					ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
					AND tdrold.TDROLD_Delete_Time IS NULL
				LEFT JOIN M_DocumentType mdt
					ON tdrold.TDROLD_DocumentTypeID=mdt.DocumentType_ID
					AND mdt.DocumentType_Delete_Time IS NULL";
$sqlLegal = mysql_query($queryLegal);
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	if($dataLegal["THROLD_DocumentGroupID"]=='1'){
		if(!array_key_exists($dataLegal["DocumentType_ID"],$legalOpt->type)){
			array_push($legalOpt->type,[$dataLegal["DocumentType_ID"]=>$dataLegal["DocumentType_Name"]]);
		}
		if(!array_key_exists($dataLegal["Company_ID"],$legalOpt->company)){
			array_push($legalOpt->company,[$dataLegal["Company_ID"]=>$dataLegal["Company_Name"]]);
		}
		if(!in_array($dataLegal["docMonth"],$legalOpt->company)){
			array_push($legalOpt->month,$dataLegal["docMonth"]);
		}
		if(!in_array($dataLegal["docYear"],$legalOpt->company)){
			array_push($legalOpt->year,$dataLegal["docYear"]);
		}
		if(!array_key_exists($dataLegal["User_ID"],$legalOpt->requester)){
			array_push($legalOpt->requester,[$dataLegal["User_ID"]=>$dataLegal["User_FullName"]]);
		}
	}
	else if($dataLegal["THROLD_DocumentGroupID"]=='2'){
		if(!array_key_exists($dataLegal["DocumentType_ID"],$licenseOpt->type)){
			array_push($licenseOpt->type,[$dataLegal["DocumentType_ID"]=>$dataLegal["DocumentType_Name"]]);
		}
		if(!array_key_exists($dataLegal["Company_ID"],$licenseOpt->company)){
			array_push($licenseOpt->company,[$dataLegal["Company_ID"]=>$dataLegal["Company_Name"]]);
		}
		if(!in_array($dataLegal["docMonth"],$licenseOpt->company)){
			array_push($licenseOpt->month,$dataLegal["docMonth"]);
		}
		if(!in_array($dataLegal["docYear"],$licenseOpt->company)){
			array_push($licenseOpt->year,$dataLegal["docYear"]);
		}
		if(!array_key_exists($dataLegal["User_ID"],$licenseOpt->requester)){
			array_push($licenseOpt->requester,[$dataLegal["User_ID"]=>$dataLegal["User_FullName"]]);
		}
	}
}
$otherLegalOpt=new stdClass();
$otherLegalOpt->company=[];
$otherLegalOpt->requester=[];
$otherLegalOpt->year=[];
$otherLegalOpt->month=[];
$queryOtherLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throold.THROOLD_RegistrationDate) docMonth,YEAR(throold.THROOLD_RegistrationDate) docYear
						FROM TH_RegistrationOfOtherLegalDocuments throold
						LEFT JOIN M_User mu
							ON throold.THROOLD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throold.THROOLD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlOtherLegal = mysql_query($queryOtherLegal);
while ($dataOtherLegal = mysql_fetch_array($sqlOtherLegal)) {
	if(!array_key_exists($dataOtherLegal["Company_ID"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->company,[$dataOtherLegal["Company_ID"]=>$dataOtherLegal["Company_Name"]]);
	}
	if(!in_array($dataOtherLegal["docMonth"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->month,$dataOtherLegal["docMonth"]);
	}
	if(!in_array($dataOtherLegal["docYear"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->year,$dataOtherLegal["docYear"]);
	}
	if(!array_key_exists($dataOtherLegal["User_ID"],$otherLegalOpt->requester)){
		array_push($otherLegalOpt->requester,[$dataOtherLegal["User_ID"]=>$dataOtherLegal["User_FullName"]]);
	}
}
$otherNonLegalOpt=new stdClass();
$otherNonLegalOpt->company=[];
$otherNonLegalOpt->requester=[];
$otherNonLegalOpt->year=[];
$otherNonLegalOpt->month=[];
$queryOtherNonLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throonld.THROONLD_RegistrationDate) docMonth,YEAR(throonld.THROONLD_RegistrationDate) docYear
						FROM TH_RegistrationOfOtherNonLegalDocuments throonld
						LEFT JOIN M_User mu
							ON throonld.THROONLD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throonld.THROONLD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlOtherNonLegal = mysql_query($queryOtherNonLegal);
while ($dataOtherNonLegal = mysql_fetch_array($sqlOtherNonLegal)) {
	if(!array_key_exists($dataOtherNonLegal["Company_ID"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->company,[$dataOtherNonLegal["Company_ID"]=>$dataOtherNonLegal["Company_Name"]]);
	}
	if(!in_array($dataOtherNonLegal["docMonth"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->month,$dataOtherNonLegal["docMonth"]);
	}
	if(!in_array($dataOtherNonLegal["docYear"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->year,$dataOtherNonLegal["docYear"]);
	}
	if(!array_key_exists($dataOtherNonLegal["User_ID"],$otherNonLegalOpt->requester)){
		array_push($otherNonLegalOpt->requester,[$dataOtherNonLegal["User_ID"]=>$dataOtherNonLegal["User_FullName"]]);
	}
}

$allOpt=[];
$allOpt[1]=$legalOpt;
$allOpt[2]=$licenseOpt;
$allOpt[3]=$landAcquisitionOpt;
$allOpt[4]=$assetOwnershipOpt;
$allOpt[5]=$sqlOtherLegal;
$allOpt[6]=$otherNonLegalOpt;
?>
var filterObject = <?=json_encode($allOpt)?>;

</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID'])){
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$ActionContent ="
	<form name='list' method='GET' action='document-list2.php'>
	<table width='100%' id='mytable' class='stripeMe'>
	<tr>
		<th colspan=4>Pencarian Dokumen</th>
	</tr>
	<tr>
		<td>Grup Dokumen</td>
		<td>
			<select name='optTHROLD_DocumentGroupID' id='optTHROLD_DocumentGroupID' onchange='showFilter();'>
				<option value='0'>--- Pilih Grup ---</option>";

			$query = "SELECT *
					  FROM M_DocumentGroup
					  WHERE DocumentGroup_Delete_Time is NULL";
			$sql = mysql_query($query);

			while ($field = mysql_fetch_object($sql) ){

$ActionContent .="
				<option value='".$field->DocumentGroup_ID."'>".$field->DocumentGroup_Name."</option>";
			}
$ActionContent .="
			</select>
		</td>
		<!--<td width='25%'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/><input name='filter' type='submit' value='Filter' class='button-small'/>
		</td>-->
	</tr>
	<tr>
		<td>SEARCH</td>
		<td>
			<input name='txtSearch' type='text'/>
		</td>
	</tr>
	<tr>
		<th colspan='2'>
			<input name='listdocument' type='submit' value='Cari' class='button' onclick='return validateInput(this);'/>
			<input name='filter' type='submit' value='Filter' class='button'/>
			<input name='export_to_excel' type='submit' value='&nbsp;Export to Excel&nbsp;' class='button-blue' />
		</th>
	</tr>";
	if (isset($_GET[filter])) {
$ActionContent .="
	<tr>
		<td>Filter</td>
		<td>:</td>
		<td colspan=4>
			<select name='optFilterHeader' id='optFilterHeader' onchange='showFilterDetail(this.value);'>
				<option value='0'>--- Pilih Grup Dokumen Terlebih Dahulu ---</option>
		</td>
	</tr>
	<tr>
		<td></td><td></td><td>
			<select name='optFilterDetail' id='optFilterDetail' class='filter'>
				<option value='0'>--- Pilih Filter Terlebih Dahulu ---</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td><td></td><td>
			<div id='optPhase' style='display:none;'>
			Tahap GRL : <input type='text'  name='phase' id='phase' size='5'>
			</div>
		</td>
	</tr>
";
	}
$ActionContent .="
	</table>
	</form>
";

/* ====== */
/* ACTION */
/* ====== */

	if(isset($_GET[listdocument])) {


// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];

else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;
	if ($_GET[optTHROLD_DocumentGroupID]<>'3'){
		$query = "SELECT dl.DL_ID, dl.DL_DocCode, c.Company_Name, dc.DocumentCategory_Name,
		dt.DocumentType_Name, dl.DL_PubDate, lds.LDS_Name, dg.DocumentGroup_Name
		FROM
		M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, M_LoanDetailStatus lds,
		M_DocumentGroup dg, M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u
		WHERE
		dl.DL_GroupDocID = '$_GET[optTHROLD_DocumentGroupID]'
		AND dl.DL_GroupDocID = dg.DocumentGroup_ID
		AND dl.DL_CompanyID = c.Company_ID
		AND dl.DL_CategoryDocID = dc.DocumentCategory_ID
		AND dl.DL_TypeDocID = dt.DocumentType_ID
		AND lds.LDS_ID = dl.DL_Status
		AND dl.DL_RegUserID = u.User_ID
		AND dl.DL_Information1 = di1.DocumentInformation1_ID
		AND dl.DL_Information2 = di2.DocumentInformation2_ID
		AND dl.DL_Delete_Time IS NULL ";

		if ($_GET[txtSearch]) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dl.DL_DocCode LIKE '%$search%'
						OR dl.DL_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dl.DL_CategoryDocID LIKE '%$search%'
						OR dc.DocumentCategory_Name LIKE '%$search%'
						OR dl.DL_TypeDocID LIKE '%$search%'
						OR dt.DocumentType_Name LIKE '%$search%'
						OR dl.DL_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dl.DL_Information1 LIKE '%$search%'
						OR di1.DocumentInformation1_Name LIKE '%$search%'
						OR dl.DL_Information2 LIKE '%$search%'
						OR di2.DocumentInformation2_Name LIKE '%$search%'
						OR dl.DL_Information3 LIKE '%$search%'
						OR dl.DL_Instance LIKE '%$search%'
						OR dl.DL_RegTime LIKE '%$search%'
						OR dl.DL_NoDoc LIKE '%$search%'
						OR dl.DL_PubDate LIKE '%$search%'
						OR dl.DL_ExpDate LIKE '%$search%'
					)";
		}
		elseif ($_GET[optFilterHeader]==1) {
			$query .="AND dl.DL_CompanyID='$_GET[optFilterDetail]' ";
		}
		elseif ($_GET[optFilterHeader]==2) {
			$query .="AND dl.DL_CategoryDocID='$_GET[optFilterDetail]' ";
		}
		elseif ($_GET[optFilterHeader]==3) {
			$query .="AND dl.DL_TypeDocID='$_GET[optFilterDetail]' ";
		}
		elseif ($_GET[optFilterHeader]==5) {
			$query .="AND dl.DL_Status='$_GET[optFilterDetail]' ";
		}
		$querylimit .="ORDER BY dl.DL_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_GET[optTHROLD_DocumentGroupID]=='3'){
		$query = "SELECT dla.DLA_ID, c.Company_Name, dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocRevision, lds.LDS_Name,
						 dla.DLA_Code
				  FROM M_DocumentLandAcquisition dla, M_Company c, M_User u,  M_LoanDetailStatus lds
				  WHERE c.Company_ID=dla.DLA_CompanyID
				  AND dla.DLA_Delete_Time IS NULL
				  AND dla.DLA_Status=lds.LDS_ID
				  AND dla.DLA_RegUserID=u.User_ID ";

		if ($_GET[txtSearch]) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dla.DLA_Code LIKE '%$search%'
						OR dla.DLA_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dla.DLA_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_RegTime LIKE '%$search%'
						OR dla.DLA_Phase LIKE '%$search%'
						OR dla.DLA_Period LIKE '%$search%'
						OR dla.DLA_DocDate LIKE '%$search%'
						OR dla.DLA_Block LIKE '%$search%'
						OR dla.DLA_Village LIKE '%$search%'
						OR dla.DLA_Owner LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_AreaClass LIKE '%$search%'
						OR dla.DLA_AreaStatement LIKE '%$search%'
						OR dla.DLA_AreaPrice LIKE '%$search%'
						OR dla.DLA_AreaTotalPrice LIKE '%$search%'
						OR dla.DLA_PlantClass LIKE '%$search%'
						OR dla.DLA_PlantQuantity LIKE '%$search%'
						OR dla.DLA_PlantPrice LIKE '%$search%'
						OR dla.DLA_PlantTotalPrice LIKE '%$search%'
						OR dla.DLA_GrandTotal LIKE '%$search%'
					)";
		}
		elseif ($_GET[optFilterHeader]==1) {
			$query .="AND dla.DLA_CompanyID='$_GET[optFilterDetail]' ";
		}
		elseif ($_GET[optFilterHeader]==5) {
			$query .="AND dla.DLA_Status='$_GET[optFilterDetail]' ";
		}
		elseif ($_GET[phase]<>NULL) {
			$query .="AND dla.DLA_Phase='$_GET[phase]' ";
		}
		$querylimit .="ORDER BY dla.DLA_ID LIMIT $offset, $dataPerPage";
	}

$queryAll=$query.$querylimit;
$sql = mysql_query($queryAll);
$num = mysql_num_rows($sql);
$sqldg = mysql_query($queryAll);
$arr = mysql_fetch_array($sqldg);
echo $queryAll;
	if ($_GET[optTHROLD_DocumentGroupID]<>'3'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori</th>
				<th>Tipe</th>
				<th>Tanggal Terbit</th>
				<th>Status</th>
			</tr>
			<tr>
				<td colspan=7 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' target='_blank' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan=8 align='center'>Daftar Dokumen $arr[DocumentGroup_Name]</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori</th>
				<th>Tipe</th>
				<th>Tanggal Terbit</th>
				<th>Status</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
		$MainContent .="
			<tr>
				<td class='center'>$field[DL_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detail& id=$field[DL_DocCode]' class='underline'>$field[DL_DocCode]</a>
				</td>
				<td class='center'>$field[Company_Name]</td>
				<td class='center'>$field[DocumentCategory_Name]</td>
				<td class='center'>$field[DocumentType_Name]</td>
				<td class='center'>".date('d-m-Y', strtotime($field[DL_PubDate]))."</td>
				<td class='center'>$field[LDS_Name]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

	elseif ($_GET[optTHROLD_DocumentGroupID]=='3'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Revisi</th>
				<th>Status</th>
			</tr>
			<tr>
				<td colspan=8 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' action='print-land-acquisition-document-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan=10 align='center'>Daftar Dokumen Pembebasan Lahan</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Revisi</th>
				<th>Status</th>

			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field[3]);
				$fregdate=date("j M Y", $regdate);
		$MainContent .="
			<tr>
				<td class='center'>$field[DLA_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detailLA&id=$field[0]' class='underline'>$field[DLA_Code]</a></td>
				<td class='center'>$field[1]</td>
				<td class='center'>$field[2]</td>
				<td class='center'>$fregdate</td>
				<td class='center'>$field[4]</td>
				<td class='center'>$field[5]</td>
				<td class='center'><input name='cBarcodePrint[]' type='checkbox' value='$field[0]' /></td>
				<td class='center'><a href='$PHP_SELF?act=editLA&id=$field[0]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a></td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			<center><input name='printbarcode' type='submit' value='Cetak Barcode' class='button' /></center>
			</form>
		";
		}
	}

		$sql1 = mysql_query($query);
		$num1 = mysql_num_rows($sql1);

		$getLink=$_SERVER["REQUEST_URI"];
		$arr = explode("&page=", $getLink);
		$link = $arr[0];

		$jumData = $num1;
		$jumPage = ceil($jumData/$dataPerPage);

		$prev=$noPage-1;
		$next=$noPage+1;

		if ($noPage > 1)
			$Pager.="<a href='$link&page=$prev'>&lt;&lt; Prev</a> ";
		for($p=1; $p<=$jumPage; $p++) {
			if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
				if (($showPage == 1) && ($p != 2))
					$Pager.="...";
				if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
					$Pager.="...";
				if ($p == $noPage)
					$Pager.="<b><u>$p</b></u> ";
				else
					$Pager.="<a href='$link&page=$p'>$p</a> ";

				$showPage = $p;
			}
		}

		if ($noPage < $jumPage)
			$Pager .= "<a href='$link&page=$next'>Next &gt;&gt;</a> ";
	}

/* ================================ */
/* MELIHAT DETAIL DARI LIST DOKUMEN */
/* ================================ */


if($_GET["act"]){
	$act=$_GET["act"];

	$ActionContent =" ";
		// Cek apakah Staff Custodian atau bukan.
		// Staff Custodian memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
		  	FROM M_DivisionDepartmentPosition ddp, M_Department d
			WHERE ddp.DDP_DeptID=d.Department_ID
			AND ddp.DDP_UserID='$_SESSION[User_ID]'
			AND d.Department_Name LIKE '%Custodian%'";
		$sql = mysql_query($query);
		$custodian = mysql_num_rows($sql);

		// Cek apakah Administrator atau bukan.
		// Administrator memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
				  FROM M_UserRole
				  WHERE MUR_RoleID='1'
				  AND MUR_UserID='$_SESSION[User_ID]'
				  AND MUR_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$admin = mysql_num_rows($sql);


	//Melihat Detail Dokumen Legal, License, Others
	if(($act=='detail') || ($act=='edit') ){
		$id=$_GET["id"];
		$query = "SELECT dl.DL_DocCode,
						 u.User_FullName,
						 dl.DL_RegTime,
						 c.Company_Name,
						 c.Company_Code,
						 dc.DocumentCategory_ID,
						 dc.DocumentCategory_Name,
						 dt.DocumentType_ID,
						 dt.DocumentType_Name,
						 dl.DL_NoDoc,
						 dl.DL_PubDate,
						 dl.DL_ExpDate,
						 di1.DocumentInformation1_ID,
						 di1.DocumentInformation1_Name,
						 di2.DocumentInformation2_ID,
						 di2.DocumentInformation2_Name,
						 dl.DL_Information3,
						 dl.DL_Instance,
						 dl.DL_Location,
						 dl.DL_Softcopy,
						 lds.LDS_Name,
						 dg.DocumentGroup_Name,
						 dg.DocumentGroup_Code,
						 dg.DocumentGroup_ID
		  	FROM M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, M_LoanDetailStatus lds,
				 M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u, M_DocumentGroup dg
			WHERE dl.DL_DocCode='$id'
			AND dl.DL_GroupDocID=dg.DocumentGroup_ID
			AND dl.DL_CompanyID=c.Company_ID
			AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
			AND dl.DL_TypeDocID=dt.DocumentType_ID
			AND dl.DL_Status=lds.LDS_ID
			AND dl.DL_RegUserID=u.User_ID
			AND dl.DL_Information1=di1.DocumentInformation1_ID
			AND dl.DL_Information2=di2.DocumentInformation2_ID";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detail') {
		$regdate=strtotime($arr['DL_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$pubdate=strtotime($arr['DL_PubDate']);
		$fpubdate=date("j M Y", $pubdate);
		if ($arr['DL_ExpDate']=="0000-00-00 00:00:00"){
			$fexpdate="-";
		}
		else {
		$expdate=strtotime($arr['DL_ExpDate']);
		$fexpdate=date("j M Y", $expdate);
		}

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen $arr[DocumentGroup_Name]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DL_DocCode' value='$arr[DL_DocCode]'>$arr[DL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Grup Dokumen</td>
		<td width='70%'><input type='hidden' name='DocumentGroup_Code' value='$arr[DocumentGroup_Code]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Kategori Dokumen</td>
		<td width='70%'>$arr[DocumentCategory_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tipe Dokumen</td>
		<td width='70%'>$arr[DocumentType_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Nomor Dokumen</td>
		<td width='70%'>$arr[DL_NoDoc]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Publikasi</td>
		<td width='70%'>$fpubdate</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Habis Masa Berlaku</td>
		<td width='70%'>$fexpdate</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 1</td>
		<td width='70%'>$arr[DocumentInformation1_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 2</td>
		<td width='70%'>$arr[DocumentInformation2_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 3</td>
		<td width='70%'>$arr[DL_Information3]</td>
	</tr>
	<tr>
		<td width='30%'>Instansi Terkait</td>
		<td width='70%'>$arr[DL_Instance]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi DoKumen</td>
		<td width='70%'>$arr[DL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DL_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
$MainContent .="
	</table>
";
	}

	if(($act=='edit') && (($custodian==1)||($admin=="1"))){
		$regdate=strtotime($arr['DL_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$pubdate=strtotime($arr['DL_PubDate']);
		$fpubdate=date("m/d/Y", $pubdate);
		if ($arr['DL_ExpDate']=="0000-00-00 00:00:00"){
			$fexpdate="";
		}
		else {
		$expdate=strtotime($arr['DL_ExpDate']);
		$fexpdate=date("m/d/Y", $expdate);
		}

$MainContent ="
	<form enctype='multipart/form-data' action='' method='POST'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen $arr[DocumentGroup_Name]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DL_DocCode' value='$arr[DL_DocCode]'>$arr[DL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Grup Dokumen</td>
		<td width='70%'><input type='hidden' name='DocumentGroup_Code' value='$arr[DocumentGroup_Code]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Kategori Dokumen</td>
		<td width='70%'>

				<select name='txtDL_CategoryDocID' id='txtDL_CategoryDocID' onchange='showType(this.value);'>
					<option value='0'>--- Pilih Kategori Dokumen ---</option>";
			$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
					 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
					 WHERE dgct.DGCT_DocumentGroupID='$arr[DocumentGroup_ID]'
					 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
					 AND dgct.DGCT_Delete_Time is NULL";
			$sql5 = mysql_query($query5);

			while ($field5=mysql_fetch_array($sql5)) {
				if ($field5["DocumentCategory_ID"]=="$arr[DocumentCategory_ID]"){
$MainContent .="
				<option value='$field5[DocumentCategory_ID]' selected='selected'>$field5[DocumentCategory_Name]</option>";
				}
				else{
$MainContent .="
				<option value='$field5[DocumentCategory_ID]'>$field5[DocumentCategory_Name]</option>";
				}
			}
$MainContent .="
			</select>
		</td>
	<tr>
		<td width='30%'>Tipe Dokumen</td>
		<td>
			<select name='txtDL_TypeDocID' id='txtDL_TypeDocID'>
					<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>";
			$query6="SELECT DISTINCT dt.DocumentType_ID,dt.DocumentType_Name
					 FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
					 WHERE dgct.DGCT_DocumentGroupID='$arr[DocumentGroup_ID]'
					 AND dgct.DGCT_DocumentCategoryID='$arr[DocumentCategory_ID]'
					 AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
					 AND dgct.DGCT_Delete_Time is NULL";
			$sql6 = mysql_query($query6);

			while ($field6=mysql_fetch_array($sql6)) {
				if ($field6["DocumentType_ID"]==$arr['DocumentType_ID']){
$MainContent .="
				<option value='$field6[DocumentType_ID]' selected='selected'>$field6[DocumentType_Name]</option>";
				}
				else{
$MainContent .="
				<option value='$field6[DocumentType_ID]'>$field6[DocumentType_Name]</option>";
				}
			}
$MainContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td width='30%'>Instansi Terkait</td>
		<td width='70%'><input name='txtDL_Instance' id='txtDL_Instance' type='text' value='$arr[DL_Instance]'></td>
	</tr>
	<tr>
		<td width='30%'>Nomor Dokumen</td>
		<td width='70%'><input type='text' name='txtDL_NoDoc' id='txtDL_NoDoc' value='$arr[DL_NoDoc]'></td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Publikasi</td>
		<td width='70%'>
			<input type='text' name='txtDL_RegDate' id='txtDL_RegDate' size='7' value='$fpubdate' onclick=\"javascript:NewCssCal('txtDL_RegDate', 'MMddyyyy');\">
		</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Habis Masa Berlaku</td>
		<td width='70%'>
			<input type='text' name='txtDL_ExpDate' id='txtDL_ExpDate' size='7' value='$fexpdate' onclick=\"javascript:NewCssCal('txtDL_ExpDate', 'MMddyyyy');\"><img src='images/icon_close.gif' onclick=\"document.getElementById('txtDL_ExpDate').value=''\" style='margin-left:5px'>
		</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 1</td>
		<td width='70%'>
				<select name='txtDL_Information1' id='txtDL_Information1'>
					<option value='0'>--- Pilih Keterangan Dokumen 1 ---</option>";
                 $query1 = "SELECT *
				 				FROM M_DocumentInformation1
								WHERE DocumentInformation1_Delete_Time is NULL
								ORDER BY DocumentInformation1_ID";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
					 if ($data[0]==$arr[DocumentInformation1_ID]){
$MainContent .="
					<option value='$data[0]' selected='selected'>$data[1]</option>";
					 }
					 else {
$MainContent .="
					<option value='$data[0]'>$data[1]</option>";
					 }
                 }
$MainContent .="
				</select>
		</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 2</td>
		<td width='70%'>
				<select name='txtDL_Information2' id='txtDL_Information2'>
					<option value='0'>--- Pilih Keterangan Dokumen 2 ---</option>";
                 $query1 = "SELECT *
				 				FROM M_DocumentInformation2
								WHERE DocumentInformation2_Delete_Time is NULL
								ORDER BY DocumentInformation2_ID";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
					 if ($data[0]==$arr[DocumentInformation2_ID]){
$MainContent .="
					<option value='$data[0]' selected='selected'>$data[1]</option>";
					 }
					 else {
$MainContent .="
					<option value='$data[0]'>$data[1]</option>";
					 }
                 }
$MainContent .="
				</select>
		<td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 3</td>
		<td width='70%'><textarea name='txtDL_Information3' id='txtDL_Information3' cols='50' rows='2'>$arr[DL_Information3]</textarea></td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
		if ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']==NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Upload Softcopy Dokumen</td>
		<td width='70%'>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='upload' class='button-small' />
		</td>
	</tr>";
		}

		elseif ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DL_Softcopy]' class='underline'>[Download Softcopy]</a><br>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='upload' class='button-small' />
		</td>
	</tr>";
		}

$MainContent .="
	<th colspan='2'>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInputEdit(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	//Melihat Detail Dokumen Pembebasan Lahan
	if(($act=='detailLA') || ($act=='editLA') ){
		$id=$_GET["id"];
		$query = "SELECT c.Company_Name,
						 c.Company_Code,
						 dla.DLA_Phase,
						 dla.DLA_Village,
						 dla.DLA_Owner,
						 dla.DLA_Block,
						 dla.DLA_AreaClass,
						 dla.DLA_AreaStatement,
					     dla.DLA_AreaPrice,
					     dla.DLA_AreaTotalPrice,
					     dla.DLA_PlantClass,
					     dla.DLA_PlantQuantity,
					     dla.DLA_PlantPrice,
					     dla.DLA_PlantTotalPrice,
					     dla.DLA_GrandTotal,
					     dla.DLA_Location,
						 dla.DLA_Period,
						 dla.DLA_DocRevision,
						 dla.DLA_Information,
						 u.User_FullName,
						 dla.DLA_RegTime,
						 dla.DLA_Softcopy,
						 dla.DLA_DocDate,
						 dla.DLA_ID,
						 dla.DLA_Code,
						 lds.LDS_Name
		  	FROM M_DocumentLandAcquisition dla, M_Company c, M_User u, M_LoanDetailStatus lds
			WHERE dla.DLA_ID='$id'
			AND dla.DLA_Delete_Time IS NULL
			AND dla.DLA_CompanyID=c.Company_ID
			AND dla.DLA_RegUserID=u.User_ID
			AND lds.LDS_ID=dla.DLA_Status";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detailLA') {
		$regdate=strtotime($arr['DLA_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$perdate=strtotime($arr['DLA_Period']);
		$fperdate=date("j M Y", $perdate);
		$docdate=strtotime($arr['DLA_DocDate']);
		$fdocdate=date("j M Y", $docdate);
		$TotalArea=number_format($arr[DLA_AreaTotalPrice],2,'.',',');
		$TotalPlant=number_format($arr[DLA_PlantTotalPrice],2,'.',',');
		$TotalPrice=number_format($arr[DLA_GrandTotal],2,'.',',');
		$AreaPrice=number_format($arr[DLA_AreaPrice],2,'.',',');
		$PlantPrice=number_format($arr[DLA_PlantPrice],2,'.',',');
		$DLA_PlantQuantity=number_format($arr[DLA_PlantQuantity],2,'.',',');
		$DLA_AreaStatement=number_format($arr[DLA_AreaStatement],2,'.',',');

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Dokumen Pembebasan Lahan<br>Revisi $arr[DLA_DocRevision]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'>$arr[DLA_Code]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tahap</td>
		<td width='70%'>$arr[DLA_Phase]</td>
	</tr>
	<tr>
		<td width='30%'>Desa</td>
		<td width='70%'>$arr[DLA_Village]</td>
	</tr>
	<tr>
		<td width='30%'>Blok</td>
		<td width='70%'>$arr[DLA_Block]</td>
	</tr>
	<tr>
		<td width='30%'>Pemilik</td>
		<td width='70%'>$arr[DLA_Owner]</td>
	</tr>
	<tr>
		<td width='30%'>Periode</td>
		<td width='70%'>$fperdate</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Dokumen</td>
		<td width='70%'>$fdocdate</td>
	</tr>
	<tr>
		<td width='30%'>Pembebasan Lahan</td>
		<td width='70%'>Kelas $arr[DLA_AreaClass] : $DLA_AreaStatement * Rp $AreaPrice = Rp $TotalArea</td>
	</tr>
	<tr>
		<td width='30%'>Tanam Tumbuh</td>
		<td width='70%'>Kelas $arr[DLA_PlantClass] : $DLA_PlantQuantity * Rp $PlantPrice = Rp $TotalPlant</td>
	</tr>
	<tr>
		<td width='30%'>Total</td>
		<td width='70%'>Rp $TotalPrice</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan</td>
		<td width='70%'>$arr[DLA_Information]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DLA_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DLA_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
$MainContent .="
	</table>

	<table width='100%' class='stripeMe'>
	<tr>
		<th colspan='4'>Detail Dokumen</th>
	</tr>
	<tr>
		<th width='20%'>Kode Dokumen</th>
		<th width='70%'>Jenis Atribut</th>
		<th width='10%'>Keterangan</th>
	</tr>";
	$dDoc_query="SELECT laa.LAA_ID, laa.LAA_Name, laas.LAAS_Name, dlaa.DLAA_DocCode
				 FROM M_DocumentLandAcquisitionAttribute dlaa, M_LandAcquisitionAttribute laa,
				 	  M_LandAcquisitionAttributeStatus laas
				 WHERE dlaa.DLAA_DLA_ID='$id'
				 AND dlaa.DLAA_LAA_ID=laa.LAA_ID
				 AND dlaa.DLAA_LAAS_ID=laas.LAAS_ID
				 AND dlaa.DLAA_Delete_Time IS NULL
				 ORDER BY laa.LAA_ID";
	$dDoc_sql=mysql_query($dDoc_query);
	while ($dDoc_arr=mysql_fetch_array($dDoc_sql)){
$MainContent .="
	<tr>
		<td align='center'>$dDoc_arr[DLAA_DocCode]</td>
		<td>$dDoc_arr[LAA_Name]</td>
		<td align='center'>$dDoc_arr[LAAS_Name]</td>
	</tr>
";
	}

$MainContent .="
	</table>
";
	}

	if(($act=='editLA') && (($custodian==1)||($admin=="1"))){
		$regdate=strtotime($arr['DLA_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$perdate=strtotime($arr['DLA_Period']);
		$fperdate=date("m/d/Y", $perdate);
		$docdate=strtotime($arr['DLA_DocDate']);
		$fdocdate=date("m/d/Y", $docdate);

$MainContent ="
	<form enctype='multipart/form-data' action='$PHP_SELF' method='POST'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Dokumen Pembebasan Lahan<br>Revisi <input type='hidden' name='txtDLA_DocRevision' value='$arr[DLA_DocRevision]'>$arr[DLA_DocRevision]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'>$arr[DLA_Code]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'><input type='hidden' name='txtDLA_ID' value='$arr[DLA_ID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DLA_RegTime' value='$fregdate'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tahap</td>
		<td width='70%'><input type='text' name='txtDLA_Phase' id='txtDLA_Phase' value='$arr[DLA_Phase]'></td>
	</tr>
	<tr>
		<td width='30%'>Desa</td>
		<td width='70%'><input type='text' name='txtDLA_Village' id='txtDLA_Village' value='$arr[DLA_Village]'></td>
	</tr>
	<tr>
		<td width='30%'>Blok</td>
		<td width='70%'><input type='text' name='txtDLA_Block' id='txtDLA_Block' value='$arr[DLA_Block]'></td>
	</tr>
	<tr>
		<td width='30%'>Pemilik</td>
		<td width='70%'><input type='text' name='txtDLA_Owner' id='txtDLA_Owner' value='$arr[DLA_Owner]'></td>
	</tr>
	<tr>
		<td width='30%'>Periode</td>
		<td width='70%'><input type='text' name='txtDLA_Period' id='txtDLA_Period' value='$fperdate' size='7' onclick=\"javascript:NewCssCal('txtDLA_Period', 'MMddyyyy');\"></td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Dokumen</td>
		<td width='70%'><input type='text' name='txtDLA_DocDate' id='txtDLA_DocDate' value='$fdocdate' size='7' onclick=\"javascript:NewCssCal('txtDLA_DocDate', 'MMddyyyy');\"></td>
	</tr>
	<tr>
		<td width='30%'>Kelas Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaClass' id='txtDLA_AreaClass' value='$arr[DLA_AreaClass]' size='3'></td>
	</tr>
	<tr>
		<td width='30%'>Luas Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaStatement' id='txtDLA_AreaStatement' value='$arr[DLA_AreaStatement]' size='3' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Harga Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaPrice' id='txtDLA_AreaPrice' value='$arr[DLA_AreaPrice]' size='10' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Total Harga Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaTotalPrice' id='txtDLA_AreaTotalPrice' value='$arr[DLA_AreaTotalPrice]' size='10' onchange='countTotal();' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Kelas Tanam Tumbuh</td>
		<td width='70%'><input type='text' name='txtDLA_PlantClass' id='txtDLA_PlantClass' value='$arr[DLA_PlantClass]' size='3'></td>
	</tr>
	<tr>
		<td width='30%'>Jumlah Tumbuhan</td>
		<td width='70%'><input type='text' name='txtDLA_PlantQuantity' id='txtDLA_PlantQuantity' value='$arr[DLA_PlantQuantity]' size='3' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Harga Tumbuhan</td>
		<td width='70%'><input type='text' name='txtDLA_PlantPrice' id='txtDLA_PlantPrice' value='$arr[DLA_PlantPrice]' size='10' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Total Tanam Tumbuh</td>
		<td width='70%'><input type='text' name='txtDLA_PlantTotalPrice' id='txtDLA_PlantTotalPrice' value='$arr[DLA_PlantTotalPrice]' size='10' onchange='countTotal();' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Total</td>
		<td width='70%'><input type='text' name='txtDLA_GrandTotal' id='txtDLA_GrandTotal' value='$arr[DLA_GrandTotal]' size='10' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Keterangan</td>
		<td width='70%'><textarea name='txtDLA_Information' id='txtDLA_Information' cols='50' rows='2'>$arr[DLA_Information]</textarea></td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'><input type='hidden' name='txtDLA_Location' value='$arr[DLA_Location]'>$arr[DLA_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
		if ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']==NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Upload Softcopy Dokumen</td>
		<td width='70%'>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='uploadLA' class='button-small' />
		</td>
	</tr>";
		}

		elseif ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DLA_Softcopy]' class='underline'>[Download Softcopy]</a> <br>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='uploadLA' class='button-small' />
		</td>
	</tr>";
		}
$MainContent .="
	</table>
	<table width='100%' class='stripeMe'>
	<tr>
		<th colspan='5'>Detail Dokumen</th>
	</tr>
	<tr>
		<th width='20%'>Kode Dokumen</th>
		<th width='70%'>Jenis Atribut</th>
		<th width='10%'>Keterangan</th>
	</tr>";
	$dDoc_query="SELECT laa.LAA_ID, laa.LAA_Name, laas.LAAS_Name, laas.LAAS_ID, dlaa.DLAA_DocCode
				 FROM M_DocumentLandAcquisitionAttribute dlaa, M_LandAcquisitionAttribute laa,
				 	  M_LandAcquisitionAttributeStatus laas
				 WHERE dlaa.DLAA_DLA_ID='$id'
				 AND dlaa.DLAA_LAA_ID=laa.LAA_ID
				 AND dlaa.DLAA_LAAS_ID=laas.LAAS_ID
				 AND dlaa.DLAA_Delete_Time IS NULL
				 ORDER BY laa.LAA_ID";
	$dDoc_sql=mysql_query($dDoc_query);
	while ($dDoc_arr=mysql_fetch_array($dDoc_sql)){
$MainContent .="
	<tr>
		<td align='center'>$dDoc_arr[DLAA_DocCode]</td>
		<td><input type='hidden' name='txtLAA_ID[]' id='txtLAA_ID[]' value='$dDoc_arr[LAA_ID]'>$dDoc_arr[LAA_Name]</td>
		<td align='center'>
			<select name='optLAAS_ID[]' id='optLAAS_ID[]'>";
		$s_query="SELECT *
				 FROM M_LandAcquisitionAttributeStatus
				 WHERE LAAS_Delete_Time IS NULL";
		$s_sql=mysql_query($s_query);
		while ($s_arr=mysql_fetch_array($s_sql)) {
			if ($s_arr[LAAS_ID]==$dDoc_arr[LAAS_ID]) {
$MainContent .="
				<option value='$s_arr[LAAS_ID]' selected='selected'>$s_arr[LAAS_Name]</option>";
			}
			else {
$MainContent .="
				<option value='$s_arr[LAAS_ID]'>$s_arr[LAAS_Name]</option>";
			}
		}
$MainContent .="
			</select>
		</td>
	</tr>
";
	}

$MainContent .="
	<th colspan='5'>
		<input name='editLA' type='submit' value='Simpan' class='button' onclick='return validateInputEditLA(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}


/* ====== */
/* ACTION */
/* ====== */
//print_r($_GET);die();
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";

}
else if($_POST['edit']) {
	$txtRegDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_RegDate']));
	$txtExpDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_ExpDate']));
	if 	($txtExpDate=="1970-01-01 08:00:00"){
		$txtExpDate="";
	}

	$query = "UPDATE M_DocumentLegal
			  SET DL_CategoryDocID='$_POST[txtDL_CategoryDocID]',
			  	  DL_TypeDocID='$_POST[txtDL_TypeDocID]',
			  	  DL_NoDoc='$_POST[txtDL_NoDoc]',
			  	  DL_PubDate='$txtRegDate',
				  DL_ExpDate='$txtExpDate',
				  DL_Information1='$_POST[txtDL_Information1]',
				  DL_Information2='$_POST[txtDL_Information2]',
				  DL_Information3='$_POST[txtDL_Information3]',
				  DL_Instance='$_POST[txtDL_Instance]',
			  	  DL_Update_Time=sysdate(),
			      DL_Update_UserID='$_SESSION[User_ID]'
			  WHERE DL_DocCode='$_POST[DL_DocCode]'";
	if ($mysqli->query($query))
		echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";
}
else if($_POST['editLA']) {
	$txtDLA_Period=date('Y-m-d H:i:s', strtotime($_POST['txtDLA_Period']));
	$txtDLA_DocDate=date('Y-m-d H:i:s', strtotime($_POST['txtDLA_DocDate']));
	$DLA_DocRevision=$_POST['txtDLA_DocRevision'];
	$DLA_DocRevision=$DLA_DocRevision+1;
	$txtLAA_ID=$_POST['txtLAA_ID'];
	$optLAAS_ID=$_POST['optLAAS_ID'];
	$jRow=count($txtLAA_ID);

	$query = "UPDATE M_DocumentLandAcquisition
			  SET DLA_Phase='$_POST[txtDLA_Phase]',
			  	  DLA_Village='$_POST[txtDLA_Village]',
			  	  DLA_Block='$_POST[txtDLA_Block]',
			  	  DLA_Period='$txtDLA_Period',
				  DLA_DocDate='$txtDLA_DocDate',
				  DLA_Owner='$_POST[txtDLA_Owner]',
				  DLA_AreaClass='$_POST[txtDLA_AreaClass]',
				  DLA_AreaStatement='$_POST[txtDLA_AreaStatement]',
				  DLA_AreaPrice='$_POST[txtDLA_AreaPrice]',
				  DLA_AreaTotalPrice='$_POST[txtDLA_AreaTotalPrice]',
				  DLA_PlantClass='$_POST[txtDLA_PlantClass]',
				  DLA_PlantQuantity='$_POST[txtDLA_PlantQuantity]',
				  DLA_PlantPrice='$_POST[txtDLA_PlantPrice]',
				  DLA_PlantTotalPrice='$_POST[txtDLA_PlantTotalPrice]',
				  DLA_GrandTotal='$_POST[txtDLA_GrandTotal]',
				  DLA_Information='$_POST[txtDLA_Information]',
			  	  DLA_Update_Time=sysdate(),
			      DLA_Update_UserID='$_SESSION[User_ID]'
			  WHERE DLA_ID='$_POST[txtDLA_ID]'";
	if ($mysqli->query($query)) {
		for ($i=0; $i<$jRow; $i++) {
			$d_query="UPDATE M_DocumentLandAcquisitionAttribute
					  SET DLAA_LAAS_ID='$optLAAS_ID[$i]',
						  DLAA_Update_Time=sysdate(),
						  DLAA_Update_UserID='$_SESSION[User_ID]'
					  WHERE DLAA_DLA_ID='$_POST[txtDLA_ID]'
					  AND DLAA_LAA_ID='$txtLAA_ID[$i]'
					  AND DLAA_Delete_Time IS NULL";
			if ($mysqli->query($d_query)) {
				echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";
			}
		}
	}
}
else if($_POST['upload']){
	$DL_DocCode=$_POST[DL_DocCode];
	$Company_Name=$_POST[Company_Name];
	$DocumentGroup_Code=$_POST[DocumentGroup_Code];
	$regdate=strtotime($_POST['DL_RegTime']);
	$DL_RegTime=date("Y", $regdate);

	$uploaddir = "SOFTCOPY/$Company_Name/$DocumentGroup_Code/$DL_RegTime/";
	if ( ! is_dir($uploaddir)) {
		$oldumask = umask(0);
		mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
		chmod("/$Company_Name", 0777);
		chmod("/$DocumentGroup_Code", 0777);
		chmod("/$DL_RegTime", 0777);
		umask($oldumask);
	}
	$uploadFile = $_FILES['userfile'];
	$extractFile = pathinfo($uploadFile['name']);

	$newName = $DL_DocCode.'.'.$extractFile['extension'];
	$sameName = 0;
	if ($handle = opendir("$uploaddir")) {
		while (false !== ($file = readdir($handle))) {
			if ($file==$newName) {
				if(strpos($newName,$DL_DocCode) !== false)  {
					$sameName++; // Tambah data file yang sama
					$newName = $DL_DocCode.'('.$sameName.')'.'.'.$extractFile['extension'];
				}
			}
		}
		closedir($handle);
	}

	if ($DocumentGroup_Code<>'GRL') {
		$query="UPDATE M_DocumentLegal
				SET DL_Softcopy='$uploaddir$newName',
					DL_Update_UserID='$_SESSION[User_ID]',
					DL_Update_Time=sysdate()
				WHERE DL_DocCode='$DL_DocCode'";
	}

	if ($mysqli->query($query)){
		if(move_uploaded_file($uploadFile['tmp_name'],$uploaddir.$newName)){
			echo "<meta http-equiv='refresh' content='0; url=document-list2.php?act=edit&id=$DL_DocCode'>";
		}
	}
}
else if($_POST['uploadLA']){
	$DLA_ID=$_POST[txtDLA_ID];
	$DLA_Location=$_POST[txtDLA_Location];
	$Company_Name=$_POST[Company_Name];
	$DocumentGroup_Code='GRL';
	$regdate=strtotime($_POST['DLA_RegTime']);
	$RegTime=date("Y", $regdate);

	$uploaddir = "SOFTCOPY/$Company_Name/$DocumentGroup_Code/$RegTime/";
	if ( ! is_dir($uploaddir)) {
		$oldumask = umask(0);
		mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
		chmod("/$Company_Name", 0777);
		chmod("/$DocumentGroup_Code", 0777);
		chmod("/$RegTime", 0777);
		umask($oldumask);
	}
	$uploadFile = $_FILES['userfile'];
	$extractFile = pathinfo($uploadFile['name']);

	$newName = $DLA_Location.'.'.$extractFile['extension'];
	$sameName = 0;
	if ($handle = opendir("$uploaddir")) {
		while (false !== ($file = readdir($handle))) {
			if ($file==$newName) {
				if(strpos($newName,$DLA_Location) !== false)  {
					$sameName++; // Tambah data file yang sama
					$newName = $DLA_Location.'('.$sameName.')'.'.'.$extractFile['extension'];
				}
			}
		}
		closedir($handle);
	}

	if ($DocumentGroup_Code=='GRL') {
		$query="UPDATE M_DocumentLandAcquisition
				SET DLA_Softcopy='$uploaddir$newName',
					DLA_Update_UserID='$_SESSION[User_ID]',
					DLA_Update_Time=sysdate()
				WHERE DLA_ID='$DLA_ID'";
	}

	if ($mysqli->query($query)){
		if(move_uploaded_file($uploadFile['tmp_name'],$uploaddir.$newName)){
			echo "<meta http-equiv='refresh' content='0; url=document-list2.php?act=editLA&id=$DLA_ID'>";
		}
	}
}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
