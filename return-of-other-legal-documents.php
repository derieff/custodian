<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource               																			=
= Dibuat Tanggal	: 26 September 2018																					=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Pengembalian Dokumen Lainnya (Legal)</title>
<head>
<?PHP include ("./config/config_db.php");
include ("./include/function.mail.retdocol.php"); ?>

<script language="JavaScript" type="text/JavaScript">
function showList(n) {
	var docGrup="5"; //Grup Dokumen Lainnya (Legal)
	var txtKe = n;
	sList = window.open("popupRelease.php?gID="+docGrup+"&txtKe="+txtKe+"", "Daftar_Pengeluaran_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var jrow = document.getElementById('countRow').value;

	for (i = 1; i <= jrow; i++){
		var txtTDRTOOLD_DocCode = document.getElementById('txtTDRTOOLD_DocCode' + i).value;
		var checkDocCode = 0;
		txtTDRTOOLD_DocCode=txtTDRTOOLD_DocCode.replace("\n","");

		if (txtTDRTOOLD_DocCode.replace(" ", "") == "")  {
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 				$query = "SELECT *
				  		  FROM TD_ReleaseOfOtherLegalDocuments tdrloold, TD_LoanOfOtherLegalDocuments tdloold, M_DocumentsOtherLegal dol
				  		  WHERE tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				  		  AND dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
						  AND dol.DOL_Status='4'
			   			  AND tdrloold.TDROOLD_ReturnCode='0'";
 				$result = mysql_query($query);
				while ($data = mysql_fetch_array($result)) {
					$TDLOOLD_DocCode = $data['TDLOOLD_DocCode'];
					$a = "if (txtTDRTOOLD_DocCode == '$TDLOOLD_DocCode') {";
					$a .= "checkDocCode = 1; ";
					$a .= "}";
				echo $a;
		 		}
			?>
			if (checkDocCode == 0) {
				alert("Kode Dokumen Yang Dikembalikan pada baris ke-" + i + " SALAH!");
				returnValue = false;
			}
		}
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
		<form name='add-detaildoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengembalian Dokumen Lainnya (Legal)</th>";

		$query1="SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
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
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);

		$queryApprover = "
			SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
			FROM M_Role_ApproverDocStepStatus rads
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			LEFT JOIN M_Approver ma
				ON ra.RA_ID = ma.Approver_RoleID
			WHERE rads.RADS_DocID = '22'
				AND rads.RADS_ProsesID = '4'
				AND ma.Approver_Delete_Time IS NULL
				ORDER BY rads.RADS_StepID
		";
		$sqlApprover=mysql_query($queryApprover);
		while($d = mysql_fetch_array($sqlApprover)){
			$approvers[] = $d['Approver_UserID'];  //Approval Untuk ke Custodian
		}

		$ActionContent .="
		<tr>
			<td width='30%'>Nama</td>
			<td>$field1[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field1[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field1[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field1[PosName]</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>

		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Keterangan Pengembalian</th>
		</tr>
		<tr>
			<td>
				<textarea name='txtTDRTOOLD_DocCode1' id='txtTDRTOOLD_DocCode1' cols='20' rows='1' readonly='readonly' onClick='javascript:showList(1);'></textarea>
			</td>
			<td>
				<textarea name='txtTDRTOOLD_Information1' id='txtTDRTOOLD_Information1' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>

		<table width='100%'>
			<tr>
				<td>";
				foreach($approvers as $approver){
					$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$approver' readonly='true' class='readonly'/>";
				}
				$ActionContent .="</td>
			</tr>
			<tr>
				<th  class='bg-white'>
					<input onclick='addRowToTable();' type='button' class='addrow'/>
					<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
					<input type='hidden' value='1' id='countRow' name='countRow' />
				</th>
			</tr>
			</table>

		<table width='100%'>
		<th>
			<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}

	if($act=='detail') {
		$id=$_GET['id'];
		$query1 = "SELECT  tdroold.TDRTOOLD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdroold.TDRTOOLD_ReturnTime
			   	   FROM TD_ReturnOfOtherLegalDocuments tdroold
				   LEFT JOIN M_User u
						ON tdroold.TDRTOOLD_UserID=u.User_ID
				   LEFT JOIN M_DivisionDepartmentPosition ddp
						ON u.User_ID=ddp.DDP_UserID
						AND ddp.DDP_Delete_Time is NULL
				   LEFT JOIN M_Division d
						ON ddp.DDP_DivID=d.Division_ID
				   LEFT JOIN M_Department dp
						ON ddp.DDP_DeptID=dp.Department_ID
				   LEFT JOIN M_Position p
						ON ddp.DDP_PosID=p.Position_ID
			       WHERE tdroold.TDRTOOLD_ReturnCode='$id'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$fregdate=date('j M Y', strtotime($field1[TDRTOOLD_ReturnTime]));


		$ActionContent ="
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengembalian Dokumen Lainnya (Legal)</th>
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td width='67%'>$field1[TDRTOOLD_ReturnCode]</td>
			<td width='3%'><a href='print-return-of-other-legal-documents.php?id=$field1[TDRTOOLD_ReturnCode]' target='_blank'><img src='./images/icon-print.png'></a>
			</td>
		</tr>
		<tr>
			<td>Tanggal Pengembalian</td>
			<td colspan='2'>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td colspan='2'>$field1[User_FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td colspan='2'>$field1[Division_Name]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td colspan='2'>$field1[Department_Name]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td colspan='2'>$field1[Position_Name]</td>
		</tr>
		</table>

		<div class='detail-title'>Daftar Dokumen</div>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Perusahaan</th>
			<th>Kategori Dokumen</th>
			<th>Nama Dokumen</th>
			<th>Instansi Terkait</th>
			<th>No. Dokumen</th>
			<th>Tgl. Terbit</th>
			<th>Tgl. Berakhir</th>
			<th>Ket. Pengembalian</th>
		</tr>";

		$queryd = "SELECT dol.DOL_DocCode, c.Company_Name, dg.DocumentGroup_Name,
						  dc.DocumentCategory_Name, dol.DOL_ID,tdroold.TDRTOOLD_Information,
						  dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
	 					  dol.DOL_TglTerbit, dol.DOL_TglBerakhir
					FROM TD_ReturnOfOtherLegalDocuments tdroold,
					 	 M_DocumentsOtherLegal dol, M_Company c, M_DocumentGroup dg, db_master.M_DocumentCategory dc
					WHERE tdroold.TDRTOOLD_ReturnCode='$id'
					AND tdroold.TDRTOOLD_Delete_Time IS NULL
					AND tdroold.TDRTOOLD_DocCode=dol.DOL_DocCode
					AND dol.DOL_CompanyID=c.Company_ID
					AND dol.DOL_GroupDocID=dg.DocumentGroup_ID
					AND dol.DOL_CategoryDocID=dc.DocumentCategory_ID";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
			$tgl_terbit=date("j M Y", strtotime($arrd['DOL_TglTerbit']));
	        $tgl_berakhir=date("j M Y", strtotime($arrd['DOL_TglBerakhir']));

			$ActionContent .="
			<tr>
				<td align='center'>$arrd[DOL_DocCode]</td>
				<td align='center'>$arrd[Company_Name]</td>
				<td align='center'>$arrd[DocumentCategory_Name]</td>
				<td align='center'>$arrd[DOL_NamaDokumen]</td>
				<td align='center'>$arrd[DOL_InstansiTerkait]</td>
				<td align='center'>$arrd[DOL_NoDokumen]</td>
				<td align='center'>$tgl_terbit</td>
				<td align='center'>$tgl_berakhir</td>
				<td align='center'><pre>$arrd[TDRTOOLD_Information]</pre></td>
			</tr>";
		}
		$ActionContent .="
		</table>";
	}

	//Kirim Ulang Email Persetujuan
	if($act=='resend'){
		mail_return_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=return-of-other-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT tdrtoold.TDRTOOLD_ID, tdrtoold.TDRTOOLD_ReturnCode, tdrtoold.TDRTOOLD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtoold.TDRTOOLD_Status
		  FROM TD_ReturnOfOtherLegalDocuments tdrtoold
		  LEFT JOIN M_User u
            ON tdrtoold.TDRTOOLD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtoold.TDRTOOLD_Status=drs.DRS_Name
		  WHERE tdrtoold.TDRTOOLD_Delete_Time is NULL
		  	AND u.User_ID='$_SESSION[User_ID]'
		  GROUP BY tdrtoold.TDRTOOLD_ReturnCode
		  ORDER BY tdrtoold.TDRTOOLD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th>Kode Pengembalian</th>
	<th>Tanggal Pengembalian</th>
	<th>Nama Penerima Dokumen</th>
	<th>Status</th>
	<th></th>
</tr>";
if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$fregdate=date("j M Y", strtotime($field['TDRTOOLD_ReturnTime']));
		$resend=($field['TDRTOOLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='$PHP_SELF?act=detail&id=$field[1]' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="
	</table>
";

$query1 ="SELECT DISTINCT tdrtoold.TDRTOOLD_ID, tdrtoold.TDRTOOLD_ReturnCode, tdrtoold.TDRTOOLD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtoold.TDRTOOLD_Status
		  FROM TD_ReturnOfOtherLegalDocuments tdrtoold
		  LEFT JOIN M_User u
            ON tdrtoold.TDRTOOLD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtoold.TDRTOOLD_Status=drs.DRS_Name
		  WHERE tdrtoold.TDRTOOLD_Delete_Time is NULL
		  	AND u.User_ID='$_SESSION[User_ID]'";
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
	echo "<meta http-equiv='refresh' content='0; url=return-of-other-legal-documents.php'>";
}

elseif(isset($_POST[adddetail])) {
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

	// Cari Kode Perusahaan $ Kode Grup Dokumen
	$query = "SELECT c.Company_Code, dg.DocumentGroup_Code
			  FROM M_DocumentsOtherLegal dol, M_Company c, M_DocumentGroup dg
			  WHERE dol.DOL_DocCode='$_POST[txtTDRTOOLD_DocCode1]'
			  AND dol.DOL_CompanyID=c.Company_ID
			  AND dol.DOL_GroupDocID=dg.DocumentGroup_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='RETN'
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

	// Kode Registrasi Dokumen
	$CT_Code="$newnum/RETN/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','RETN','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$count=$_POST[countRow];

		//Insert Detail
		for ($i=1 ; $i<=$count ; $i++) {
			$txtTDRTOOLD_DocCode=str_replace("", "\n",$_POST["txtTDRTOOLD_DocCode".$i]);
			$txtTDRTOOLD_Information=str_replace("<br>", "\n",$_POST["txtTDRTOOLD_Information".$i]);

			$sql1= "INSERT INTO TD_ReturnOfOtherLegalDocuments
					VALUES (NULL,'$CT_Code','$txtTDRTOOLD_DocCode','$txtTDRTOOLD_Information',
							'waiting', sysdate(),
							'$_SESSION[User_ID]','$_SESSION[User_ID]', sysdate(),NULL,NULL)";
			$mysqli->query($sql1);

			$sql2="UPDATE TD_ReleaseOfOtherLegalDocuments tdrloold, TD_LoanOfOtherLegalDocuments tdloold, M_DocumentsOtherLegal dol
				   SET tdrloold.TDROOLD_ReturnCode='$CT_Code',
				   	   tdrloold.TDROOLD_Update_UserID='$_SESSION[User_ID]',
					   tdrloold.TDROOLD_Update_Time=sysdate(),
					   dol.DOL_Status='1',
				   	   dol.DOL_Update_UserID='$_SESSION[User_ID]',
					   dol.DOL_Update_Time=sysdate()
				   WHERE tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				   AND tdrloold.TDROOLD_ReturnCode='0'
				   AND tdloold.TDLOOLD_DocCode='$txtTDRTOOLD_DocCode'
				   AND dol.DOL_DocCode=tdloold.TDLOOLD_DocCode";
			$mysqli->query($sql2);
		}

		$txtA_ApproverID=$_POST['txtA_ApproverID'];
		$jumlah=count($txtA_ApproverID);

		for($i=0;$i<$jumlah;$i++){
			$step=$i+1;
			$sql2= "INSERT INTO M_Approval
					VALUES (NULL,'$CT_Code', '$txtA_ApproverID[$i]', '$step',
					        '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
			$mysqli->query($sql2);
			$sa_query="SELECT *
					   FROM M_Approval
					   WHERE A_TransactionCode='$CT_Code'
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
		$sql3 = "UPDATE M_Approval
            SET A_Status='2'
            WHERE A_TransactionCode='$CT_Code' AND A_ApproverID='$txtA_ApproverID[0]' AND A_Step='1'";
        $sfe_sql=mysql_query($sql3);
        if($sfe_sql){
		    mail_return_doc($CT_Code);
        }
	}
	echo "<meta http-equiv='refresh' content='0; url=return-of-other-legal-documents.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);

	// KODE DOKUMEN
	var cellOneSel = row.insertCell(0);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDRTOOLD_DocCode' + iteration;
	el.id = 'txtTDRTOOLD_DocCode' + iteration;
	el.setAttribute("readonly","readonly");
	el.setAttribute("onClick", "javascript:showList("+iteration+");");
	el.size = '80';
	cellOneSel.appendChild(el);

	// KETERANGAN PENGEMBALIAN DOKUMEN
	var cellTwoSel = row.insertCell(1);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDRTOOLD_Information' + iteration;
	el.id = 'txtTDRTOOLD_Information' + iteration;
	el.size = '80';
	cellTwoSel.appendChild(el);
}

// HAPUS BARIS
function removeRowFromTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	if(document.getElementById('countRow').value > 1)
		document.getElementById('countRow').value -= 1;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}
</script>
