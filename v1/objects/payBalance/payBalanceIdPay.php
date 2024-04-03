<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о платежах исполнителя по его ID               //
//                                                             //
//-------------------------------------------------------------//

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    $session_id=$data['session_id'];
}
list($id_owner)=explode("_",$pref);

if (count($this->urlData)==1){
    $id=$this->urlData[0];
    if($id==0){
        $result=DBQueryNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." LIMIT 1");
        $id=$result[0];
    }
    if($id>0){

        list($id_worker)=DBQueryNew($this->conn, "SELECT w.id FROM ".$pref."lp_worker as w, ".$pref."lp_account as a, ".$pref."pay_pay as p WHERE p.id=$id and a.id=p.id_lp_account and w.id_man=a.id_man");

        $query="SELECT tbl1.id as id, 
        tbl1.dt as dt, 
        tbl2.name as state, 
        tbl3.name as id_type, 
        tbl4.name as id_worker,
        tbl1.sum as sum,
        tbl1.id_operation as id_operation,
        tbl1.name_operation as name_operation,
        tbl5.sum,
        if(tbl5.sum<tbl1.sum,1,0)
            FROM ".$pref.$this->table_name." as tbl1 
            LEFT JOIN (SELECT '1' as id,'Да' as name UNION SELECT '2','Нет(удален)') as tbl2 ON tbl2.id=tbl1.state
            LEFT JOIN ".$pref."pay_type as tbl3 ON tbl3.id=tbl1.id_type
            LEFT JOIN (SELECT w.id as id ,concat(m.sname,' ',m.name) as name FROM ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE m.id=w.id_man) as tbl4 ON tbl4.id=tbl1.id_worker
            LEFT JOIN ".$pref."pay_detail as tbl5 ON tbl5.id_pay_balance=tbl1.id 
        WHERE tbl1.id_worker=$id_worker and (tbl1.sum!=tbl1.sum2 or (tbl1.sum=tbl1.sum2 and tbl5.id_pay_balance=tbl1.id))
        ORDER BY tbl1.dt";
    
        $result=DBFetchNew($this->conn, $query);

        foreach($result as $i=>$val){
            if($val[7]=='orders_shifts' and $val[6]>0){
                list($result[$i][6],$result[$i][7])=DBQueryNew($this->conn, "SELECT concat('Смена №',s.id,' ',s.dt_begin,' - ',s.dt_end), o.name FROM ".$pref."orders_shifts as s, ".$pref."orders_requests as r, ".$pref."clients_object as o 
                WHERE s.id=".$val[6]." and r.id=s.id_request and o.id=r.id_object");
            }
            $sum_all=$sum_all+$val[8];
        }

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
     
            $this->result=$result+array('sum'=>$sum_all);
        }else{
            $this->result=array('message'=>404,'query'=>$query);
        }
    }
}

?>