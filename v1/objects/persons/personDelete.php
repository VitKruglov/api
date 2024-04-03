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
			$query="UPDATE ".$pref.$this->table_name." SET id_group=2, cookie=NULL, remember_token=NULL, session_timeout=NULL, refresh_token=NULL, refresh_timeout=NULL WHERE id=$id";	
			$result=DBExecuteNew($this->conn, $query);

            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                //удаляем права
                $result2=DBExecuteNew($this->conn, "DELETE FROM ".$pref."permis_person WHERE id_person=$id");
                $this->result=array('result'=>'Ok', 'Id'=>$id);
            }
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>