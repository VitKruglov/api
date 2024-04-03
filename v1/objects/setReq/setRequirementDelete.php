<?php
//-------------------------------------------------------------//
//                                                             //
//        удаление расширенного или клиентозавимисого требования из набора                            //
//                                                             //
//-------------------------------------------------------------//

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
}

if(isset($this->formdata['idSet']))
    $idSet=$this->formdata['idSet'];
elseif(isset($data['idSet']))
    $idSet=$data['idSet'];

if(isset($this->formdata['idRequirement']))
    $idRequirement=$this->formdata['idRequirement'];
elseif(isset($data['idRequirement']))
    $idRequirement=$data['idRequirement'];


$result=array();
if(isset($idSet) and isset($idRequirement)){
        //проверяем, что нет закрытых заявок с таким набором 
        $result=DBFetchNew($this->conn, "SELECT r.state, r.dt_end FROM ".$pref."orders_requirements as rr, ".$pref."orders_requests as r 
        WHERE r.id=rr.id_request and rr.id_set=$idSet and r.state='n' and r.dt_end<NOW()");

        if(count($result)>0){
            $this->result=array('result'=>'Error', 'error'=>'Невозможно изменить набор требований, т.к. есть закрытые заявки с таким набором!');
        }else{
            $query="SELECT * FROM ".$pref."set_req WHERE id_set=$idSet and id_requirement=$idRequirement";

            $result=DBQueryNew($this->conn, $query);

            if($result['error'])
                $this->result=array('error'=>$result['error'],'query'=>$query);
            elseif(count($result)>0){
                //получаем название столбцов
                $query="DELETE FROM ".$pref."set_req WHERE id_set=$idSet and id_requirement=$idRequirement";	
                $result=DBExecuteNew($this->conn, $query);

                if($result['error']){
                    $this->result=array('error'=>$result['error'],'query'=>$query);
                }else{
                    $this->result=array('result'=>'Ok delete');
                }
            }else{
                $this->result=array('message'=>404);
            }
        }
}

?>