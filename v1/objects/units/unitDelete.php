<?php
//-------------------------------------------------------------//
//                                                             //
//        удаление группы пользователя по ID                             //
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
			$query="DELETE FROM ".$pref.$this->table_name." WHERE id=$id";	
			$result=DBExecuteNew($this->conn, $query);

            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                //удаляем права
                $result2=DBExecuteNew($this->conn, "DELETE FROM ".$pref."permis_unit WHERE id_unit=$id");
                $this->result=array('result'=>'Ok', 'Id'=>$id);
            }
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>