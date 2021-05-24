<?php
    require_once '../tools/auth.php';

	if(!checkAuth())
		exit;
    
    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

	$result=array();
	$result["isError"]=false;
	$result["message"]="Backup effettuato con successo";

	$query = "call pBackupOpere({$_SESSION["id_museo"]});";
	if (!mysqli_query($conn, $query)){
		$result["isError"]=true;
		$result["message"]="Errore col database (".mysqli_errno($conn).": ".mysqli_error($conn).")";
	}else{
		$res=mysqli_query($conn, "SELECT * FROM backup WHERE museo={$_SESSION['id_museo']} order by data desc limit 1");
		while($row = mysqli_fetch_object($res)){
			$result["id"]=$row->id;
			$result["date"]=$row->data;
		}
		mysqli_free_result($res);
	}

    mysqli_close($conn);

	echo json_encode($result);
?>