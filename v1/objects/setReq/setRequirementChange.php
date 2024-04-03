<?php
//-------------------------------------------------------------//
//                                                             //
//    привязка расширенных или клиентозависимых требований к заказу     //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

    $result=array();

    if(isset($this->postParam['idRequest']))
        $idRequest=$this->postParam['idRequest'];
    elseif(isset($this->postParam['input']['idRequest']))
        $idRequest=$this->postParam['input']['idRequest'];

    if(isset($this->postParam['requirements']))
        $reqs=$this->postParam['requirements'];
    elseif(isset($this->postParam['input']['requirements']))
        $reqs=$this->postParam['input']['requirements'];

    foreach($reqs as $k=>$val){
        if(!isset($val['hidden']))
            $reqs[$k]['hidden']=null;
        if(!isset($val['required']))
            $reqs[$k]['required']=null;
    }

 #   $this->result=array('result'=>$this->postParam,'res'=>$this->formdata);
    

    //проверяем все наборы
    $query="SELECT id_set, id_requirement, hidden, required FROM ".$pref."set_req";

    $result=DBFetchNew($this->conn, $query);

    for($i=0;$i<count($result);$i++){
        list($id_set,$id_requirement, $hidden, $required)=$result[$i];
        $arr[$id_set][$id_requirement]['use']=1;
        $arr[$id_set][$id_requirement]['hidden']=$hidden;
        $arr[$id_set][$id_requirement]['required']=$required;
    }

    #$this->result=array('result'=>$this->postParam);

    

    list($id_order_requirement)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."orders_requirements WHERE id_request=$idRequest");
    list($idRate)=DBQueryNew($this->conn, "SELECT id_rate FROM ".$pref."orders_requests WHERE id=$idRequest");

    foreach($arr as $id_set=>$val){
        if($val==$reqs){
            $s='К заказу '.$idRequest.' привязан набор требований '.$id_set;
            if($id_order_requirement>0)
                $result=DBExecuteNew($this->conn, "UPDATE ".$pref."orders_requirements SET id_set=$id_set WHERE id=$id_order_requirement");
            else
                $id_order_requirement=DBExecuteNew($this->conn, "INSERT INTO ".$pref."orders_requirements (id_set, id_request, state, id_person_change, dt_change) VALUES ($id_set, $idRequest, 1, ".$this->postParam['session_id'].", NOW())");
            $exist=1;
            $this->result=array('result'=>$s,'id_set'=>$id_set);
            break;
        }
    }

    if($exist!=1){
        //формируем название набора
        
        list($name)=DBQueryNew($this->conn, "SELECT concat(DATE_FORMAT(NOW(),'%Y-%m-%d %H:%i'),' ', o.name,' ',s.name,' / ', d.name) FROM ".$pref."clients_rate as r, ".$pref."clients_department as d , ".$pref."speciality as s, ".$pref."clients_object as o WHERE r.id=$idRate and d.id=r.id_department and s.id=r.id_speciality and o.id=d.id_object");

        //создаем новый набор
        $id_set=DBExecuteNew($this->conn, "INSERT INTO ".$pref."set_requirements (name, id_person_change,dt_change) VALUES ('$name', ".$this->postParam['session_id'].", NOW())");
        foreach($reqs as $id_requirement=>$val){
            if($val['use']==1){
                DBExecuteNew($this->conn, "INSERT INTO ".$pref."set_req (id_set, id_requirement) VALUES ($id_set, $id_requirement)");
                if($val['hidden']==1 or $val['hidden']==0)
                    DBExecuteNew($this->conn, "UPDATE ".$pref."set_req SET hidden=".$val['hidden']." WHERE id_set=$id_set and id_requirement=$id_requirement");
                if($val['required']==1 or $val['required']==0)
                    DBExecuteNew($this->conn, "UPDATE ".$pref."set_req SET required=".$val['required']." WHERE id_set=$id_set and id_requirement=$id_requirement");
            }
        }
        $s='К заказу '.$idRequest.' привязан новый набор требований '.$id_set;
        if($id_order_requirement>0)
            $result=DBExecuteNew($this->conn, "UPDATE ".$pref."orders_requirements SET id_set=$id_set WHERE id=$id_order_requirement");
        else
            $id_order_requirement=DBExecuteNew($this->conn, "INSERT INTO ".$pref."orders_requirements (id_set, id_request, state, id_person_change, dt_change) VALUE ($id_set, $idRequest, 1, ".$this->postParam['session_id'].", NOW())");

        $this->result=array('result'=>$s,'new_id_set'=>$id_set);
        
    }
    

?>