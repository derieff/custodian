<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 4 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
= 		23/05/2012	: Validasi keterangan. (OK)																			=
=					  Letak DatePicker di bagian detail, default LeadTime adl 7 hari (OK)								=
=					  LeadTime hanya untuk dokumen yang dipinjam														=
=					  Button "Cancel" untuk detail transaksi (OK)														=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Pengeluaran Dokumen</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldoc.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//LoV UTK DAFTAR PERMINTAAN DOKUMEN
function showList() {
	// var docGrup="non_grl";
	var docGrup="1"; // Arief F - 14082018
	sList = window.open("popupLoan.php?gID="+docGrup+"", "Daftar_Permintaan_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
function remLink() {
  if (window.sList && window.sList.open && !window.sList.closed)
	window.sList.opener = null;
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;
	checkDocCode = 0;

	var txtTHROLD_THLOLD_Code = document.getElementById('txtTHROLD_THLOLD_Code').value;
	var txtTHROLD_UserID = document.getElementById('txtTHROLD_UserID').value;

		if (txtTHROLD_THLOLD_Code.replace(" ", "") == "") {
			alert("Kode Permintaan Dokumen Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT *
					  FROM TH_LoanOfLegalDocument thlold,TD_LoanOfLegalDocument tdlold
					  WHERE thlold.THLOLD_Delete_Time IS NULL
					  AND thlold.THLOLD_Status='accept'
					  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
					  AND tdlold.TDLOLD_Response='0'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$THLOLD_LoanCode = $data['THLOLD_LoanCode'];

				$a = "if (txtTHROLD_THLOLD_Code == '$THLOLD_LoanCode') {";
				$a .= "checkDocCode = 1; ";
				$a .= "}";
			echo $a;
		 	}
			?>
			if (checkDocCode == 0) {
				alert("Kode Permintaan Dokumen SALAH!");
				returnValue = false;
			}
		}

	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PEMINJAMAN DOKUMEN
function validateInputDetail(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOLD_ID = document.getElementsByName('TDLOLD_ID[]');
	var DL_DocCode = document.getElementsByName('DL_DocCode[]');
	var txtTDROLD_LeadTime = document.getElementsByName('txtTDROLD_LeadTime[]');

	for (var i = 0; i < TDLOLD_ID.length; i++){
		if (TDLOLD_ID[i].checked) {
			if (txtTDROLD_LeadTime[i].value.replace(/^\s+|\s+$/g,'') == "") {
				returnValue = false;
				notcheck = false;
				alert("Tanggal Pengembalian Untuk Dokumen "+DL_DocCode[i].value+" Belum Ditentukan!");
			}
			else {
				returnValue = true;
				notcheck = false;
			}
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PERMINTAAN DOKUMEN
function validateInputDetailx(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOLD_ID = document.getElementsByName('TDLOLD_ID[]');
	var DL_DocCode = document.getElementsByName('DL_DocCode[]');
	var txtTDROLD_LeadTime = document.getElementsByName('txtTDROLD_LeadTime[]');

	for (var i = 0; i < TDLOLD_ID.length; i++){
		if (TDLOLD_ID[i].checked) {
			returnValue = true;
			notcheck = false;
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='addRelDoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen</th>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2
				  FROM M_User u
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input id='txtTHROLD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input id='txtTHROLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input id='txtTHROLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input id='txtTHROLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Kode Permintaan Dokumen</td>
				<td>
					<input id='txtTHROLD_THLOLD_Code' name='txtTHROLD_THLOLD_Code' size='25' type='text' value='' readonly='readonly' onClick='javascript:showList();'/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHROLD_Information' id='txtTHROLD_Information' cols='50' rows='2'></textarea></td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
					<input name='cancel' type='submit' value='Batal' class='button'/>
				</th>
			</tr>";
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}
			$ActionContent .="
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
		$ActionContent .="
		</table>
		</form>";
	}

	//Menambah Detail Dokumen
	elseif($act=='adddetail')	{
		$code=$_GET["id"];

		$query = "SELECT releaseHeader.THROLD_Information,
						 releaseHeader.THROLD_ReleaseDate,
						 releaseHeader.THROLD_ID,
						 releaseHeader.THROLD_ReleaseCode,
						 u.User_FullName as FullName,
						 ddp.DDP_DeptID as DeptID,
						 ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID,
						 dp.Department_Name as DeptName,
						 d.Division_Name as DivName,
						 p.Position_Name as PosName,
						 u.User_SPV1,
						 u.User_SPV2,
						 c.Company_Name,
						 dg.DocumentGroup_Name,
						 thlold.THLOLD_DocumentType tipe_dokumen,
						 thlold.THLOLD_LoanCategoryID kategori_permintaan
				  FROM TH_ReleaseOfLegalDocument releaseHeader
				  LEFT JOIN M_User u
					ON u.User_ID=releaseHeader.THROLD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN TH_LoanOfLegalDocument thlold
					ON releaseHeader.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
					AND thlold.THLOLD_Delete_Time IS NULL
				  LEFT JOIN M_Company c
					ON thlold.THLOLD_CompanyID=c.Company_ID
				  LEFT JOIN M_DocumentGroup dg
					ON thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  WHERE releaseHeader.THROLD_ReleaseCode='$code'
				  AND releaseHeader.THROLD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$fregdate=date("j M Y", strtotime($field['THROLD_ReleaseDate']));
		// $atasan=($field['User_SPV2'])?$field['User_SPV2']:$field['User_SPV1'];

		$jenis = "22"; //Semua Dokumen

		$queryApprover = "
			SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
			FROM M_Role_ApproverDocStepStatus rads
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			LEFT JOIN M_Approver ma
				ON ra.RA_ID = ma.Approver_RoleID
			WHERE rads.RADS_DocID = '$jenis'
				AND rads.RADS_ProsesID = '3'
				AND ma.Approver_Delete_Time IS NULL
				AND ma.Approver_UserID != '0'
				ORDER BY rads.RADS_StepID
		";
		$sqlApprover=mysql_query($queryApprover);
		while($d = mysql_fetch_array($sqlApprover)){
			$approvers[] = $d['Approver_UserID'];  //Approval Untuk ke Custodian
		}

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen</th>
		<tr>
			<td width='30%'>Kode Pengeluaran</td>
			<td width='70%'>
				<input name='txtTDROLD_THROLD_ID' type='hidden' value='$field[THROLD_ID]'/>
				<input type='hidden' name='txtTDROLD_THROLD_ReleaseCode' value='$field[THROLD_ReleaseCode]'/>
				$field[THROLD_ReleaseCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Pendaftaran</td>
			<td>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>$field[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[PosName]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>$field[Company_Name]</td>
		</tr>
		<tr>
			<td>Grup</td>
			<td>$field[DocumentGroup_Name]</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROLD_Information' id='txtTHROLD_Information' cols='50' rows='2'>$field[THROLD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		$query="SELECT tdlold.TDLOLD_ID, tdlold.TDLOLD_Code, dt.DocumentType_Name, dl.DL_NoDoc, dl.DL_DocCode,
					   thlold.THLOLD_LoanCategoryID
				FROM TD_LoanOfLegalDocument tdlold, TH_LoanOfLegalDocument thlold, TH_ReleaseOfLegalDocument throld,
					 M_DocumentLegal dl, M_DocumentType dt
				WHERE throld.THROLD_ReleaseCode='$code'
				AND throld.THROLD_Delete_Time IS NULL
				AND throld.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				AND thlold.THLOLD_ID=tdlold.TDLOLD_THLOLD_ID
				AND tdlold.TDLOLD_Response='0'
				AND tdlold.TDLOLD_Delete_Time IS NULL
				AND dl.DL_DocCode=tdlold.TDLOLD_DocCode
				AND dl.DL_TypeDocID=dt.DocumentType_ID";
		$sql = mysql_query($query);
		$i=0;

		$ActionContent .="
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th></th>
			<th>Kode Dokumen</th>
			<th>Tipe Dokumen</th>
			<th>Nomor Dokumen</th>
			<th>Keterangan</th>
			<th>Waktu Pengembalian</th>
		</tr>";

		while ($arr=mysql_fetch_array($sql)) {
			$LeadTime=($arr['THLOLD_LoanCategoryID']=="1")?date('m/d/Y',strtotime("+7 day", strtotime($field['THROLD_ReleaseDate']))):"";

			$ActionContent .="
			<tr>
				<td class='center'>
					<input id='TDLOLD_ID[]' name='TDLOLD_ID[]' type='checkbox' value='$arr[TDLOLD_ID]'>
				</td>
				<td class='center'><input id='DL_DocCode[]' name='DL_DocCode[]' type='hidden' value='$arr[DL_DocCode]'>$arr[DL_DocCode]</td>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[DL_NoDoc]</td>
				<td class='center'>
					<textarea id='txtTDROLD_Information' name='txtTDROLD_Information[]'></textarea>
				</td>
				<td class='center'>
					<input id='txtTDROLD_LeadTime[$i]' name='txtTDROLD_LeadTime[]' type='text' value='$LeadTime' size='10'  readonly='readonly' class='readonly'>
				</td>
			</tr>";
			$i++;
			$loanType=$arr['THLOLD_LoanCategoryID'];
		}
		$ActionContent .="
		</table>

		<table width='100%'>
		<tr>
			<td>";
			foreach($approvers as $approver){
				$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$approver' readonly='true' class='readonly'/>";
			}
			$ActionContent .="</td>
		</tr>
		<tr>";

		if ($loanType=="1") {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}else {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetailx(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}

		$ActionContent .="
		</tr>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_release_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=release-of-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throld.THROLD_ID,
				 throld.THROLD_ReleaseCode,
				 throld.THROLD_ReleaseDate,
				 u.User_FullName,
 		         drs.DRS_Description,
				 throld.THROLD_Status
		  FROM TH_ReleaseOfLegalDocument throld
		  LEFT JOIN M_User u
			ON throld.THROLD_UserID=u.User_ID
		  LEFT JOIN M_DocumentRegistrationStatus drs
			ON throld.THROLD_Status=drs.DRS_Name
		  WHERE throld.THROLD_Delete_Time is NULL
		  AND u.User_ID='$_SESSION[User_ID]'
		  ORDER BY throld.THROLD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='30%'>Kode Pengeluaran</th>
	<th width='25%'>Tanggal Pengeluaran</th>
	<th width='20%'>Dikeluarkan Oleh</th>
	<th width='20%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$fregdate=date("j M Y", strtotime($field['THROLD_ReleaseDate']));
		$resend=($field['THROLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[THROLD_ReleaseCode]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-release-document.php?id=$field[THROLD_ID]' class='underline'>$field[THROLD_ReleaseCode]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[User_FullName]</td>
			<td class='center'>$field[DRS_Description]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT throld.THROLD_ID, throld.THROLD_ReleaseCode, throld.THROLD_ReleaseDate, u.User_FullName,
 		          throld.THROLD_Status
		   FROM TH_ReleaseOfLegalDocument throld, M_User u
		   WHERE throld.THROLD_Delete_Time is NULL
		   AND throld.THROLD_UserID=u.User_ID
		   AND u.User_ID='$_SESSION[User_ID]'
		   ORDER BY throld.THROLD_ID DESC";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=release-of-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_ReleaseOfLegalDocument throld
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       throld.THROLD_Delete_UserID='$_SESSION[User_ID]',throld.THROLD_Delete_Time=sysdate(),
			       throld.THROLD_Update_UserID='$_SESSION[User_ID]',throld.THROLD_Update_Time=sysdate()
			   WHERE throld.THROLD_ID='$_POST[txtTDROLD_THROLD_ID]'
			   AND throld.THROLD_ReleaseCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=release-of-document.php'>";
	}
}

elseif(isset($_POST[addheader])) {
	$regyear=date("Y");
	$rmonth=date("n");

	// Mengubah Bulan ke Romawi
	switch ($rmonth)	{
		case 1: $regmonth="I"; break;
		case 2: $regmonth="II"; break;
		case 3: $regmonth="III"; break;
		case 4: $regmonth="IV"; break;
		case 5: $regmonth="V"; break;
		case 6: $regmonth="VI"; break;
		case 7: $regmonth="VII"; break;
		case 8: $regmonth="VIII"; break;
		case 9: $regmonth="IX"; break;
		case 10: $regmonth="X"; break;
		case 11: $regmonth="XI"; break;
		case 12: $regmonth="XII"; break;
	}

	// Cari Kode Perusahaan
	$query = "SELECT c.Company_Code, dg.DocumentGroup_Code
			  FROM TH_LoanOfLegalDocument thlold, M_Company c, M_DocumentGroup dg
			  WHERE thlold.THLOLD_LoanCode='$_POST[txtTHROLD_THLOLD_Code]'
			  AND thlold.THLOLD_CompanyID=c.Company_ID
			  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Pengeluaran Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='OUT'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Pengeluaran Dokumen
	$CT_Code="$newnum/OUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode Pengeluaran dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','OUT','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n",$_POST['txtTHROLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_ReleaseOfLegalDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[txtTHROLD_THLOLD_Code]',
					    '$info','0',NULL,NULL,'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=release-of-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$TDLOLD_ID=$_POST[TDLOLD_ID];
	$txtTHROLD_Information=str_replace("<br>", "\n",$_POST[txtTHROLD_Information]);
	$txtTDROLD_Information=str_replace("<br>", "\n",$_POST[txtTDROLD_Information]);
	$txtTDROLD_LeadTime=$_POST[txtTDROLD_LeadTime];
	$sum=count($TDLOLD_ID);

	for ($i=0 ; $i<$sum ; $i++) {
		$TDROLD_LeadTime=date('Y-m-d H:i:s', strtotime($txtTDROLD_LeadTime[$i]));
		if ($TDROLD_LeadTime=="1970-01-01 08:00:00"){
			$TDROLD_LeadTime="";
		}
		$sql1= "INSERT INTO TD_ReleaseOfLegalDocument
				VALUES (NULL,NULL,'$_POST[txtTDROLD_THROLD_ID]', '$TDLOLD_ID[$i]','$txtTDROLD_Information[$i]',
						'$TDROLD_LeadTime',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',
						sysdate(),NULL,NULL)";
		$sql2= "UPDATE TD_LoanOfLegalDocument
				SET TDLOLD_Response='1', TDLOLD_Update_UserID='$_SESSION[User_ID]',TDLOLD_Update_Time=sysdate()
				WHERE TDLOLD_ID='$TDLOLD_ID[$i]'";
		$mysqli->query($sql1);
		$mysqli->query($sql2);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);

	for($i=0;$i<$jumlah;$i++){
		$step=$i+1;
		$sql2= "INSERT INTO M_Approval
				VALUES (NULL,'$_POST[txtTDROLD_THROLD_ReleaseCode]', '$txtA_ApproverID[$i]', '$step',
				        '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		$mysqli->query($sql2);
		$sa_query="SELECT *
				   FROM M_Approval
				   WHERE A_TransactionCode='$_POST[txtTDROLD_THROLD_ReleaseCode]'
				   AND A_ApproverID='$txtA_ApproverID[$i]'
				   AND A_Delete_Time IS NULL";
		$sa_sql=mysql_query($sa_query);
		$sa_arr=mysql_fetch_array($sa_sql);
		$ARC_AID=$sa_arr['A_ID'];
		$str=rand(1,100);
		$RandomCode=crypt('T4pagri'.$str);
		$iSQL="INSERT INTO L_ApprovalRandomCode
			   VALUES ('$ARC_AID','$RandomCode')";
		$mysqli->query($iSQL);
	}

	$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDROLD_THROLD_ReleaseCode]'
			AND A_Step='1'";

	$sql4= "UPDATE TH_ReleaseOfLegalDocument
			SET THROLD_Status='waiting', THROLD_Information='$txtTHROLD_Information',
			THROLD_Update_UserID='$_SESSION[User_ID]',THROLD_Update_Time=sysdate()
			WHERE THROLD_ReleaseCode='$_POST[txtTDROLD_THROLD_ReleaseCode]'
			AND THROLD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_release_doc($_POST['txtTDROLD_THROLD_ReleaseCode']);
		echo "<meta http-equiv='refresh' content='0; url=release-of-document.php'>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// Menampilkan DatePicker
function getLeadTime(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

  	for (i=0;i<=rows;i++){
		 cal.manageFields("txtTDROLD_LeadTime["+i+"]", "txtTDROLD_LeadTime["+i+"]", "%m/%d/%Y");
	}
}
</script>
