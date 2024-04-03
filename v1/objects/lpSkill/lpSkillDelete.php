<?php
//-------------------------------------------------------------//
//                                                             //
//        удаление клиента по ID                             //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['session_id']))
        $session_id=$data['session_id'];
}

if(isset($this->formdata['idWorker']))
    $idWorker=$this->formdata['idWorker'];
elseif(isset($data['idWorker']))
    $idWorker=$data['idWorker'];

if(isset($this->formdata['idSkill']))
    $idSkill=$this->formdata['idSkill'];
elseif(isset($data['idSkill']))
    $idSkill=$data['idSkill'];

if(isset($this->formdata['idClient']))
    $idClient=$this->formdata['idClient'];
elseif(isset($data['idClient']))
    $idClient=$data['idClient'];

$result=array();

list($clients)=DBQueryNew($this->conn, "SELECT clients FROM ".$pref."global_requirements WHERE id=".$idSkill);
list($idWorker_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."lp_worker as w WHERE w.id=".$idWorker);

if(!isset($idSkill))
    $this->result=array('result'=>'Error', 'error'=>'Не указан навык');
elseif(!isset($idWorker))
    $this->result=array('result'=>'Error', 'error'=>'Не указан исполнитель');
elseif(!$idWorker_exist)
    $this->result=array('result'=>'Error', 'error'=>'Не найден исполнитель');
elseif(!isset($idClient) and $clients==1)
    $this->result=array('result'=>'Error', 'error'=>'Не указан клиент для клиентозависимого навыка');
else{
    if(!isset($idClient))
        $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$session_id.", state=0 WHERE id_worker=$idWorker and id_skill=$idSkill and state=1";
    else
        $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$session_id.", state=0 WHERE id_worker=$idWorker and id_skill=$idSkill and id_client=$idClient and state=1";

	$result=DBExecuteNew($this->conn, $query);

    if($result['error']){
        $this->result=array('error'=>$result['error'],'query'=>$query);
    }else{
        $this->result=array('result'=>'Ok', 'idWorker'=>$idWorker, 'idSkill'=>$idSkill);
    }
}




?>