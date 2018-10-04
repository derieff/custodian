<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 4 Sep 2018																						=
= Update Terakhir	: -																									=
= Revisi			:																									=
========================================================================================================================
-->

<link href="./css/mobile.css" rel="stylesheet" type="text/css">
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodocol.php");
$decrp = new custodian_encryp;

if(($_GET['cfm'])&&($_GET['ati'])&&($_GET['rdm'])) {
	$A_Status="3";
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);
	//print_r ($A_ID); echo '<br />';
	//print_r ($ARC_RandomCode); echo '<br />';
	//print_r ($_GET);

	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	//echo $query;
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
				  FROM TH_LoanOfOtherLegalDocuments
				  WHERE THLOOLD_LoanCode='$A_TransactionCode'
				  AND THLOOLD_Delete_Time IS NULL";
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
					//echo $nStep; echo '<br />';

					$qComp = "SELECT Company_Area FROM M_Company WHERE Company_ID = '{$h_arr['THLOOLD_CompanyID']}'";
					$aComp = mysql_fetch_array(mysql_query($qComp));

					if($h_arr['THLOOLD_DocumentType'] == "ORIGINAL" or $h_arr['THLOOLD_DocumentType'] == "SOFTCOPY"){
						$jenis = "17";
					}elseif($h_arr['THLOOLD_DocumentType'] == "HARDCOPY"){
						$jenis = "18";
					}else{
						$jenis = "";
					}

					for ($i=$nStep; $i<=$jStep; $i++) {
						$j = $i + 1;
						$query = "
						SELECT rads.RADS_StatusID, ma.A_ApproverID
						FROM M_Approval ma
						JOIN M_Role_ApproverDocStepStatus rads
							ON ma.A_Step = rads.RADS_StepID
						LEFT JOIN M_Role_Approver ra
							ON rads.RADS_RA_ID = ra.RA_ID
						WHERE ma.A_Step = '{$i}'
							AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$aComp['Company_Area']}')
							AND ma.A_TransactionCode = '{$A_TransactionCode}'
							AND rads.RADS_DocID = '{$jenis}'
							AND rads.RADS_ProsesID = '2'
						";
						$result = mysql_fetch_array(mysql_query($query));

						if ($result['RADS_StatusID'] == '1') {
							//echo 'Step : ' . $i . ' => Kirim Email Approval<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));
							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									if ($xsql = mysql_query($xquery)) {
										mail_loan_doc($A_TransactionCode);
									}
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_loan_doc($A_TransactionCode);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/

							/*if ($i == $jStep) {
								$query = "UPDATE TH_LoanOfOtherLegalDocuments
									SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID',
								    	THLOOLD_Update_Time=sysdate()
									WHERE THLOOLD_LoanCode='$A_TransactionCode'
									AND THLOOLD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 3, 1 );
									mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
								}
							}*/
							break;
						} else if ($result['RADS_StatusID'] == '2') {
							//echo 'Step : ' . $i . ' => Kirim Email Notifikasi<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));

							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									$xsql = mysql_query($xquery);
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/

							/*if ($i == $jStep) {
								$query = "UPDATE TH_LoanOfOtherLegalDocuments
									SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID',
								    	THLOOLD_Update_Time=sysdate()
									WHERE THLOOLD_LoanCode='$A_TransactionCode'
									AND THLOOLD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 3, 1 );
									mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
									//mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
								}
							}*/
						}
					}

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

					/*$query = "UPDATE M_Approval
								SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_Step='$nStep'";
					if ($sql = mysql_query($query)) {
						// Kirim Email ke Approver selanjutnya
						mail_loan_doc($A_TransactionCode);
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
					}*/
				}
				else {
					$query = "UPDATE TH_LoanOfOtherLegalDocuments
								SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID',
								    THLOOLD_Update_Time=sysdate()
								WHERE THLOOLD_LoanCode='$A_TransactionCode'
								AND THLOOLD_Delete_Time IS NULL";
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
									WHERE Company_ID='$h_arr[THLOOLD_CompanyID]'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$Company_Code=$field['Company_Code'];

						// Cari No Permintaan Dokumen Terakhir
						$query = "SELECT MAX(CT_SeqNo)
									FROM M_CodeTransaction
									WHERE CT_Year='$regyear'
									AND CT_Action='DREQ'
									AND CT_GroupDocCode='4'
									AND CT_Delete_Time is NULL";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);

						$DocumentGroup_Code = "DLL";

						if($field[0]==NULL)
							$maxnum=0;
						else
							$maxnum=$field[0];
						$nnum=$maxnum+1;

						$d_query="SELECT *
								  FROM TD_LoanOfOtherLegalDocuments
								  WHERE TDLOOLD_THLOOLD_ID='$h_arr[THLOOLD_ID]'
								  AND TDLOOLD_Delete_Time IS NULL";
						$d_sql=mysql_query($d_query);
						while($d_arr=mysql_fetch_array($d_sql)){
							$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
							$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

							switch ($h_arr['THLOOLD_LoanCategoryID']) {
								case "1":
									$docStatus="3";
									break;
								case "2":
									$docStatus="3";
									break;
								case "3":
									$docStatus="1";
									break;
							}

							$query1="UPDATE M_DocumentsOtherLegal
									 SET DOL_Status ='$docStatus',DOL_Update_Time=sysdate(),DOL_Update_UserID='$A_ApproverID'
									 WHERE DOL_DocCode='$d_arr[TDLOOLD_DocCode]'";
							$query2="INSERT INTO M_CodeTransaction
									 VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
											 '$rmonth','$regyear','$A_ApproverID',sysdate(),
											 '$A_ApproverID',sysdate(),NULL,NULL)";
							$query3="UPDATE TD_LoanOfOtherLegalDocuments
									 SET TDLOOLD_Code ='$CT_Code',TDLOOLD_Update_Time=sysdate(),
										 TDLOOLD_Update_UserID='$A_ApproverID'
									 WHERE TDLOOLD_THLOOLD_ID='$h_arr[THLOOLD_ID]'
									 AND TDLOOLD_DocCode='$d_arr[TDLOOLD_DocCode]'";

							$mysqli->query($query1);
							$mysqli->query($query2);
							$mysqli->query($query3);
							$nnum=$nnum+1;
						}
						mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 3, 1 );
						mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );

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
if($_GET['act']) {
	$act=$decrp->decrypt($_GET['act']);
	if ($act=='reject'){
		$A_ID=$decrp->decrypt($_GET['ati']);
		$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);

		echo "
		<form name='reason' method='post' action='$PHP_SELF'>
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td style='text-align:left !important'; class='header'>Custodian System</td>
		</tr>
		<tr>
			<td><input type='hidden' name='A_ID' value='$A_ID'>
				<input type='hidden' name='ARC_RandomCode' value='$ARC_RandomCode'>
				<textarea name='txtTHLOOLD_Reason' id='txtTHLOOLD_Reason' rows='3'>$arr[THLOOLD_Reason]</textarea>
				<br>*Wajib Diisi Apabila Anda Tidak Menyetujui Permintaan Dokumen.<br>
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

if(isset($_POST[reject])) {
	$A_Status='4';
	$A_ID=$_POST['A_ID'];
	$ARC_RandomCode=$_POST['ARC_RandomCode'];

	if (str_replace(" ", "", $_POST['txtTHLOOLD_Reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.lodoc.php?act=".$decrp->encrypt('reject').">";
	}
	else {
		$THLOOLD_Reason=str_replace("<br>", "\n", $_POST['txtTHLOOLD_Reason']);
		$query = "SELECT *
				  FROM L_ApprovalRandomCode
				  WHERE ARC_AID='$A_ID'
				  AND ARC_RandomCode='$ARC_RandomCode'";
		$sql = mysql_query($query);
		$num = mysql_num_rows($sql);

		if ($num==1) {

			$query = "SELECT *
				  	  FROM M_Approval
				  	  WHERE A_ID='$A_ID'";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$step=$arr[A_Step];
			$AppDate=$arr['A_ApprovalDate'];
			$A_TransactionCode=$arr['A_TransactionCode'];
			$A_ApproverID=$arr['A_ApproverID'];

			if ($AppDate==NULL) {

				$h_query="SELECT *
						  FROM TH_LoanOfOtherLegalDocuments
						  WHERE THLOOLD_LoanCode='$A_TransactionCode'
						  AND THLOOLD_Delete_Time IS NULL";
				$h_sql=mysql_query($h_query);
				$h_arr=mysql_fetch_array($h_sql);

				$query1="UPDATE TH_LoanOfOtherLegalDocuments
						  SET THLOOLD_Status='reject', THLOOLD_Reason='$THLOOLD_Reason',
							  THLOOLD_Update_Time=sysdate(), THLOOLD_Update_UserID='$A_ApproverID'
						  WHERE THLOOLD_LoanCode='$A_TransactionCode'";
				$query2= "UPDATE M_Approval
						  SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
							  A_Update_Time=sysdate()
						  WHERE A_ID='$A_ID'";
				$query3= "UPDATE M_Approval
						  SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						  	  A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
							  A_Status='$A_Status'
						  WHERE A_TransactionCode='$A_TransactionCode'
						  AND A_Step>='$step'";
				$mysqli->query($query1);
				$mysqli->query($query2);
				$mysqli->query($query3);

				$d_query="SELECT *
						  FROM TD_LoanOfOtherLegalDocuments
						  WHERE TDLOOLD_THLOOLD_ID='$h_arr[THLOOLD_ID]'
						  AND TDLOOLD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);
				while($d_arr=mysql_fetch_array($d_sql)){

					$query1="UPDATE M_DocumentsOtherLegal
							 SET DOL_Status ='1',DOL_Update_Time=sysdate(),DOL_Update_UserID='$A_ApproverID'
							 WHERE DOL_DocCode='$d_arr[TDLOOLD_DocCode]'";

					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 4 );
				$e_query="SELECT *
						  FROM M_Approval
						  WHERE A_TransactionCode='$A_TransactionCode'
						  AND A_Step<'$step' ";
				$e_sql=mysql_query($e_query);
				while ($e_arr=mysql_fetch_array($e_sql)){
					mail_notif_loan_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
				}
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
}
?>
