<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о клиенте по ID                 //
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
    if($id==0){
        $result=DBQueryNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." LIMIT 1");
        $id=$result[0];
    }
    if($id>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //получаем название столбцов
			$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
			$res_columns=DBFetchNew($this->conn, $query);
			$i=0;
			while (count($res_columns)>$i)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i]=strtolower($name_column);
    			$i++;
			}
            
            $res=array();

            foreach($result as $key=>$val)
                $res[$arr[$key]]=$val;  //html_entity_decode(


            //--------------------------------- таблица детализации----------------------------//
            list($id_worker)=DBQueryNew($this->conn, "SELECT w.id FROM ".$pref."lp_worker as w, ".$pref."lp_account as a, ".$pref."pay_pay as p WHERE p.id=$id and a.id=p.id_lp_account and w.id_man=a.id_man");

            $query="SELECT tbl1.id as id, 
            tbl1.dt as dt, 
            tbl2.name as state, 
            tbl3.name as id_type, 
            tbl4.name as id_worker,
            tbl1.sum as sum,
            tbl1.id_operation as id_operation,
            tbl1.name_operation as name_operation,
            tbl5.sum
                FROM ".$pref."pay_balance as tbl1 
                LEFT JOIN (SELECT '1' as id,'Да' as name UNION SELECT '2','Нет(удален)') as tbl2 ON tbl2.id=tbl1.state
                LEFT JOIN ".$pref."pay_type as tbl3 ON tbl3.id=tbl1.id_type
                LEFT JOIN (SELECT w.id as id ,concat(m.sname,' ',m.name) as name FROM ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE m.id=w.id_man) as tbl4 ON tbl4.id=tbl1.id_worker
                LEFT JOIN ".$pref."pay_detail as tbl5 ON tbl5.id_pay_balance=tbl1.id 
            WHERE tbl1.id_worker=$id_worker and (tbl1.sum!=tbl1.sum2 or (tbl1.sum=tbl1.sum2 and tbl5.id_pay_balance=tbl1.id))
            ORDER BY tbl1.dt";

            $result2=DBFetchNew($this->conn, $query);

            $ostatok=$res['sum'];
            foreach($result2 as $i=>$val){
                $ostatok=$ostatok-$val[8];
            } 

            foreach($result2 as $i=>$val){
                if($val[7]=='orders_shifts' and $val[6]>0){
                    list($result2[$i][6],$result2[$i][7])=DBQueryNew($this->conn, "SELECT concat('Смена №',s.id,' ',s.dt_begin,' - ',s.dt_end), o.name FROM ".$pref."orders_shifts as s, ".$pref."orders_requests as r, ".$pref."clients_object as o WHERE s.id=".$val[6]." and r.id=s.id_request and o.id=r.id_object");
                }
                if(($val[8]==NULL or $val[8]!=$val[5]) and $ostatok>0){
                    $ost_pay=$val[5]-$val[8];
                    if($ost_pay>$ostatok) $ost_pay=$ostatok;
                    $ostatok=$ostatok-$ost_pay;
                    $result2[$i][9]="<input type=\"text\" name=\"pay_sum[".$val[0]."]\" value=\"$ost_pay\" size=4> <input type=\"checkbox\" name=\"pay[".$val[0]."]\" id=\"pay[".$val[0]."]\" checked>";
                }else{
                    $result2[$i][9]='';
                }
            }                    
            
            //-----------------------------------------------------------------//

            $this->result=$res+array('detail'=>$result2);
            
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>