<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOAOD_DocumentCode<?= $_GET['row'] ?>.value = symbol;
	window.opener.document.adddetaildoc.docCode.value = window.opener.document.adddetaildoc.docCode.value + "\'" + symbol + "\'" +",";
	window.close();
	}
}
// -->
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?PHP
$batas = 10;
$pg = isset( $_GET['pg'] ) ? $_GET['pg'] : "";

if ( empty( $pg ) ) {
	$posisi = 0;
	$pg = 1;
} else {
	$posisi = ( $pg - 1 ) * $batas;
}

$txtTHLOLD_CompanyID=$_GET['cID'];
$txtTHLOLD_DocumentGroupID=$_GET['gID'];

IF ($txtTHLOLD_DocumentGroupID=="4"){
	if ($_GET['recentCode']) $filter =" AND dl.DAO_DocCode NOT IN (".substr($_GET[recentCode],0, -1).")";

	$query="SELECT DISTINCT c.Company_Name, dg.DocumentGroup_Name,
				dao.DAO_RegTime, dao.DAO_DocCode,
				e.Employee_FullName, mk.MK_Name, dao.DAO_Type, dao.DAO_Jenis, dao.DAO_NoPolisi, dao.DAO_NoRangka,
				dao.DAO_NoMesin, dao.DAO_NoBPKB, dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate, dao.DAO_Pajak_StartDate,
				dao.DAO_Pajak_ExpiredDate, dao.DAO_Lokasi_PT, dao.DAO_Region, dao.DAO_Keterangan
			FROM M_DocumentAssetOwnership dao
			LEFT JOIN db_master.M_Employee e
				ON dao.DAO_Employee_NIK=e.Employee_NIK
			LEFT JOIN db_master.M_MerkKendaraan mk
				ON dao.DAO_MK_ID=mk.MK_ID
			LEFT JOIN M_Company c
				ON dao.DAO_CompanyID=c.Company_ID
			LEFT JOIN M_DocumentGroup dg
				ON dao.DAO_GroupDocID=dg.DocumentGroup_ID
			AND dao.DAO_CompanyID='$txtTHLOLD_CompanyID'
			AND dao.DAO_GroupDocID='$txtTHLOLD_DocumentGroupID'
			AND dao.DAO_Delete_Time IS NULL
			$filter
			ORDER BY dao.DAO_RegTime DESC";
	$limit = " LIMIT $posisi, $batas";
	$no = 1+$posisi;
	$lastQuery = $query.$limit;

	$sql = mysql_query($lastQuery);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		echo "<div class=title><b>$h_arr[Company_Name] - $h_arr[DocumentGroup_Name]</b></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr>
            	<th rowspan='2'>No.</th>
            	<th rowspan='2'>Nama Pemilik</th>
            	<th rowspan='2'>Merk Kendaraan</th>
            	<th rowspan='2'>Type</th>
            	<th rowspan='2'>Jenis</th>
            	<th rowspan='2'>No. Polisi</th>
            	<th rowspan='2'>No. Rangka</th>
            	<th rowspan='2'>No. Mesin</th>
            	<th rowspan='2'>No. BPKB</th>
            	<th colspan='2'>STNK</th>
            	<th colspan='2'>Pajak Kendaraan</th>
            	<th rowspan='2'>Lokasi (PT)</th>
            	<th rowspan='2'>Region</th>
            	<th rowspan='2'>Keterangan</th>
            </tr>
            <tr>
            	<th>Start Date</th>
            	<th>Expired Date</th>
            	<th>Start Date</th>
            	<th>Expired Date</th>
            </tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT dt.DocumentType_Name, dl.DAO_RegTime, dl.DAO_DocCode, dl.DAO_NoDoc, dl.DAO_Instance, dl.DAO_PubDate, dl.DAO_ExpDate,
							   di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DAO_Information3
						FROM M_DocumentLegal dl, M_DocumentType dt, M_DocumentInformation1 di1, M_DocumentInformation2 di2
						WHERE dl.DAO_Status ='1'
						AND dl.DAO_CompanyID='$txtTHLOLD_CompanyID'
						AND dl.DAO_GroupDocID='$txtTHLOLD_DocumentGroupID'
						AND dl.DAO_CategoryDocID='$optTDLOLD_DocumentCategoryID'
						AND dl.DAO_Delete_Time IS NULL
						AND dl.DAO_TypeDocID=dt.DocumentType_ID
						AND dl.DAO_Information1=di1.DocumentInformation1_ID
						AND dl.DAO_Information2=di2.DocumentInformation2_ID
						$filter
						AND (
							dl.DAO_DocCode LIKE '%$search%'
							OR dl.DAO_TypeDocID LIKE '%$search%'
							OR dt.DocumentType_Name LIKE '%$search%'
							OR dl.DAO_Information1 LIKE '%$search%'
							OR di1.DocumentInformation1_Name LIKE '%$search%'
							OR dl.DAO_Information2 LIKE '%$search%'
							OR di2.DocumentInformation2_Name LIKE '%$search%'
							OR dl.DAO_Information3 LIKE '%$search%'
							OR dl.DAO_Instance LIKE '%$search%'
							OR dl.DAO_NoDoc LIKE '%$search%'
							OR dl.DAO_PubDate LIKE '%$search%'
							OR dl.DAO_ExpDate LIKE '%$search%'
						)
						ORDER BY dl.DAO_RegTime DESC ";
			$limit = " LIMIT $posisi, $batas";
			$sql = mysql_query($query.$limit);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$stnk_sdate=date("d M Y", strtotime($arr['DAO_STNK_StartDate']));
			if (($arr['DAO_STNK_ExpiredDate']=="0000-00-00 00:00:00")||($arr['DAO_STNK_ExpiredDate']=="1970-01-01 01:00:00"))
				$stnk_exdate="-";
			else
				$stnk_exdate=date("d M Y", strtotime($arr['DAO_STNK_ExpiredDate']));

            $pajak_sdate=date("d M Y", strtotime($arr['DAO_Pajak_StartDate']));
			if (($arr['DAO_Pajak_ExpiredDate']=="0000-00-00 00:00:00")||($arr['DAO_Pajak_ExpiredDate']=="1970-01-01 01:00:00"))
				$pajak_exdate="-";
			else
				$pajak_exdate=date("d M Y", strtotime($arr['DAO_Pajak_ExpiredDate']));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['DAO_DocCode'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DAO_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['Employee_FullName'] ?></td>
				<td align='center'><?= $arr['MK_Name'] ?></td>
				<td align='center'><?= $arr['DAO_Type'] ?></td>
				<td align='center'><?= $arr['DAO_Jenis'] ?></td>
                <td align='center'><?= $arr['DAO_NoPolisi'] ?></td>
                <td align='center'><?= $arr['DAO_NoRangka'] ?></td>
                <td align='center'><?= $arr['DAO_NoMesin'] ?></td>
                <td align='center'><?= $arr['DAO_NoBPKB'] ?></td>
				<td align='center'><?= $stnk_sdate ?></td>
				<td align='center'><?= $stnk_exdate ?></td>
                <td align='center'><?= $pajak_sdate ?></td>
				<td align='center'><?= $pajak_exdate ?></td>
				<td align='center'><?= $arr['DAO_Lokasi_PT'] ?></td>
                <td align='center'><?= $arr['DAO_Region'] ?></td>
                <td align='center'><?= $arr['DAO_Keterangan'] ?></td>
			</tr>
			<?PHP
		}
	}
}
?>
</TABLE>
<?php
	$jml_data = mysql_num_rows(mysql_query($query));
	$JmlHalaman = ceil($jml_data/$batas);
	if ( $pg > 1 ) {
		$link = $pg-1;
		if (isset($_GET[pID])) {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		} else {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		}
	} else {
		$prev = "Previous ";
	}

	$nmr = '';
	for ($i = 1; $i<= $JmlHalaman; $i++) {
		if ($i == $pg) {
			$nmr .= $i . " ";
		} else {
			if (isset($_GET[pID])) {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			} else {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			}
		}
	}

	if ($pg < $JmlHalaman) {
		$link = $pg + 1;
		if (isset($_GET[pID])) {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		} else {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		}
	} else {
		$next = " Next";
	}

	if ($JmlHalaman > 1) echo '<br />Halaman : ' .$prev . $nmr . $next . '<br /><br />';
?>
</BODY>
</HTML>
