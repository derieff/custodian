<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 14 Sep 2018																						=
= Update Terakhir	: -																									=
= Revisi			:																									=
========================================================================================================================
-->
<link href="./css/mobile.css" rel="stylesheet" type="text/css">
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocao.php");
$decrp = new custodian_encryp;
$PHP_SELF=$_SERVER['PHP_SELF'];

if( !empty($_GET['cfm']) && !empty($_GET['ati']) && !empty($_GET['rdm']) ) {
	$A_Status="3";
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);

	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	$sql = mysql_query($query);
	$num = mysql_num_rows($sql);

	if ($num==1) {
		// MENCARI TAHAP APPROVAL USER TERSEBUT
		$query = "SELECT *
				  FROM M_Approval
				  WHERE A_ID='$A_ID'";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		$step=$arr['A_Step'];
		$AppDate=$arr['A_ApprovalDate'];
		$A_TransactionCode=$arr['A_TransactionCode'];
		$A_ApproverID=$arr['A_ApproverID'];

		$h_query="SELECT *
				  FROM TH_ReleaseOfAssetOwnershipDocument throaod,TH_LoanOfAssetOwnershipDocument thloaod
				  WHERE throaod.THROAOD_ReleaseCode='$A_TransactionCode'
				  AND throaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
				  AND throaod.THROAOD_Delete_Time IS NULL
				  AND thloaod.THLOAOD_Delete_Time IS NULL";
		$h_sql=mysql_query($h_query);
		$h_arr=mysql_fetch_array($h_sql);

		if ($AppDate==NULL) {
			// MENCARI JUMLAH APPROVAL
			$query = "SELECT MAX(A_Step) AS jStep
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$jStep=$arr['jStep'];

			// UPDATE APPROVAL
			$query = "UPDATE M_Approval
						SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Update_Time=sysdate()
						WHERE A_ID='$A_ID'";
			$sql = mysql_query($query);

			// PROSES BILA "SETUJU"
			if ($A_Status=='3') {
				// CEK APAKAH MERUPAKAN APPROVAL FINAL
				if ($step <> $jStep) {
					$nStep=$step+1;
					$query = "UPDATE M_Approval
								SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_Step='$nStep'";
					if ($sql = mysql_query($query)) {
						// Kirim Email ke Approver selanjutnya
						mail_release_doc($A_TransactionCode, 0);
						echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Custodian System</td>
		</tr>
		<tr>
			<td>
				Persetujuan Anda Telah Disimpan.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
					}
				}
				else {
					$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
								SET THROAOD_Status='accept', THROAOD_Update_UserID='$A_ApproverID',
								    THROAOD_Update_Time=sysdate()
								WHERE THROAOD_ReleaseCode='$A_TransactionCode'
								AND THROAOD_Delete_Time IS NULL";
					if ($sql = mysql_query($query)) {
						// ACTION UNTUK GENERATE NO DOKUMEN
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
						$query = "SELECT *
									FROM M_Company
									WHERE Company_ID='$h_arr[THLOAOD_CompanyID]'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$Company_Code=$field['Company_Code'];

						// Cari Kode Dokumen Grup
						$query = "SELECT *
									FROM M_DocumentGroup
									WHERE DocumentGroup_ID ='4'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$DocumentGroup_Code=$field['DocumentGroup_Code'];

						// Cari No Dokumen Terakhir
						$query = "SELECT MAX(CD_SeqNo)
									FROM M_CodeDocument
									WHERE CD_Year='$regyear'
									-- AND CT_Action='DOUT'
									AND CD_GroupDocCode='$DocumentGroup_Code'
									AND CD_CompanyCode='$Company_Code'
									AND CD_Delete_Time is NULL";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);

						if($field[0]==NULL)
							$maxnum=0;
						else
							$maxnum=$field[0];
						$nnum=$maxnum+1;

						$d_query="SELECT *
								  FROM TD_ReleaseOfAssetOwnershipDocument tdroaod, TD_LoanOfAssetOwnershipDocument tdloaod
								  WHERE tdroaod.TDROAOD_THROAOD_ID='$h_arr[THROAOD_ID]'
								  AND tdroaod.TDROAOD_Delete_Time IS NULL
								  AND tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID";
						$d_sql=mysql_query($d_query);
						while($d_arr=mysql_fetch_array($d_sql)){
							$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
							// Kode Pengeluaran Dokumen
							$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

							switch ($h_arr['THLOAOD_LoanCategoryID']) {
								case "1":
									$docStatus="4";
									$code="0";
									break;
								case "2":
									$docStatus="5";
									$code=NULL;
									break;
								case "3":
									$docStatus="1";
									$code=NULL;
									break;
							}
							$query1="UPDATE M_DocumentAssetOwnership
									 SET DAO_Status='$docStatus', DAO_Update_UserID='$A_ApproverID',
									 	 DAO_Update_Time=sysdate()
									 WHERE DAO_DocCode='$d_arr[TDLOAOD_DocCode]'";
							$query2="INSERT INTO M_CodeTransaction
								   	 VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
											 '$rmonth','$regyear','$A_ApproverID',sysdate(),
											 '$A_ApproverID',sysdate(),NULL,NULL)";
							$query3="UPDATE TD_ReleaseOfAssetOwnershipDocument
									 SET TDROAOD_Code ='$CT_Code',TDROAOD_ReturnCode='$code',
										 TDROAOD_Update_UserID='$A_ApproverID', TDROAOD_Update_Time=sysdate()
									 WHERE TDROAOD_THROAOD_ID='$h_arr[THROAOD_ID]'
									 AND TDROAOD_TDLOAOD_ID='$d_arr[TDLOAOD_ID]'";

							$mysqli->query($query1);
							$mysqli->query($query2);
							$mysqli->query($query3);
							$nnum=$nnum+1;
						}
						mail_notif_release_doc($A_TransactionCode, $h_arr['THLOAOD_UserID'], 3 );
						mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );

						echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Berhasil</td>
		</tr>
		<tr>
			<td>
				Persetujuan Anda Telah Disimpan.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
					}
				}
			}
		}
		else {
			echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Gagal</td>
		</tr>
		<tr>
			<td>
				Anda tidak dapat melakukan persetujuan ini<br>
				karena Anda telah melakukan persetujuan sebelumnya.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
		}
	}
	else {
			echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Gagal</td>
		</tr>
		<tr>
			<td>
				Anda tidak dapat melakukan persetujuan ini<br>
				karena Anda tidak memiliki hak persetujuan untuk transaksi ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
	}
}
if(isset($_GET['act'])) {
	$act=$decrp->decrypt($_GET['act']);

	if ($act=='confirm'){
		$userID=$decrp->decrypt($_GET['user']);
		$docID=$decrp->decrypt($_GET['doc']);
		$relCode=$decrp->decrypt($_GET['rel']);
		$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
					SET THROAOD_DocumentReceived='1', THROAOD_Update_UserID='$userID', THROAOD_Update_Time=sysdate()
					WHERE THROAOD_ID='$docID'
					AND THROAOD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		if($sql){
			mail_notif_reception_release_doc($relCode, $userID, 3 ,1);
			mail_notif_reception_release_doc($relCode, "cust0002", 3 );
			echo "<meta http-equiv='refresh' content='0; url=detail-of-release-asset-ownership-document.php?id=$docID'>";
		}else{
			$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
		}
	}
	else if ($act=='reject'){
		$userID=$decrp->decrypt($_GET['user']);
		$docID=$decrp->decrypt($_GET['doc']);
		$relCode=$decrp->decrypt($_GET['rel']);

		echo "
		<form name='reason' method='post' action='$PHP_SELF'>
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td style='text-align:left !important'; class='header'>Custodian System</td>
		</tr>
		<tr>
			<td>
				<input type='hidden' name='user_id' value='$userID'>
				<input type='hidden' name='doc_id' value='$docID'>
				<input type='hidden' name='rel_code' value='$relCode'>
				<textarea name='reject_reason' id='reject_reason' rows='3'></textarea>
				<br>*Wajib Diisi Apabila Anda Tidak Menyetujui Pengeluaran Dokumen.<br>
			</td>
		</tr>
		<tr>
			<td>
				<center><input name='reject' type='submit' value='Tolak'/></center>
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System</td>
		</tr>
		</tbody>
		</table>
		</form>";
	}
}

if(isset($_POST['reject'])) {
	$rejectedFlag='2';
	$userID=$_POST['user_id'];
	$docID=$_POST['doc_id'];
	$relCode=$_POST['rel_code'];

	if (str_replace(" ", "", $_POST['reject_reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.reldocao.php?act=".$decrp->encrypt('reject').
		"&user=".$decrp->encrypt($userID)."&doc=".$decrp->encrypt($docID)."&rel=".$decrp->encrypt($relCode).">";
	}
	else {
		$rejectReason=str_replace("<br>", "\n",$_POST['reject_reason']);
		
		$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
					SET THROAOD_DocumentReceived='$rejectedFlag',THROAOD_ReasonOfDocumentCancel='$rejectReason', THROAOD_Update_UserID='$userID', THROAOD_Update_Time=sysdate()
					WHERE THROAOD_ID='$docID'
					AND THROAOD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		if($sql){
			mail_notif_reception_release_doc($relCode, $userID, 4 ,1);
			mail_notif_reception_release_doc($relCode, "cust0002", 4 );
			echo "<meta http-equiv='refresh' content='0; url=detail-of-release-asset-ownership-document.php?id=$docID'>";
		}else{
			$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
		}
	}
}
?>
