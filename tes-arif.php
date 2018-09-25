<?php
// echo str_pad(2139, 8, "0", STR_PAD_LEFT);
date_default_timezone_set("Asia/Jakarta");
$sekarang = date('H:i:s d-m-Y');
echo "Sekarang : ".$sekarang."<br>";
echo "Batas Waktu : ".date('H:i d-m-Y', strtotime('+150 minutes', strtotime($sekarang)));
// include ("./include/function.mail.lodocol.php");
// mail_loan_doc('003/REQ/AMP/DLL/IX/2018','1');
// include ("./config/config_db.php");
// $sa_query = "SELECT *
//         FROM M_Approval
//         WHERE A_TransactionCode='003/REQ/AMP/KEA/IX/2018'
//         AND A_ApproverID='00002568'
//         AND A_Step = '1'
//         AND A_Delete_Time IS NULL";
// $sa_arr = mysql_fetch_array(mysql_query($sa_query));
// $ARC_AID = $sa_arr['A_ID'];
// $str = rand(1,100);
// $RandomCode = crypt('T4pagri'.$str);
// $iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
// mysql_query($iSQL);
// echo $ARC_AID."<br>";
// echo $RandomCode;
// // $query = "SELECT u.User_ID, u.User_Name, u.User_FullName, u.User_Email, u.User_SPV1, u.User_SPV2, u.User_Local,
// // e.Employee_GradeCode, e.Employee_Grade
// // FROM custodian.M_User AS u JOIN db_master.M_Employee AS e
// // WHERE u.User_ID = e.Employee_NIK AND e.Employee_gradecode IN ('0000000005','06','0000000003','05','04','0000000004')";
// //
// // $sql = mysql_query($query);
// // while($field = mysql_fetch_array($sql)){
// //     echo "User ID : ".$field['User_ID']."<br>";
// //     echo "Name : ".$field['User_FullName']."<br>";
// //     echo "Employee Grade Code : ".$field['Employee_GradeCode']."<br>";
// //     echo "Employee Grade Name : ".$field['Employee_Grade']."<br>";
// //
// //     echo "<hr>";
// // }
// $gchest = '17';
// $query = "SELECT *
//             FROM M_DocumentLocationStructure
//             WHERE DLS_ID='$gchest'
//             AND DLS_Delete_Time is NULL
//             ORDER BY DLS_ID";
// $sql = mysql_query($query);
// $field = mysql_fetch_array($sql);
//
// $numCellChar=$field['DLS_TotalCellChar'];
// $numCellNo=$field['DLS_TotalCellNo'];
// $numCabin=$field['DLS_TotalCabin'];
// $numFolder=$field['DLS_TotalFolder'];
//
// for($ncc=1; $ncc<=$numCellChar; $ncc++){
// for ($ncn=1; $ncn<=$numCellNo; $ncn++){
//     for ($nc=1; $nc<=$numCabin ;$nc++){
//         for ($nf=1; $nf<=$numFolder ;$nf++){
//
//             $new_gchest=str_pad($gchest, 2, "0", STR_PAD_LEFT);
//             $new_chr=chr($ncc+64);
//             $new_ncn=str_pad($ncn, 2, "0", STR_PAD_LEFT);
//             $new_nc=str_pad($nc, 2, "0", STR_PAD_LEFT);
//             $new_nf=str_pad($nf, 3, "0", STR_PAD_LEFT);
//
//             $code="$new_gchest"."$new_chr"."$new_ncn"."$new_nc"."$new_nf"."F";
//             $name="Chest $new_gchest Cell $new_chr$new_ncn Cabin $new_nc Folder $new_nf";
//
//             //Arief F - 14082018
//             // Cek DL_Code ada atau tidak di table L_DocumentLocation
//             $cek = "SELECT DL_ID FROM L_DocumentLocation WHERE DL_Code='$code'";
//             $sql_cek = mysql_query($cek);
//             // $cek_ada = mysql_fetch_array($s?q)
//             $num = mysql_num_rows($sql_cek);
//             if($num == 0){
//                 $CompanyID = NULL;
//                 $DocGroupID = NULL;
//                 $get_other_component = "SELECT DL_Chest, DL_CellChar, DL_CellNo, DL_Cabin, DL_CompanyID, DL_DocGroupID
//             		  FROM L_DocumentLocation
//             		  WHERE DL_DocGroupID is NOT NULL AND DL_Delete_Time is NULL AND
//             			DL_Chest='$new_gchest' AND DL_CellChar='$new_chr' AND DL_CellNo='$new_ncn' AND DL_Cabin='$new_nc'
//             		  ORDER BY DL_ID LIMIT 0,1";
//                 $run_sql = mysql_query($get_other_component);
//                 $cek_available_component = mysql_num_rows($run_sql);
//                 if($cek_available_component > 0){
//                     $d_cac = mysql_fetch_array($run_sql);
//                     $CompanyID = $d_cac['DL_CompanyID'];
//                     $DocGroupID = $d_cac['DL_DocGroupID'];
//                 }
//                 echo $code."<br>".$name."<br>";
//                 echo $CompanyID."<br>".$DocGroupID."<br>";
//                 echo "gak ada<hr>";
//                 // $sql= "INSERT INTO L_DocumentLocation
//                 //             VALUES (NULL,'$code','$name','$new_gchest','$new_chr','$new_ncn','$new_nc',
//                 //                     '$new_nf','$CompanyID','$DocGroupID','1','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',
//                 //                     sysdate(),NULL,NULL)";
//                 // $mysqli->query($sql);
//                 if($mysqli->query($sql1)) {
//                     echo "berhasil";
//                     $count_process++; //Arief F - 21082018
//                 }else{
//                     echo "gagal";
//                 }
//             }
//             //End of Cek DL_Code ada atau tidak di table L_DocumentLocation
//         }
//     }
// }
// }
 ?>
