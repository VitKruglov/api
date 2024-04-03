<?php
//-------------------------------------------------------------//
//                                                             //
//        изменение информации о клиенте по ID                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

if((isset($this->postParam['input']['id']) or isset($this->postParam['input']['idWorker'])) and isset($this->postParam['input']['idSpeciality'])){
    if(isset($this->postParam['input']['id']))
        $id_worker=$this->postParam['input']['id'];
    elseif(isset($this->postParam['input']['idWorker']))
        $id_worker=$this->postParam['input']['idWorker'];
    $id_speciality=$this->postParam['input']['idSpeciality'];
    if($id_worker>0 and $id_speciality>0){
        $this->result=array('idWorker'=>$id_worker, 'idSpeciality'=>$id_speciality);

        unset($this->postParam['input']['id']);

        $query_where=" WHERE id_worker=".$id_worker." and id_speciality=".$id_speciality;

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}

?>