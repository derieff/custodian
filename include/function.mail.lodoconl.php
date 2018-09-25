<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 4 Sep 2018																						=
= Update Terakhir	: -																									=
= Revisi			: -																									=
=========================================================================================================================
*/
include_once('./phpmailer/class.phpmailer.php');
include_once('./phpmailer/class.html2text.inc.php');
include_once ("./config/db_sql.php");
include_once ("./include/class.endencrp.php");

function mail_loan_doc($loanCode,$reminder=0){
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	// $testing='TESTING';

	$e_query ="	SELECT User_ID,User_FullName,User_Email,A_TransactionCode,
					   ARC_AID,ARC_RandomCode,THLOONLD_Information,THLOONLD_LoanDate,THLOONLD_LoanCategoryID,
					   THLOONLD_DocumentType, THLOONLD_DocumentWithWatermarkOrNot,
					   LoanCategory_Name,A_Step,A_ApproverID
				FROM TH_LoanOfOtherNonLegalDocuments
				LEFT JOIN M_Approval
					ON THLOONLD_LoanCode=A_TransactionCode
					AND A_Status='2'
					AND A_Delete_Time IS NULL
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				LEFT JOIN M_LoanCategory
					ON THLOONLD_LoanCategoryID = LoanCategory_ID
				WHERE THLOONLD_LoanCode='$loanCode'
				AND THLOONLD_Delete_Time IS NULL";
	//echo $e_query; die;
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	/* Config Lokal */
	/*$mail->Username = 'admin@oncom.local';
	$mail->Password = '';
	$mail->From       = 'admin@oncom.local';
	$mail->FromName   = 'Custodian System';*/

	/* Config Server */
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       ='10.20.10.5';
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Permintaan Dokumen '.$loanCode.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Permintaan Dokumen '.$loanCode.'';
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name


		$ed_query="	SELECT DISTINCT	c1.Company_Name,
						DONL_RegTime,THLOONLD_Reason,THLOONLD_UserID,User_FullName,
                        DONL_NamaDokumen, DONL_NoDokumen, DONL_TahunDokumen,
                        Department_Name,
						db_master.M_Employee.Employee_Department,
						db_master.M_Employee.Employee_Division
					FROM TH_LoanOfOtherNonLegalDocuments
					LEFT JOIN TD_LoanOfOtherNonLegalDocuments
						ON TDLOONLD_THLOONLD_ID=THLOONLD_ID
					LEFT JOIN M_Company c1
						ON c1.Company_ID=THLOONLD_CompanyID
					LEFT JOIN M_DocumentsOtherNonLegal
						ON TDLOONLD_DocCode=DONL_DocCode
                    LEFT JOIN M_Company c2
                        ON DONL_PT_ID=c2.Company_ID
					LEFT JOIN M_User
						ON THLOONLD_UserID=User_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
                    LEFT JOIN db_master.M_Department
        				ON Department_Code=DONL_Dept_Code
					WHERE THLOONLD_LoanCode='$loanCode'
					AND THLOONLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;

		while ($ed_arr = mysql_fetch_object($ed_handle)) {

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
                                Departemen : '.$ed_arr->Department_Name.'<br />
                                Nama Dokumen : '.$ed_arr->DONL_NamaDokumen.'<br />
                                No. Dokumen : '.$ed_arr->DONL_NoDokumen.'<br />
                                Tahun Dokumen : '.date('Y', strtotime($ed_arr->DONL_TahunDokumen)).'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		if($row->THLOONLD_DocumentType == "ORIGINAL" ){
			$tipe_dokumen = "Asli";
		}elseif($row->THLOONLD_DocumentType == "HARDCOPY" or $row->THLOONLD_DocumentType == "SOFTCOPY"){
			$tipe_dokumen = ucfirst(strtolower($row->THLOONLD_DocumentType));
		}else{
			if( $row->THLOONLD_LoanCategoryID != '3') $tipe_dokumen .= "Asli";
			else $tipe_dokumen .= "";
		}
		if( $row->THLOONLD_DocumentWithWatermarkOrNot == "1" ){
			$dengan_cap = " dengan Watermark";
		}elseif( $row->THLOONLD_DocumentWithWatermarkOrNot == "2" ){
			$dengan_cap = " tanpa Watermark";
		}else{
			$dengan_cap = "";
		}
		//$asli = ($row->THLOONLD_LoanCategoryID != '3') ? ' Asli ' : '';
		$keteranganPermintaan = "";
		if( $row->THLOONLD_Information != null or $row->THLOONLD_Information != "" ){
			$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$row->THLOONLD_Information.")";
		}
		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.')</b>
		oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan permintaan dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center><span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodoconl.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a></span>
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodoconl.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a></span><br />
				</p>
				</div>';

		// approval history
		$sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
				FROM M_Approval app
				LEFT JOIN M_User user
				ON app.A_ApproverID = user.User_ID
				LEFT JOIN db_master.M_Employee emp
				ON user.User_ID = emp.Employee_NIK
				WHERE app.A_TransactionCode='".$loanCode."'
				AND app.A_Status NOT IN ('1','2','4')
				GROUP BY app.A_Update_UserID
				ORDER BY app.A_Step";
		$sql_handle = mysql_query($sql);
		$app_history = mysql_num_rows($sql_handle);
		if($app_history){
			$bodyFooter .= '
				<div style="margin-bottom: 15px;margin-top:7%">
				<p>
					Approval History :
					<ol>
				';

			while ($obj = mysql_fetch_object($sql_handle)) {
					$bodyFooter .= '
						<li><b>'.ucwords(strtolower($obj->User_FullName)).'</b><BR>
						Dept : '.ucwords(strtolower($obj->Employee_Department)).'<BR>
						Div : '.ucwords(strtolower($obj->Employee_Division)).'<BR>
						Tanggal Persetujuan : '.date('d/m/Y H:i:s', strtotime($obj->A_ApprovalDate)).'.</li>
					';
			}

			$bodyFooter .= '
					</ol>
				</p>
				</div>';
		}

			$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$emailContent;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);


	/*try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}
	  print_r ($errorMsg);

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}*/

	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
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


	//Approval ke Pak Arif, pararel approval ke Pak Rianto
	if ($row->A_ApproverID=='00000001'){
	//if ($row->A_ApproverID=='00000948'){
		/* EDIT THIS PART (userPararelApp = USER ID PARAREL) */
		$userPararelApp='00000005'; //Pak Rianto
		$query_pararel="SELECT User_FullName, User_Email
						FROM M_User
						WHERE User_ID='$userPararelApp'";
		$sql_pararel=mysql_query($query_pararel);
		$obj_pararel=mysql_fetch_object($sql_pararel);
		/* ======================== */
		$bodyHeader = '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$obj_pararel->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.')</b>
		oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center" style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%" style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%" style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$emailContent=$bodyHeader.$body.$bodyFooter;
		//echo $row->user_email.$emailContent;
		$mail->ClearAddresses();
		$mail->AddAddress($obj_pararel->User_Email,$obj_pararel->User_FullName);
		//$mail->AddAddress('sabrina.davita@tap-agri.com','Sabrina ID');
		$h2t =& new html2text($body);
		$mail->AltBody = $h2t->get_text();
		$mail->WordWrap   = 80; // set word wrap
		$mail->MsgHTML($emailContent);

		/*
		try {
		  if ( !$mail->Send() ) {
			$error = "Unable to send to: " . $to . "<br>";
			throw new phpmailerAppException($error);
		  } else {
			//echo 'Message has been sent using SMTP<br><br>';
		  }
		} catch (phpmailerAppException $e) {
		  $errorMsg[] = $e->errorMessage();
		}

		if ( count($errorMsg) > 0 ) {
		  foreach ($errorMsg as $key => $value) {
			$thisError = $key + 1;
			//echo $thisError . ': ' . $value;
		  }
		}*/

		if(!$mail->Send()){
			echo "
			<table border='0' align='center' cellpadding='0' cellspacing='0'>
			<tbody>
			<tr>
				<td class='header'>Pengiriman Email Gagal</td>
			</tr>
			<tr>
				<td>
					ERROR<br>
					Terjadi Gangguan Dalam Pengiriman Email.<br>
					Mohon maaf atas ketidaknyamanan ini.<br>
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

function mail_notif_loan_doc($loanCode, $User_ID, $status, $attr){
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	//$testing='TESTING';

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	/* Config Lokal */
	/*$mail->Username = 'admin@oncom.local';
	$mail->Password = '';
	$mail->From       = 'admin@oncom.local';
	$mail->FromName   = 'Custodian System';*/


	/* Config Server */
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       ='10.20.10.5';
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($status=='3'){
		$mail->Subject  =''.$testing.'Notifikasi Proses Permintaan Dokumen '.$loanCode;
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.'Notifikasi Proses Permintaan Dokumen '.$loanCode;
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		// $ed_query="	SELECT DISTINCT	Company_Name,THLOONLD_LoanCategoryID, DocumentCategory_Name,DocumentType_Name,
		// 							DONL_NoDoc,DONL_RegTime,THLOONLD_Reason,THLOONLD_UserID,User_FullName,
		// 							THLOONLD_Information,Company_ID, LoanCategory_Name,DocumentGroup_Name
		// 			FROM TH_LoanOfOtherNonLegalDocuments
		// 			LEFT JOIN TD_LoanOfOtherNonLegalDocuments
		// 				ON TDLOONLD_THLOONLD_ID=THLOONLD_ID
		// 			LEFT JOIN M_LoanCategory
		// 				ON THLOONLD_LoanCategoryID = LoanCategory_ID
		// 			LEFT JOIN M_DocumentGroup
		// 				ON THLOONLD_DocumentGroupID=DocumentGroup_ID
		// 			LEFT JOIN M_Company
		// 				ON Company_ID=THLOONLD_CompanyID
		// 			LEFT JOIN M_DocumentsOtherNonLegal
		// 				ON TDLOONLD_DocCode=DONL_DocCode
		// 			LEFT JOIN M_DocumentCategory
		// 				ON DocumentCategory_ID=TDLOONLD_DocumentCategoryID
		// 			LEFT JOIN M_DocumentType
		// 				ON DocumentType_ID=DONL_TypeDocID
		// 			LEFT JOIN M_User
		// 				ON THLOONLD_UserID=User_ID
		// 			WHERE THLOONLD_LoanCode='$loanCode'
		// 			AND THLOONLD_Delete_Time IS NULL";

		$ed_query="	SELECT DISTINCT	Company_Name,THLOONLD_LoanCategoryID,
						THLOONLD_DocumentType, THLOONLD_DocumentWithWatermarkOrNot,
						DONL_RegTime,THLOONLD_Reason,THLOONLD_UserID,User_FullName,
						THLOONLD_Information,Company_ID, LoanCategory_Name
					FROM TH_LoanOfOtherNonLegalDocuments
					LEFT JOIN TD_LoanOfOtherNonLegalDocuments
						ON TDLOONLD_THLOONLD_ID=THLOONLD_ID
					LEFT JOIN M_LoanCategory
						ON THLOONLD_LoanCategoryID = LoanCategory_ID
					LEFT JOIN M_Company
						ON Company_ID=THLOONLD_CompanyID
					LEFT JOIN M_DocumentsOtherNonLegal
						ON TDLOONLD_DocCode=DONL_DocCode
					LEFT JOIN M_User
						ON THLOONLD_UserID=User_ID
					WHERE THLOONLD_LoanCode='$loanCode'
					AND THLOONLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								Tgl. Terbit : '.date('d/m/Y H:i:s', strtotime($ed_arr->DONL_RegTime)).'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THLOONLD_Reason;
			$regUser=$ed_arr->THLOONLD_UserID;
			$requester=$ed_arr->User_FullName;
			$company=$ed_arr->Company_ID;
			$info=$ed_arr->THLOONLD_Information;
			$loanName = $ed_arr->LoanCategory_Name;
			//$asli = ($ed_arr->THLOONLD_LoanCategoryID != '3') ? ' Asli ' : '';
			if($ed_arr->THLOONLD_DocumentType == "ORIGINAL" ){
				$tipe_dokumen = "Asli";
			}elseif($ed_arr->THLOONLD_DocumentType == "HARDCOPY" or $ed_arr->THLOONLD_DocumentType == "SOFTCOPY"){
				$tipe_dokumen = ucfirst(strtolower($ed_arr->THLOONLD_DocumentType));
			}else{
				if( $ed_arr->THLOONLD_LoanCategoryID != '3') $tipe_dokumen .= "Asli";
				else $tipe_dokumen .= "";
			}
			if( $ed_arr->THLOONLD_DocumentWithWatermarkOrNot == "1" ){
				$dengan_cap = " dengan Watermark";
			}elseif( $ed_arr->THLOONLD_DocumentWithWatermarkOrNot == "2" ){
				$dengan_cap = " tanpa Watermark";
			}else{
				$dengan_cap = "";
			}
			$keteranganPermintaan = "";
			if( $row->THLOONLD_Information != null or $row->THLOONLD_Information != "" ){
				$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$info.")";
			}
		}
	if (($status=='3')&&($row->User_ID<>$regUser)){
		if ($attr == '1') {
			$bodyFooter .= '
	                    </TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah disetujui oleh Departemen Custodian. Terima kasih.  </span><br />
					</p>
					</div>';
		} else {
			$bodyFooter .= '
	                    </TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Sedang dalam proses persetujuan dari Departemen Custodian. Terima kasih.  </span><br />
					</p>
					</div>';
		}
	}
	if (($status=='3')&&($row->User_ID==$regUser)){
		// CC UNTUK CFO
		$query_cc="	SELECT User_FullName, User_Email
					FROM M_User
					LEFT JOIN M_DivisionDepartmentPosition
						ON DDP_UserID=User_ID
					LEFT JOIN M_Position
						ON DDP_PosID=Position_ID
					WHERE Position_Name = 'CHIEF FINANCIAL OFFICER - AMP'";
		$sql_cc=mysql_query($query_cc);
		$obj_cc=mysql_fetch_object($sql_cc);

		//$mail->AddCC($obj_cc->User_Email,$obj_cc->User_FullName);
		$mail->addCustomHeader("CC: {$obj_cc->User_FullName} <{$obj_cc->User_Email}>");

		//CC UNTUK CEO REGION
		$ceo_query="SELECT User_FullName, User_Email
					FROM M_User
					LEFT JOIN M_DivisionDepartmentPosition
						ON DDP_UserID=User_ID
					LEFT JOIN M_Position
						ON DDP_PosID=Position_ID
					LEFT JOIN M_Company
						ON Company_ID='$company'
					WHERE Position_Name=CONCAT('CEO - ',Company_Area)";
		$ceo_handle=mysql_query($ceo_query);
		$ceo_obj=mysql_fetch_object($ceo_handle);
		if($ceo_obj->User_Email){
			//$mail->AddCC($ceo_obj->User_Email,$ceo_obj->User_FullName);
			$mail->addCustomHeader("CC: {$ceo_obj->User_FullName} <{$ceo_obj->User_Email}>");
		}

		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. Permintaan dokumen Bapak/Ibu sedang diproses oleh Tim Custodian. Terima kasih.  </span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID<>$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}

		// approval history
		$sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
				FROM M_Approval app
				LEFT JOIN M_User user
				ON app.A_ApproverID = user.User_ID
				LEFT JOIN db_master.M_Employee emp
				ON user.User_ID = emp.Employee_NIK
				WHERE app.A_TransactionCode='".$loanCode."'
				AND app.A_Status NOT IN ('1','2','4')
				GROUP BY app.A_Update_UserID
				ORDER BY app.A_Step";
		$sql_handle = mysql_query($sql);
		$app_history = mysql_num_rows($sql_handle);
		if($app_history){
			$bodyFooter .= '
				<div style="margin-bottom: 15px">
				<p>
					Approval History :
					<ol>
				';

			$i=1;
			while ($obj = mysql_fetch_object($sql_handle)) {
				//if ($i < $app_history) {
					$bodyFooter .= '
						<li><b>'.ucwords(strtolower($obj->User_FullName)).'</b><BR>
						Dept : '.ucwords(strtolower($obj->Employee_Department)).'<BR>
						Div : '.ucwords(strtolower($obj->Employee_Division)).'<BR>
						Tanggal Persetujuan : '.date('d/m/Y H:i:s', strtotime($obj->A_ApprovalDate)).'.</li>
					';
				//}
				$i++;
			}

			$bodyFooter .= '
					</ol>
				</p>
				</div>';
		}

	$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$loanName.')</b>
		oleh '.$requester.' '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	/*
	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}*/

	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
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

?>
