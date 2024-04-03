<?php
//-------------------------------------------------------------//
//                                                             //
//              добавление расширенного или клиентозависимого требования к набору         //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

    $result=array();

    if(isset($this->postParam['hidden']))
        $hidden=$this->postParam['hidden'];
    elseif(isset($this->postParam['input']['hidden']))
        $hidden=$this->postParam['input']['hidden'];

    if(isset($this->postParam['required']))
        $required=$this->postParam['required'];
    elseif(isset($this->postParam['input']['required']))
        $required=$this->postParam['input']['required'];

    if(isset($this->postParam['idSet']))
        $idSet=$this->postParam['idSet'];
    elseif(isset($this->postParam['input']['idSet']))
        $idSet=$this->postParam['input']['idSet'];

    if(isset($this->postParam['idRequirement']))
        $idRequirement=$this->postParam['idRequirement'];
    elseif(isset($this->postParam['input']['idRequirement']))
        $idRequirement=$this->postParam['input']['idRequirement'];

    if(!$hidden) $hidden='NULL';
    if(!$required) $required='NULL';

    #$this->result=array('result'=>$this->postParam,'res'=>$this->formdata);

    

    //проверяем, что нет закрытых заявок с таким набором 
    $result=DBFetchNew($this->conn, "SELECT r.state, r.dt_end FROM ".$pref."orders_requirements as rr, ".$pref."orders_requests as r 
    WHERE r.id=rr.id_request and rr.id_set=".$idSet." and r.state=0 and r.dt_end<NOW()");
    if(count($result)>0){
        $this->result=array('result'=>'Error', 'error'=>'Невозможно изменить набор требований, т.к. есть закрытые заявки с таким набором!');
    }else{
        $query="INSERT INTO ".$pref."set_req SET id_set=".$idSet.", id_requirement=".$idRequirement.", hidden=$hidden, required=$required ON DUPLICATE KEY UPDATE hidden=$hidden, required=$required";

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{ 
            $this->result=array('result'=>'Ok');
        }
    }
    
?>