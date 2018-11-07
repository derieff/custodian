<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
	<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
</span>
<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
	<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
</span><br />

<p align=center style="margin-bottom: 7%;">
	<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
		<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?act='.$decrp->encrypt('confirm').'&user='.$decrp->encrypt($regUser).'&doc='.$decrp->encrypt($docID).'&rel='.$decrp->encrypt($relCode).'">Sudah Diterima</a>
	</span>
	<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
		<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($accept_row->ARC_AID).'&rdm='.$decrp->encrypt($accept_row->ARC_RandomCode).'">Belum Diterima</a>
	</span><br />
</p>
<?php
// include ("./config/config_db.php");
// //GENERATE USER ID
// $query="select generateUserID() as UserID";
// $sql=mysql_query($query);
// if(mysql_num_rows($sql) > 0){
// 	$obj=mysql_fetch_array($sql);
// 	$User_ID=$obj['UserID'];
//
// 	echo $User_ID;
// }else{
// 	echo "tidak ada function generateUserID()";
// }
// Fungsi header dengan mengirimkan raw data excel
// header("Content-type: application/vnd-ms-excel");
//
// // Mendefinisikan nama file ekspor "hasil-export.xls"
// header("Content-Disposition: attachment; filename=contoh-convert-sql-ke-excel.xls");
//
// include ("./config/config_db.php");
// $query = "SELECT donl.DONL_ID, c.Company_Name, c2.Company_Name, donl.DONL_NoDokumen, donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, m_d.Department_Name, lds.LDS_Name, donl.DONL_DocCode FROM M_DocumentsOtherNonLegal donl, M_Company c, M_User u, M_LoanDetailStatus lds, M_Company c2, db_master.M_Department m_d WHERE c.Company_ID=donl.DONL_CompanyID AND donl.DONL_Delete_Time IS NULL AND donl.DONL_Status=lds.LDS_ID AND donl.DONL_RegUserID=u.User_ID AND c2.Company_ID=donl.DONL_PT_ID AND m_d.Department_Code=donl.DONL_Dept_Code";
//
// $sql = mysql_query($query);
//
// $MainContent = "<table width='100%' border='1' class='stripeMe'>
// <tr>
// 	<th colspan='9' align='center'>Daftar Dokumen Lainnya (Di Luar Legal)</th>
// </tr>
// <tr>
// 	<th>ID</th>
// 	<th>Kode Dokumen</th>
// 	<th>Perusahaan</th>
// 	<th>Nama PT (Dokumen)</th>
// 	<th>No. Dokumen</th>
// 	<th>Nama Dokumen</th>
// 	<th>Tahun Dokumen</th>
// 	<th>Departemen</th>
// 	<th>Status</th>
// </tr>
// ";
//
// while ($field = mysql_fetch_array($sql)) {
// 	$regdate=strtotime($field[3]);
// 	$fregdate=date("j M Y", $regdate);
// $MainContent .="
// <tr>
// 	<td class='center'>$field[DONL_ID]</td>
// 	<td class='center'>
// 		<a href='$PHP_SELF?act=detailONL&id=$field[DONL_DocCode]' class='underline'>$field[DONL_DocCode]</a></td>
// 	<td class='center'>$field[1]</td>
// 	<td class='center'>$field[2]</td>
// 	<td class='center'>$field[3]</td>
// 	<td class='center'>$field[4]</td>
// 	<td class='center'>$field[5]</td>
// 	<td class='center'>$field[6]</td>
// 	<td class='center'>$field[7]</td>
// </tr>
// ";
// $no=$no+1;
// }
// $MainContent .="</table>";
//
// echo $MainContent;
?>
