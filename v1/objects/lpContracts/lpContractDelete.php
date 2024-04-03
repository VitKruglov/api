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
#    if(isset($data['id_worker']))
#        $id_worker=$data['id_worker'];       
#    if(isset($data['id_speciality']))
#        $id_speciality=$data['id_speciality'];                  
}

$result=array();

if (count($this->urlData)==1){
    $id=$this->urlData[0];
    if($id>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //получаем название столбцов
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$session_id.", state=0 WHERE id=".$id;
            
			$result=DBExecuteNew($this->conn, $query);

            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                $this->result=array('result'=>'Ok', 'Id'=>$id);
            }
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>