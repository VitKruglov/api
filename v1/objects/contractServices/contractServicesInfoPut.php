<?php
//-------------------------------------------------------------//
//                                                             //
//        изменение информации об услуге договора                //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

if(isset($this->postParam['input']['id'])){
    $id=$this->postParam['input']['id'];
    if($id>0){
        $this->result=array('id'=>$id);


        unset($this->postParam['input']['id']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//
        //проверка на соответствие Группы и объекта
        if(!isset($this->postParam['input']['idGroup']))
            $result['error']="400 Not found Group";
        elseif(!isset($this->postParam['input']['idObject']))
            $result['error']="400 Not found Object";
        elseif(!isset($this->postParam['input']['idDepartment']))
            $result['error']="400 Not found Department";
        if(isset($this->postParam['input']['idGroup']) and isset($this->postParam['input']['idObject'])){
            if($this->postParam['input']['idGroup']==0)
                $prov_object=1;
            elseif($this->postParam['input']['idGroup']>0 and $this->postParam['input']['idObject']>0){
                list($prov_object)=DBQueryNew($this->conn, "SELECT 1 FROM ".$pref."clients_object WHERE id_group=".$this->postParam['input']['idGroup']." and state=1 and id=".$this->postParam['input']['idObject']);
                if(!$prov_object)
                    $result['error']="400 Object does not match group.";
            }elseif($this->postParam['input']['idGroup']>0 and $this->postParam['input']['idObject']==0)
                $prov_object=1;

            if($this->postParam['input']['idObject']==0)
                $prov_department=1;
            elseif($this->postParam['input']['idObject']>0 and $this->postParam['input']['idDepartment']>0){
                list($prov_department)=DBQueryNew($this->conn, "SELECT 1 FROM ".$pref."clients_department WHERE id_object=".$this->postParam['input']['idObject']." and state=1 and id=".$this->postParam['input']['idDepartment']);
                if(!$prov_department)
                    $result['error']=$result['error']." 400 Department does not match object.";
            }elseif($this->postParam['input']['idObject']>0 and $this->postParam['input']['idDepartment']==0)
                $prov_department=1;
        }

        if($prov_object and $prov_department){
            $result=DBExecuteNew($this->conn, $query);

            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
                $result2=DBExecuteNew($this->conn, $query2);
                
                $this->result=array('result'=>'ok'); //, 'arr'=>$arr
            }
        }else
            $this->result=array('error'=>$result['error']);
    }else{
        $this->result=array('message'=>404);
    }
}
?>