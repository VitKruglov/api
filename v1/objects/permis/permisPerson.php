<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о правах доступа              //
//                                                             //
//-------------------------------------------------------------//

$name_script="";
$name_script_info="";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    if(isset($data['search'])){
            $search=$data['search'];
            foreach($search as $type=>$val)
                foreach($val as $name_search=>$val2)
                    if($val2!=NULL and $type=='int') $where = $where." AND ".$name_search."=".$val2;
                    elseif($val2!=NULL and $type=='text') $where = $where." AND ".$name_search." LIKE '%".$val2."%'";
    }
    $session_id=$data['session_id'];
}
if (isset($this->urlData[0])){
    $id=$this->urlData[0];
    $isset_id=" WHERE id=$id";
    $isset_id2=" WHERE id_person=$id";
}

include("/opt/crom/vendor/autoload.php");
$yaml= new \Symfony\Component\Yaml\Yaml;

$swagger = $yaml->parseFile('v1/swagger.yaml');
$arr_name=array();
$arr_name['*']=array();

foreach ($swagger['paths'] as $function_name =>$val){
	$function_name=substr($function_name,1);
	foreach ($val as $methode_name =>$val2){
		$sss=explode("/",$function_name);
		$arr_name[$sss[0]][$sss[1]]['name']=$val2['summary'];
	}
}

$result_persons=DBFetchNew($this->conn, "SELECT id, realname FROM ".$pref."persons".$isset_id);

foreach($arr_name as $subname=>$val){
    foreach($val as $functionname=>$val2){
        for($i=0;$i<count($result_persons);$i++){
            $arr_name[$subname][$functionname]['data'][$result_persons[$i][0]]=NULL;
        }
    }
    for($i=0;$i<count($result_persons);$i++){
        $arr_name[$subname]['data'][$result_persons[$i][0]]=NULL;
    }
}

$result_permis=DBFetchNew($this->conn, "SELECT REPLACE(tbl1.api_name,'/*','') as api_name, tbl1.id_person as id_person, tbl1.type as type
FROM ".$pref."permis_person as tbl1".$isset_id2);

for($i=0;$i<count($result_permis);$i++){
    unset($fn);
    list($subname,$fn)=explode("/",$result_permis[$i][0]);
    if(!$fn)
        $arr_name[$subname]['data'][$result_permis[$i][1]]=$result_permis[$i][2];
    else
        $arr_name[$subname][$fn]['data'][$result_permis[$i][1]]=$result_permis[$i][2];
}

$person_name=array();
foreach($result_persons as $j => $b) $person_name=$person_name+array($b[0] => $b[1]);

$this->result=$arr_name+array('data'=>array('persons_name'=>$person_name));

?>