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
    if(isset($data['idWorker']))
        $id_worker=$data['idWorker'];       
    if(isset($data['idSpeciality']))
        $id_speciality=$data['idSpeciality'];           
    elseif(isset($this->urlData) and !is_null ($this->urlData[0]))
        if(strpos($this->urlData[0],'_')!==null)
            list($id_speciality,$id_worker)=explode('_',$this->urlData[0]);
}

$result=array();

if($id_worker>0 and $id_speciality>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id_worker=$id_worker and id_speciality=$id_speciality";

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //получаем название столбцов
			$query="DELETE FROM ".$pref.$this->table_name." WHERE id_worker=$id_worker and id_speciality=$id_speciality";	

            $result=DBExecuteNew($this->conn, $query);


            if($result['error']){
                $this->result=array('error'=>$result['error'],'query'=>$query);
            }else{
                $this->result=array('result'=>'Ok');
            }
     
        }else{
            $this->result=array('message'=>404,'query'=>$query);
        }
}
?>