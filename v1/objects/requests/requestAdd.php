<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового клиента                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['id_person_change'])) unset($this->postParam['input']['id_person_change']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);


        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        //проверка на входящие данные
        //соответствие Заказа и объекта
        if($this->postParam['input']['idOrder']>0 and $this->postParam['input']['idObject']>0){
            list($id_order)=DBQueryNew($this->conn, "SELECT o.id FROM ".$pref."orders as o, ".$pref."clients_object as ob, ".$pref."clients_group  as g 
            WHERE o.id=".$this->postParam['input']['idOrder']." and ob.id=".$this->postParam['input']['idObject']." and g.id=ob.id_group and g.id_client=o.id_client");
            if($id_order==$this->postParam['input']['idOrder'])
                $prov_order=true;
            else
                $result['error']="400 Not found Order or Object";
        }else
            $result['error']="400 Not found Order or Object";

        //соответствие Заказа и ставки
        if($this->postParam['input']['idRate']>0 and $this->postParam['input']['idObject']>0){
            list($id_object)=DBQueryNew($this->conn, "SELECT d.id_object FROM ".$pref."clients_rate as r, ".$pref."clients_department as d 
            WHERE r.id=".$this->postParam['input']['idRate']." and d.id=r.id_department");
            if($id_object==$this->postParam['input']['idObject'])
                $prov_rate=true;
            else
                $result['error']="400 Not found Rate or Object";
        }else
            $result['error']="400 Not found Rate or Object";

        if($prov_order and $prov_rate)
            $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error']);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
            $result2=DBExecuteNew($this->conn, $query);
            
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>