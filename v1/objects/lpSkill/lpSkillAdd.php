<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание новых базовых требований         //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

if(isset($this->postParam['idWorker'])){
    $idWorker=$this->postParam['idWorker'];
    $this->postParam['input']['idWorker']=$idWorker;
}elseif(isset($this->postParam['input']['idWorker']))
    $idWorker=$this->postParam['input']['idWorker'];

if(isset($this->postParam['idSkill'])){
    $idSkill=$this->postParam['idSkill'];
    $this->postParam['input']['idSkill']=$idSkill;
}elseif(isset($this->postParam['input']['idSkill']))
    $idSkill=$this->postParam['input']['idSkill'];

if(isset($this->postParam['idClient'])){
    $idClient=$this->postParam['idClient'];
    $this->postParam['input']['idClient']=$idClient;
}elseif(isset($this->postParam['input']['idClient']))
    $idClient=$this->postParam['input']['idClient'];
if($idClient<1) unset($idClient);


$result=array();

        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

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
                list($idSkill_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."lp_skill WHERE id_worker=$idWorker and id_skill=$idSkill and state=1");
            else
                list($idSkill_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."lp_skill WHERE id_worker=$idWorker and id_skill=$idSkill and id_client=$idClient and state=1");
            
            if($idSkill_exist>0){
                $this->result=array('result'=>'Ok', 'Id'=>$idSkill_exist);                
            }else{
                $result=DBExecuteNew($this->conn, $query);

                if($result['error']){
                    $this->result=array('error'=>$result['error'],'query'=>$query);
                }else{
                    $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
                    $result2=DBExecuteNew($this->conn, $query);
                    
                    $this->result=array('result'=>'Ok', 'Id'=>$result);
                }
            }
        }
        
?>