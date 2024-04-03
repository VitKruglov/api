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


    if($this->session_id>0){
        $id=$this->session_id;
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //получаем название столбцов
			$query="UPDATE ".$pref.$this->table_name." SET cookie=null, remember_token=null, refresh_token=null, session_timeout=null, refresh_timeout=null WHERE id=$id";	
			$result=DBExecuteNew($this->conn, $query);

            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                $this->result=array('result'=>'Ok', 'session_id'=>$id);
            }
        }else{
            $this->result=array('message'=>404);
        }
    }





?>