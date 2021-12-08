<?php
/**
 * Created by PhpStorm.
 * User: Victor
 * Date: 2017/3/9
 * Time: 23:59
 */
require('lib/init.php');

if(empty($_POST)&&!isset($_FILES['file'])&&!isset($_FILES['md5file'])){
	include('view/index.html');
}else{

    
	if(isset($_FILES['file']['name'])&&$_FILES['file']['error']==0){
		$filename=ROOT."/upload/".randStr().".txt";

		
		move_uploaded_file($_FILES['file']['tmp_name'],$filename);
		
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize ($filename));
    	fclose($handle);
    	$contents=str_replace("\r\n"," ",$contents);
   		$data = explode(" ", $contents);

   		$sql="insert ignore into dictionary(source,md5) values(' ',' ')";
   		foreach ($data as $d) {
   			if(!empty($d)){
   			    $d=htmlspecialchars($d);
   				$md5=md5($d);
   				$sql.=",('$d'".","."'$md5')";
   			}

   		}
   		$sql.=";";
        $tip = query($sql) ? mysql_affected_rows() . " rows affected" : "insert fail :".mysql_error().mysql;
		include('view/index.html');

	}

	if(isset($_FILES['md5file']['name'])&&$_FILES['md5file']['error']==0){
		$filename=ROOT."/upload/".randStr().".txt";
		
		move_uploaded_file($_FILES['md5file']['tmp_name'],$filename);
		
		$handle = fopen($filename, "r");
		$contents = file_get_contents($filename);
    	fclose($handle);
    	$contents=str_replace("\r\n"," ",$contents);
  		
   		$data = explode(" ", $contents);
   		$total_time=0;
        $res_arr=array();
        $time_arr=array();
        $data_arr=array();

   		foreach ($data as $d) {

   		    if(strlen($d)!=32){
   		        continue;
            }
   			$sql="select source from dictionary where md5='$d' limit 1";
   			$t1=microtime(true);
   			$rs=mysql_fetch_row(query($sql));

   			if(empty($rs)){
   				$res=false;
                array_push($res_arr,$res);
                array_push($data_arr,$d);
                array_push($time_arr,"null");
   			}else{
   				$time=microtime(true)-$t1;
   				$total_time+=$time;
   				array_push($time_arr,$time);
                $res=true;
                array_push($res_arr,$res);
                $origin=$rs[0];
                array_push($data_arr,$origin);
            }

   		}   $true=0;
   			$size=count($res_arr);
   			for ($i=0;$i<count($res_arr);$i++) {
   				if($res_arr[$i]){
   					$true+=1;
   				}
   			}
   			$tip="";
   			$total_time=round($total_time,4);
   			$tip.=$true." out of ".$size." string decoded in ".$total_time." seconds"."<br>";
   			$tip.="The accuracy is ".(round(($true/$size),2)*100)."%";
   			include('/view/index.html');
	}

	if(isset($_POST['md5'])&&$_POST['md5']!=""){
		$md5=htmlspecialchars(trim($_POST['md5']));
        if(strlen($md5)!=32){
           $tip=md5($md5);
           include('view/index.html');
           die;
        }
		$sql="select source from dictionary where md5='$md5' limit 1";
		$t1=microtime(true);
        $rs=mysql_fetch_row(query($sql));
	    $res_arr=array();
      	$time_arr=array();
      	$data_arr=array();
   		if(empty($rs)){
   			$res=false;
   			array_push($res_arr,$res);
   			array_push($data_arr,$md5);
            array_push($time_arr,"null");
            $tip="no result";
   		
   		}else{
   			$time=microtime(true)-$t1;
   			array_push($time_arr,$time);
   			$res=true;
   			array_push($res_arr,$res);
   			$origin=$rs[0];
   			array_push($data_arr, $origin);
   			$tip="decoded in ".round($time,3)." seconds";
   		}
            include('view/index.html');
		
	}
	


}


 ?>