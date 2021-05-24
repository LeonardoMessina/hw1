<?php
    require_once '../tools/dbconfig.php';

    $conn = mysqli_connect($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['name']) or die(mysqli_error($conn));

    $id=$_GET["id"];
    $width=$_GET["width"];
    $height=$_GET["height"];
    $type=$_GET["type"];

    $res=mysqli_query($conn, "SELECT lat,lon FROM museo m WHERE id=$id");
    if(!$res){
        echo mysqli_error($conn);
        exit;
    }
    $row = mysqli_fetch_object($res);
    mysqli_free_result($res);

    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://dev.virtualearth.net/REST/v1/Imagery/Map/Road/$row->lat,$row->lon/15?ml=OrdnanceSurvey&mapSize=$width,$height&format=jpeg&key=AjQZQ4PhE6Db3LxZlBgxJV3zVBcYDePgjRj2qFgX1NmIICMyBSf5LBVKRhoB5CfK&dcl=1&mapMetadata=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $info=json_decode(curl_exec($curl));
        $bbox=$info->resourceSets[0]->resources[0]->bbox;
    }

    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"http://dev.virtualearth.net/REST/v1/Traffic/Incidents/$bbox[0],$bbox[1],$bbox[2],$bbox[3]/false?severity=1,2,3,4&c=it&type=1,2,3,4,5,6,7,8,9,10,11&key=AjQZQ4PhE6Db3LxZlBgxJV3zVBcYDePgjRj2qFgX1NmIICMyBSf5LBVKRhoB5CfK");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $trafficMap=json_decode(curl_exec($curl));
    }

    if($type=="text"){
        $result=array();
        for($i=0;$i<count($trafficMap->resourceSets);$i++){
            $traffic=$trafficMap->resourceSets[$i];
            for($j=0;$j<count($traffic->resources);$j++){
                $resource=$traffic->resources[$j];
                $result[]=$resource->description;
            }
        }
        echo json_encode($result);
    }else{
        $url="https://dev.virtualearth.net/REST/v1/Imagery/Map/Road/$row->lat,$row->lon/15?ml=OrdnanceSurvey&pushpin=$row->lat,$row->lon;46&mapSize=$width,$height&format=jpeg&key=AjQZQ4PhE6Db3LxZlBgxJV3zVBcYDePgjRj2qFgX1NmIICMyBSf5LBVKRhoB5CfK&dcl=1";
        for($i=0;$i<count($trafficMap->resourceSets);$i++){
            $traffic=$trafficMap->resourceSets[$i];
            for($j=0;$j<count($traffic->resources);$j++){
                $resource=$traffic->resources[$j];
                $v1=$resource->point->coordinates[0];
                $v2=$resource->point->coordinates[1];
                $url=$url."&pushpin=$v1,$v2;17";
            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        header("Content-Type: image/jpg");
        
        echo curl_exec($curl);
    }
    mysqli_close($conn);
?>