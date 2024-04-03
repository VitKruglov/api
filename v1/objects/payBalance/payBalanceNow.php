<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о текущем балансе               //
//                                                             //
//-------------------------------------------------------------//


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    if(isset($data['search'])){
            $search=$data['search'];
            foreach($search as $type=>$val)
                foreach($val as $name_search=>$val2)
                    if($val2!=NULL and $type=='int') $where = $where." AND ".$name_search."=".$val2;
                    elseif($val2!=NULL and $type=='text') $where = $where." AND ".$name_search." LIKE '%".$val2."%'";
    }
    $session_id=$data['session_id'];
    $id_worker=$data['id_worker'];
}


list($id_owner)=explode("_",$pref);


$result=array();

$query="SELECT sum(b.sum*t.type) FROM ".$pref."pay_balance as b,".$pref."lp_contract as c, ".$pref."pay_type as t WHERE c.id_worker=$id_worker and b.id_contract=c.id and t.id=b.id_type";
    
$result=DBQueryNew($this->conn, $query);

if($result['error'])
    $this->result=array('error'=>$result['error'],'query'=>$query);
elseif(count($result)>0){
    $this->result=array('balance'=>$result[0]);
}else{
    $this->result=array('message'=>404);
}

?>