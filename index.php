<?php
	session_start();
  if(@$_GET['logout']){
    session_destroy();
    header("Location: login.php");
    exit();
  }
  if(@$_SESSION['logged'] == false){
    header("Location: login.php");
    exit();
  }
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
	require_once('lib/datastore/dbConnect.php');
	$status = 0;
	
	//print_r($obj_delete_lead);
		if(@$_GET['action']=="refresh"){
		$check = "<?php \$done=0;\$last_purged=0; ?>";
		file_put_contents('logs/check.php', $check);
		file_put_contents('logs/arr_csv.txt', "");
	}
	function csvToArray($file, $delimiter) 
	{
	  if (($handle = fopen($file, 'r')) !== FALSE) {
	    $i = 0; 
	    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
	      for ($j = 0; $j < count($lineArray); $j++) {
	        $arr[$i][$j] = $lineArray[$j]; 
	      }
	      $i++; 
	    } 
	    fclose($handle); 
	  }
	  return $arr; 
	}

	function getCsvRows($fileName){
		$feed = "imports/".$fileName;
		$keys = array();
		$newArray = array();

		$data = csvToArray($feed, ',');
		$count = count($data) - 1;
		$labels = array_shift($data);  

		foreach ($labels as $label) {
		  $keys[] = $label;
		}

		for ($j = 0; $j < $count; $j++) {
		  $d = array_combine($keys, $data[$j]);
		  $newArray[$j] = $d;
		}
		return $newArray;
	}
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="../js/dataTables/dataTables.bootstrap.min.css">
</head>
<style type="text/css">
	#import_link:hover, #export_link:hover, #export_link:hover, #logout:hover, #refresh:hover{
		text-decoration: none;
		cursor: pointer;
		text-shadow:2px 2px 5px silver;
	}
	#disabled_import:hover{
		cursor: not-allowed;
		text-decoration: none;
	}
	#check_btn, #purge_btn{
		width: 100%;
	}
	#main{
		padding:1em;
	}
	.rem{
		color:red;
	}
	.hidden{
		display: none !important;
	}
	.matched{
		color:red;
	}
	.appr{
		color:green;
	}
</style>
<body>
	<div class="row" style="border-bottom:solid maroon 3px">
		<div class="col-md-12" style="background-color:dimgray">
			<br>
			<br>
		</div>
	</div>
	<div id="main" class="row">
		<div class="col-md-3 text-center">
			<br><br>
			<form method="POST" name="select_file" enctype="multipart/form-data">
					<input id="import_file" name="import_file" onChange="change();" type="file" class="hidden"> 
					<input type="submit" id="add_btn" name="add_btn" class="hidden btn btn-primary" value="Add Records" disabled="true" onClick="return confirm('Note: Purging may take 3-5 minutes. Are you sure you want to Proceed?');">
					<h3>
					<input type="submit" id="check_btn" name="check_btn" class="btn btn-success" value="Check Category" disabled="true">
					</h3>
					<h3>
					<input type="submit" id="purge_btn" name="purge_btn" class="btn btn-danger" value="Check for Duplicate" disabled="true" osnClick="return confirm('Note: Purging may take 3-5 minutes. Are you sure you want to Proceed?');">
					</h3>
				</form>
	
			
		</div>
		<div class="col-md-8" style="">
			<br>
			<div class="row">
				<a href="?action=refresh" id="refresh" name="refresh" class="pull-right" ><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span><strong> Reset &nbsp;&nbsp;&nbsp;</strong></a>
				<a id="logout" class="pull-right" href="?logout=1"><span class="glyphicon glyphicon-off" aria-hidden="true"></span><strong> Logout &nbsp;&nbsp;&nbsp;</strong></a>
				<a id="export_link" class="pull-right" ><span class="glyphicon glyphicon-cog" aria-hidden="true"></span><strong> Settings &nbsp;&nbsp;&nbsp;</strong></a>
				<a id="disabled_import" class="hidden"><span class="glyphicon glyphicon-import" aria-hidden="true"></span><strong> Import &nbsp;&nbsp;&nbsp;</strong></a>
				<a id="import_link" class="pull-right" onClick="import_file();"><span class="glyphicon glyphicon-import" aria-hidden="true"></span><strong> Import &nbsp;&nbsp;&nbsp;</strong></a>
		<!--
			<h3 class="text-info">
				<a id="export_link" ><span class="glyphicon glyphicon-export" aria-hidden="true"></span><strong>Export</strong></a>
			</h3>
			<h3 class="text-info">
				<a id="mp_link"><span class="glyphicon glyphicon-import" aria-hidden="true"></span><strong>Manual Purge</strong></a>
			</h3>
		-->
					<strong><span id="file_name"></span></strong>
						<span id="cooking" style="color:gray" class="">
							<img src="assets/img/cooking.gif" style="height:20px"> cooking. . .
						</span>
			</div>
			<br>
			<div class="row well">
				<div id="logs" style="overflow-y: scroll; overflow-x: hidden; height: 400px; ">

					<?php
						$done = 0;
						$txt = "";
						/*
						$check = "<?php \$done=0;\$last_purged=0; ?>";
						file_put_contents('logs/check.php', $check);
						*/
						//$aa = file_get_contents("logs/arr_csv.txt");
						if(isset($_POST['check_btn'])){
							$image_tmp = $_FILES['import_file']['tmp_name'];
							$file_name = $_FILES['import_file']['name'];
							$match = 0;
							if(isset($image_tmp)){
								try{
									if(strpos($file_name, '.csv') !== false){
										$move = move_uploaded_file($image_tmp, "imports/".$file_name);
										if($move){

										}else{
											echo "Please close the file first to proceed.";
										}
										$newArray = getCsvRows($file_name);

										//file_put_contents('logs/arr_csv.txt', json_encode($newArray));
										//print_r(json_encode($newArray));

										$key = array_keys($newArray);
										$last_row = end($key);
										//echo $last_row."<br>";

										while (ob_get_level() > 0)
										    ob_end_flush();
										$match = 0;
										$added = 0;
										for($i=0;$i<=$last_row;$i++){		
											if(isset($newArray[$i]['biz_phone'])){
												//$aa = file_get_contents("logs/check.txt");
										?>
											<script type="text/javascript">
													var objDiv = document.getElementById("logs");
													objDiv.scrollTop = objDiv.scrollHeight;
											</script>
										<?php			  
										  $cnt = $i + 2;
										  $string = $newArray[$i]['biz_phone'];
										  if(empty($string) || $string==" " || $string==""){
										  	$string="emptyfield";
										  }else{
										  	$sc = array("-","(",")"," ","  ");
										  	$string = str_replace($sc, "", $string);
										  }
										$biz_name = $newArray[$i]['biz_name'];
										$biz_email = $newArray[$i]['biz_email'];
										$biz_city = $newArray[$i]['biz_city'];
										$biz_state = $newArray[$i]['biz_state'];
										$biz_zip = $newArray[$i]['biz_zip'];

											//print_r($obj_delete_lead->getKeyId());
										      try {
												//echo $biz_name."<br>";
										        $exist = 0;
										        require_once('check/chains&franchise&big_biz.php');
										        $check_cfb = $biz_name;
												$check_cfb = preg_replace('/\s+/', '', $check_cfb);
												$check_cfb = strtolower($check_cfb);
										        $c = 2;
										        $d = 2;
										        $aa = 0;
										        $aaa = 0;
												foreach ($newArray3 as $obj) {
													//echo $obj['keywords'];
													$cfb = preg_replace('/\s+/', '', $obj['chains&franchise&big_biz']);
													$cfb = strtolower($cfb);
													if (strpos($check_cfb, $cfb) !== false) {
														if(strlen($check_cfb) == strlen($cfb)){	
														$exist = 1;
														$aa = $c;
														}
													    //echo 'true - '.$check_cfb.'='.$cfb.' <br>';
													}else{
														//echo 'false<br>';
													}
													$c++;
												}
												require_once('check/banks&financial_firms.php');
												$check_bf = $biz_name;
												$check_bf = preg_replace('/\s+/', '', $check_bf);
												$check_bf = strtolower($check_bf);

												foreach ($newArray4 as $obj2) {
													//echo $obj['keywords'];
													$bf = preg_replace('/\s+/', '', $obj2['banks&financial_firms']);
													$bf = strtolower($bf);
													if (strpos($check_bf, $bf) !== false) {
														if(strlen($check_bf)==strlen($bf)){
														$exist = 1;
														$aaa = $d;
														}
													    //echo 'true - '.$check_cfb.'='.$cfb.' <br>';
													}else{
														//echo 'false<br>';
													}
													$d++;
												}
										        /*
												require_once('check/bad_data.php');
										        //$text = 'sexy Grill';
												foreach ($newArray2 as $obj2) {
													//echo $obj['keywords'];
													$check_bn = strtolower($biz_name);
													$keywords = " ".strtolower($obj2['keywords'])." ";
													$keywords_s = strtolower($obj2['keywords'])." ";
													$keywords_e = " ".strtolower($obj2['keywords']);
													$keywords_all = strtolower($obj2['keywords']);
													if (strpos($check_bn, $keywords) !== false) {
													    //echo '<span class="rem">'.$biz_name.' matched with '.$obj2['keywords'].'</span><br>';
													    $exist = 1;
													    //echo "center<br>";
													}else if (strpos($check_bn, $keywords_s) !== false) {
													    //echo '<span class="rem">'.$biz_name.' matched with '.$obj2['keywords'].'</span><br>';
													    $exist = 1;
													    //echo "start<br>";
													}else if (strpos($check_bn, $keywords_e) !== false) {
													    //echo '<span class="rem">'.$biz_name.' matched with '.$obj2['keywords'].'</span><br>';
													    $exist = 1;
													    //echo "last<br>";
													}else if (strpos($check_bn, $keywords_all) !== false) {
														if(strlen($check_bn) == strlen($keywords_all)){
													    //echo '<span class="rem">'.$biz_name.' matched with '.$obj2['keywords'].'</span><br>';
													    $exist = 1;
													    //echo "last<br>";
													}
													}else{
														//echo $biz_name.' does not matched with '.$obj2['keywords'].' <br>';
													}
												}
												*/
												if($exist == 1){
													$match += 1;
													echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.',cfb='.$aa.',bf='.$aaa.'</span><br>';
												}else{
													$added += 1;
													echo '<span class="appr"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> '.$biz_name.'</span><br>';
												}
										            flush();
										            $check = 0;
										            $last_num = $i + 1;
													$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
													//file_put_contents('logs/check.php', $check);


										            /*
													$check = "<?php \$last_purged=".$i."; ?>";
													file_put_contents('logs/check.php', $check);
													$path = dirname(__FILE__).'/logs/check.php';
													$fp = fopen($path, 'a');
													fwrite($fp, $check);
													fclose($fp);
													*/


										      } catch (Exception $e) {
										          echo "Unable to delete ".$string."($cnt) <br>";
										          $last_num = $i + 1;
												$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
												//file_put_contents('logs/check.php', $check);
										      }
										      	}else{
										      		//$i = $last_row +1;
										      		echo "<strong>Warning: </strong>'biz_phone' column not found.<br>";
										      	}
										}
										$done = 1;
										if($done==1){
											$check = "<?php \$last_purged=0; ?>";
											//file_put_contents('logs/check.php', $check);
										}
										echo "<br>********************************************<br>";
										//echo "<strong class='appr'>".$added." record(s) has been added</strong><br>";
										echo "<strong class='matched'>".$match." record(s) has been removed</strong><br><br><br><br><br>";
									}else{
										echo "not a csv bruh";
									}
								}catch(Exception $e){
									echo "<strong>Error:</strong>".$e->getMessage();
									//$myfile = file_put_contents('logs/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
									//echo "<strong>Warning:</strong>Unable to import file. Close the file and try again";
									$last_num = $i + 1;
									$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
									//file_put_contents('logs/check.php', $check);
								}
							}else{
								echo "not set";
							}

						}else{
							echo "<h4 style='color:gray'>Dataschef on Standby ...</h4>";
							?>
								<script type="text/javascript">
									document.getElementById("cooking").className = "hidden";
								</script>
							<?php
						}

						if(isset($_POST['purge_btn'])){
							$image_tmp = $_FILES['import_file']['tmp_name'];
							$file_name = $_FILES['import_file']['name'];
							$match = 0;
							if(isset($image_tmp)){
								try{
									if(strpos($file_name, '.csv') !== false){
										$move = move_uploaded_file($image_tmp, "imports/".$file_name);
										if($move){

										}else{
											echo "Please close the file first to proceed.";
										}
										$feed = "imports/".$file_name;
										$keys = array();
										$newArray = array();

										$data = csvToArray($feed, ',');
										$count = count($data) - 1;
										$labels = array_shift($data);  

										foreach ($labels as $label) {
										  $keys[] = $label;
										}

										for ($j = 0; $j < $count; $j++) {
										  $d = array_combine($keys, $data[$j]);
										  $newArray[$j] = $d;
										}

										//file_put_contents('logs/arr_csv.txt', json_encode($newArray));
										//print_r(json_encode($newArray));

										$key = array_keys($newArray);
										$last_row = end($key);
										//echo $last_row."<br>";

										while (ob_get_level() > 0)
										    ob_end_flush();
										$match = 0;
										for($i=0;$i<=$last_row;$i++){	
										$status = 1;	
											if(isset($newArray[$i]['biz_phone'])){
												//$aa = file_get_contents("logs/check.txt");
										?>
											<script type="text/javascript">
													var objDiv = document.getElementById("logs");
													objDiv.scrollTop = objDiv.scrollHeight;
											</script>
										<?php			  
										  $cnt = $i + 2;
										  $string = $newArray[$i]['biz_phone'];
										  if(empty($string) || $string==" " || $string==""){
										  	$string="emptyfield";
										  }else{
										  	$sc = array("-","(",")"," ","  ");
										  	$string = str_replace($sc, "", $string);
										  }
										$biz_name = $newArray[$i]['biz_name'];
										$biz_email = $newArray[$i]['biz_email'];
										$biz_city = $newArray[$i]['biz_city'];
										$biz_state = $newArray[$i]['biz_state'];
										$biz_zip = $newArray[$i]['biz_zip'];

											//print_r($obj_delete_lead->getKeyId());
										      try {
												$obj_delete_lead = $obj_gateway_lead->fetchAll("SELECT * FROM lead WHERE biz_name='$biz_name'");
													//foreach ($obj_delete_lead as $obj) {
												//echo $string."<br>";
												//print_r($obj_delete_lead);
												if(!empty($obj_delete_lead)){
														foreach ($obj_delete_lead as $obj) {
															//echo $obj->biz_name;
												//echo $obj->biz_phone."<br>";
															//echo $obj->biz_name;
															if($obj->biz_name=="".$biz_name."" && $obj->biz_phone=="".$string.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else if($obj->biz_name=="".$biz_name."" && $obj->biz_email=="".$biz_email.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else if($obj->biz_name=="".$biz_name."" && $obj->biz_city=="".$biz_city."" && $obj->biz_state=="".$biz_state."" && $obj->biz_zip=="".$biz_zip.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else{
																echo "Unable to Remove ".$biz_name."($cnt) <br>";
															} 
														}
												}else{
													echo '<span class="appr"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> '.$biz_name.'</span><br>';
												}

														//print_r($obj->getKeyId());
													//}
										          	//$result = $obj_gateway_lead->delete($obj_delete_lead);
										            //echo $string." DELETED($cnt) <br>";
										      		
										            
										            flush();
										            $check = 0;
										            $last_num = $i + 1;
													$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
													file_put_contents('logs/check.php', $check);


										            /*
													$check = "<?php \$last_purged=".$i."; ?>";
													file_put_contents('logs/check.php', $check);
													$path = dirname(__FILE__).'/logs/check.php';
													$fp = fopen($path, 'a');
													fwrite($fp, $check);
													fclose($fp);
													*/


										      } catch (Exception $e) {
										          echo "Unable to delete ".$string."($cnt) <br>";
										          $last_num = $i + 1;
												$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
												file_put_contents('logs/check.php', $check);
										      }
										      	}else{
										      		//$i = $last_row +1;
										      		echo "<strong>Warning: </strong>'biz_phone' column not found.<br>";
										      	}
										}
										$done = 1;
										if($done==1){
											$check = "<?php \$last_purged=0; ?>";
											file_put_contents('logs/check.php', $check);
										}
										echo "<br>********************************************<br>";
										//echo "<strong class='appr'>".$added." record(s) has been added</strong><br>";
										echo "<strong class='matched'>".$match." record(s) has been removed</strong><br><br><br><br><br>";
									}else{
										echo "not a csv bruh";
									}
								}catch(Exception $e){
									echo "<strong>Error:</strong>".$e->getMessage();
									//$myfile = file_put_contents('logs/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
									//echo "<strong>Warning:</strong>Unable to import file. Close the file and try again";
									$last_num = $i + 1;
									$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$match.";\$file_name='".$file_name."'; ?>";
									file_put_contents('logs/check.php', $check);
								}
							}else{
								echo "not set";
							}
						}

						require_once('logs/check.php');
						if(isset($_POST['continue_btn'])){
							//require_once("logs/arr_csv.txt");
							try{
							//$arrr = file_get_contents("logs/arr_csv.txt");
							//$newArray = json_decode($arrr);
							//print_r($newArray[1]->PHONE_NUMBER);
							//print_r(json_encode($newArray[0]['PHONE_NUMBER']));
							$feed = "imports/".$file_name;
										//$keys = array();
										//$newArray = array();

										$data = csvToArray($feed, ',');
										$count = count($data) - 1;
										$labels = array_shift($data);  

										foreach ($labels as $label) {
										  $keys[] = $label;
										}

										for ($j = 0; $j < $count; $j++) {
										  $d = array_combine($keys, $data[$j]);
										  $newArray[$j] = $d;
										}

							while (ob_get_level() > 0)
										    ob_end_flush();
										$match = 0;
										$last_purged = $last_purged - 1;
										for($i=$last_purged;$i<=$last_row;$i++){	
										?>
											<script type="text/javascript">
													var objDiv = document.getElementById("logs");
													objDiv.scrollTop = objDiv.scrollHeight;
											</script>
										<?php	
											if(isset($newArray[$i]['biz_phone'])){
												//$aa = file_get_contents("logs/check.txt");			  
										  $cnt = $i + 2;
										  $string = $newArray[$i]['biz_phone'];
										  if(empty($string) || $string==" " || $string==""){
										  	$string="emptyfield";
										  }else{
										  	$sc = array("-","(",")"," ","  ");
										  	$string = str_replace($sc, "", $string);
										  }
										  	$biz_name = $newArray[$i]['biz_name'];
											$biz_email = $newArray[$i]['biz_email'];
											$biz_city = $newArray[$i]['biz_city'];
											$biz_state = $newArray[$i]['biz_state'];
											$biz_zip = $newArray[$i]['biz_zip'];
										      try {
										          	$obj_delete_lead = $obj_gateway_lead->fetchAll("SELECT * FROM lead WHERE biz_name='$biz_name'");
													//foreach ($obj_delete_lead as $obj) {
												//echo $string."<br>";
												//print_r($obj_delete_lead);
												if(!empty($obj_delete_lead)){
														foreach ($obj_delete_lead as $obj) {
															//echo $obj->biz_name;
												//echo $obj->biz_phone."<br>";
															//echo $obj->biz_name;
															if($obj->biz_name=="".$biz_name."" && $obj->biz_phone=="".$string.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else if($obj->biz_name=="".$biz_name."" && $obj->biz_email=="".$biz_email.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else if($obj->biz_name=="".$biz_name."" && $obj->biz_city=="".$biz_city."" && $obj->biz_state=="".$biz_state."" && $obj->biz_zip=="".$biz_zip.""){
															//echo $obj->biz_phone."<br>";
																//$obj_gateway_lead->delete($obj);
																echo '<span class="rem"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> '.$biz_name.'</span><br>';
																$match += 1;
															}else{
																echo "Unable to Remove ".$biz_name."($cnt) <br>";
															} 
														}
												}else{
													echo '<span class="appr"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> '.$biz_name.'</span><br>';
												}
										            flush();
										            $check = 0;

										            /*
													$check = "<?php \$last_purged=".$i."; ?>";
													file_put_contents('logs/check.php', $check);
													$path = dirname(__FILE__).'/logs/check.php';
													$fp = fopen($path, 'a');
													fwrite($fp, $check);
													fclose($fp);
													*/

													$last_num = $i + 1;
													$current_matched = $matched + $match;
													$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$current_matched.";\$file_name='".$file_name."'; ?>";
													file_put_contents('logs/check.php', $check);

										      } catch (DynamoDbException $e) {
										          echo "Unable to delete ".$string."($cnt) <br>";
										    	  $last_num = $i + 1;
													$current_matched = $matched + $match;
													$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$current_matched.";\$file_name='".$file_name."'; ?>";
													file_put_contents('logs/check.php', $check);
										      }
										      	}else{
										      		//$i = $last_row +1;
										      		echo "<strong>Warning: </strong>'biz_phone' column not found.<br>";
										      	}							  
										}
										$done = 1;
										$last_purged = 0;
										if($done==1){
											$check = "<?php \$last_purged=0; ?>";
											file_put_contents('logs/check.php', $check);
										}
										$tot = $matched + $match;
										echo "<br>********************************************<br>";
										//echo "<strong class='appr'>".$added." record(s) has been added</strong><br>";
										echo "<strong class='matched'>".$tot." record(s) has been removed</strong><br><br><br><br><br>";
									}catch(Exception $e){
									echo "<strong>Error:</strong>".$e->getMessage();
									//$myfile = file_put_contents('logs/logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
									//echo "<strong>Warning:</strong>Unable to import file. Close the file and try again";
									$last_num = $i + 1;
									$current_matched = $matched + $match;
									$check = "<?php \$last_purged=".$last_num.";\$last_row=".$last_row.";\$matched=".$current_matched.";\$file_name='".$file_name."'; ?>";
									file_put_contents('logs/check.php', $check);
									$last_purged = $i +1;
								}
						}
					?>
				</div>
			</div>
		<div class="row">
			<?php
			
			if($last_purged!=0){
			?>
				<form method="POST">
					<input type="submit" id="continue_btn" name="continue_btn" class="btn btn-danger" value="Last purge is at row <?php echo $last_purged; ?>, Click to continue Purging.">
				</form>
				<?php
			}
				/*
					if(isset($_POST['continue_btn'])){
						echo $done;
					}
					*/
				?>
		</div>
		</div>
	</div>
</body>
</html>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script src="../js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script src="../js/dataTables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="../js/angular.min.js"></script>
<script type="text/javascript">
$('.carousel').carousel({
    pause: "false"
});
	function change(){
		var file_name = $('#import_file').val().replace(/\\/g, '/').replace(/.*\//, '');
		document.getElementById('file_name').innerHTML = "File: <span style='color:red'><i>"+file_name+"</i></span>";
		if(file_name.indexOf('.csv') >= 0){
			document.getElementById("purge_btn").disabled = false;
			document.getElementById("check_btn").disabled = false;
		}else{
			document.getElementById("purge_btn").disabled = true;
			document.getElementById("check_btn").disabled = true;
		}
	}
	function import_file(){
		$('#import_file').click();
	}
	function cooking(){
		$('#cooking').removeClass('hidden');
	} 
var last_purged = <?php echo $last_purged; ?>;
	if(last_purged != 0){
		$('#import_link').addClass('hidden');
		$('#disabled_import').removeClass('hidden');
	}

</script>