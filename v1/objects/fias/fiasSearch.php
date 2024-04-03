<?php
//-------------------------------------------------------------//
//                                                             //
//        поиск адреса по строке через dadate                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['search']))
        $search=$data['search'];
    if(isset($data['fias_level']))
        $fias_level=$data['fias_level'];        
}


$res=array();
include_once '/opt/crom/www/lib/dadata.php';

$token = "771a680b1c53fcb5287ebc09b27639ef21b630fd";
$secret = "872bcced5e15bc279d5483c90e1c2623f2e54446";

$dadata = new Dadata($token, $secret);
$dadata->init();

// Стандартизовать адрес
$fields = array("query" => $search, "count" => 20);
$result = $dadata->suggest("address", $fields);

$dadata->close();

if(count($result['suggestions'])==0)
    $res['message']='Адрес не найден';
else
    foreach($result['suggestions'] as $k=>$val){
        if($fias_level){ 
            if($val['data']['fias_level']==$fias_level)
                $res[$val['data']['fias_id']]=$val['value'];
        }else
            $res[$val['data']['fias_id']]=$val['value'];

    }

$this->result=$res;

#$this->result=array('search1'=>$search);

?>