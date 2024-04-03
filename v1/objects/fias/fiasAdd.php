<?php
//-------------------------------------------------------------//
//                                                             //
//        поиск адреса по строке через dadate                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}
if(isset($this->postParam['session_id'])){
    $session_id=$this->postParam['session_id'];
}
if(isset($this->postParam['fias_id'])){
    $fias_id=$this->postParam['fias_id'];
}elseif(isset($this->postParam['input']['fias_id'])){
    $fias_id=$this->postParam['input']['fias_id'];
}

if(isset($this->postParam['client_id'])){
    $client_id=$this->postParam['client_id'];
}elseif(isset($this->postParam['input']['client_id'])){
    $client_id=$this->postParam['input']['client_id'];
}

if(isset($this->postParam['object_id'])){
    $object_id=$this->postParam['object_id'];
}elseif(isset($this->postParam['input']['object_id'])){
    $object_id=$this->postParam['input']['object_id'];
}

if(isset($this->postParam['man_id'])){
    $man_id=$this->postParam['man_id'];
}elseif(isset($this->postParam['input']['object_id'])){
    $object_id=$this->postParam['input']['object_id'];
}


$res=array();
include_once '/opt/crom/www/lib/dadata.php';

$token = "771a680b1c53fcb5287ebc09b27639ef21b630fd";
$secret = "872bcced5e15bc279d5483c90e1c2623f2e54446";

$dadata = new Dadata($token, $secret);
$dadata->init();

// Стандартизовать адрес
$fields = array("query" => $fias_id, "count" => 1);
$result = $dadata->suggest("address", $fields);

$dadata->close();

if($result['suggestions'][0]){
    $val= $result['suggestions'][0]['data'];
    $query="INSERT INTO ".$pref."fias (`address`, `postal_code`,`federal_district`,`region_fias_id`,`region_with_type`,
    `area_fias_id`, `area_tith_type`,
    `city_fias_id`, `city_with_type`, `city_area`,`city_district_fias_id`, `city_district_with_type`,
    `street_fias_id`, `street_with_type`,
    `house_fias_id`,`house_type`,`house`,
    `geo_lat`,`geo_lon`, `level`,
    `json`,`fias_id`) VALUE (
    '".$result['suggestions'][0]['value']."', '".$val['postal_code']."','".$val['federal_district']."','".$val['region_fias_id']."','".$val['region_with_type']."',
    '".$val['area_fias_id']."', '".$val['area_tith_type']."',
    '".$val['city_fias_id']."','".$val['city_with_type']."', '".$val['city_area']."', '".$val['city_district_fias_id']."', '".$val['city_district_with_type']."',
    '".$val['street_fias_id']."', '".$val['street_with_type']."',
    '".$val['house_fias_id']."','".$val['house_type']."', '".$val['house']."',
    '".$val['geo_lat']."', '".$val['geo_lon']."', '".$val['fias_level']."','".json_encode($val,JSON_UNESCAPED_UNICODE)."', '".$val['fias_id']."')";

    $result=DBExecuteNew($this->conn, $query);

    if($result['error']){
        $this->result=array('error'=>$result['error'],'query'=>$query);
    }else{
        if($client_id>0){
            $query2="UPDATE ".$pref."clients SET id_fiass=$result WHERE id=".$client_id;
            $result2=DBExecuteNew($this->conn, $query2);
            if($result2['error'])
                $str=array('error'=>$result2['error'],'query2'=>$query2);
            else
                $str=array('client update'=>'Ok');
        }
        if($object_id>0){
            $query2="UPDATE ".$pref."clients_object SET id_fiass=$result, geolat=".$val['geo_lat'].", geolon=".$val['geo_lon']." WHERE id=".$object_id;
            $result2=DBExecuteNew($this->conn, $query2);
            if($result2['error'])
                $str=array('error'=>$result2['error'],'query2'=>$query2);
            else
                $str=array('object update'=>'Ok');
        }
        if($man_id>0){
            $query2="UPDATE ".$pref."lp_man SET id_fiass=$result, geolat='".$val['geo_lat']."', geolon='".$val['geo_lon']."' WHERE id=".$man_id;
            $result2=DBExecuteNew($this->conn, $query2);
            if($result2['error'])
                $str=array('error'=>$result2['error'],'query2'=>$query2);
            else
                $str=array('man update'=>'Ok');
        }        
        $this->result=array('result'=>'Ok', 'Id'=>$result)+$str;#+array('fias'=>$val);
    }
}  
?>