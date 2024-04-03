<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о выплате по ID                 //
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
        //получаем название столбцов
		$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
		$res_columns=DBFetchNew($this->conn, $query);
		$i=0;
		while (count($res_columns)>$i)
		{
    		list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
			$arr[$i]=strtolower($name_column);
            $res[strtolower($name_column)]='';
            $res['data_type'][strtolower($name_column)]['type']=$arr_type[$key];
    		$i++;
		}
        $arr_name=array('ID','Выплачено','Выплатил','Подтверждено','Подтвердил','Активный','Счет АК','Счет исполнителя','Сумма','Примечание');
        foreach($arr_name as $i=>$name)
            $res['data_type'][$arr[$i]]['name']=$name;

        //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_pay']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_pay']['table']=$res['data_type']['id_person_pay']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_pay']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_signature']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_signature']['table']=$res['data_type']['id_person_signature']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_signature']['readonly']='readonly';
  
            $a=DBFetchNew($this->conn, "SELECT 0, NULL UNION SELECT id, name FROM ".$pref."accounts");
            $res['data_type']['id_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_account']['table']=$res['data_type']['id_account']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT 0 as id, NULL as nam UNION SELECT a.id, concat(m.sname,' ',m.name,', ',if(a.bank_name is NULL or a.bank_name='','',a.bank_name),' ', if(a.card is NULL or a.card='',if(a.type=0,', наличные',''), concat(', ',a.card))) FROM ".$pref."lp_account as a, ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE w.id=a.id_worker and m.id=w.id_man ORDER BY nam");
            $res['data_type']['id_lp_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_lp_account']['table']=$res['data_type']['id_lp_account']['table']+array($b[0] => $b[1]);

            list($signature)=DBQueryNew($this->conn, "SELECT 1 FROM ".$pref."pay_pay WHERE id='$id' and id_person_signature>0 and dt_signature is not NULL");
            if(!$signature){
                $res['data_type']['dt_signature']['type']='button';
                $res['dt_signature']='Подтверждено';
            }
            
            $this->result=$res;
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
                $arr_type[$i]=$type_column;
    			$i++;
			}
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            //--------------------------------- таблица детализации----------------------------//
            $arr[$i]='detail';
            list($id_worker)=DBQueryNew($this->conn, "SELECT w.id FROM ".$pref."lp_worker as w, ".$pref."lp_account as a, ".$pref."pay_pay as p WHERE p.id=$id and a.id=p.id_lp_account and w.id=a.id_worker");

            $query="SELECT tbl1.id as id, 
            tbl1.dt as dt, 
            tbl2.name as state, 
            tbl3.name as id_type, 
            concat(tbl7.name,' ',tbl6.sname,' ',tbl6.name) as id_contract,
            tbl1.sum*tbl3.type as sum,
            tbl1.id_operation as id_operation,
            tbl1.name_operation as name_operation,
            sum(tbl8.sum),
            tbl8.id_pay_pay
                FROM ".$pref."pay_balance as tbl1 
                LEFT JOIN (SELECT 1 as id,'Да' as name UNION SELECT 0,'Нет(удален)') as tbl2 ON tbl2.id=tbl1.state
                LEFT JOIN ".$pref."pay_type as tbl3 ON tbl3.id=tbl1.id_type
                LEFT JOIN ".$pref."lp_contract as tbl4 ON tbl4.id=tbl1.id_contract
                LEFT JOIN ".$pref."lp_worker as tbl5 ON tbl5.id=tbl4.id_worker
                LEFT JOIN ".$pref."lp_man as tbl6 ON tbl6.id=tbl5.id_man
                LEFT JOIN ".$pref."type_contract as tbl7 ON tbl7.id=tbl4.type   
                LEFT JOIN ".$pref."pay_detail as tbl8 ON tbl8.id_pay_balance=tbl1.id 
            WHERE tbl1.state=1 and tbl1.dt_signature IS NOT NULL and tbl1.id_person_signature>0 and tbl4.id_worker=$id_worker and (tbl1.sum!=tbl1.sum_paid or (tbl1.sum=tbl1.sum_paid and tbl8.id_pay_balance=tbl1.id and tbl8.id_pay_pay=$id))
            GROUP BY tbl1.id, tbl8.id_pay_pay
            ORDER BY tbl1.dt";


            $result2=DBFetchNew($this->conn, $query);
            if($result2['error'])
                $this->result=array('error'=>$result2['error'],'query'=>$query);

            $ostatok=$res['sum'];
            foreach($result2 as $i=>$val){
                if($val[9]==$id)
                    $ostatok=$ostatok-$val[8];
                elseif($val[9]>0)
                    unset($result2[$i]);
            } 

            foreach($result2 as $i=>$val){
                list($prov_pay_all)=DBQueryNew($this->conn, "SELECT if(sum=sum2,1,0) FROM ".$pref."pay_balance WHERE id=".$val[0]);
                if($val[7]=='orders_shifts' and $val[6]>0){
                    list($result2[$i][6])=DBQueryNew($this->conn, "SELECT concat('Смена №',s.id,' ',s.dt_begin,' - ',s.dt_end,' ',o.name) FROM ".$pref."orders_shifts as s, ".$pref."orders_requests as r, ".$pref."clients_object as o WHERE s.id=".$val[6]." and r.id=s.id_request and o.id=r.id_object");
                }
                if(($val[8]==NULL or $val[8]!=$val[5]) and $ostatok>0 and $prov_pay_all!=1){
                    $ost_pay=$val[5]-$val[8];
                    if($ost_pay>$ostatok) $ost_pay=$ostatok;
                    $ostatok=$ostatok-$ost_pay;
                    $result2[$i][9]="<input type=\"text\" name=\"pay_sum[".$val[0]."]\" value=\"$ost_pay\" size=4> <input type=\"checkbox\" name=\"pay[".$val[0]."]\" id=\"pay[".$val[0]."]\" checked>";
                }else{
                    if(!$result2[$i][8] and !$result2[$i][9]){
                        unset($result2[$i]);
                    }else
                        $result2[$i][9]='';
                }
                if(isset($result2[$i])){
                    $result2[$i][7]=$result2[$i][8];
                    $result2[$i][8]=$result2[$i][9];
                }
                unset($result2[$i][9]);
            }            
            
            $res['detail']=$result2;
            $res['detail']=$res['detail']+array('name'=>array('№','Дата','Активен','Операция','Договор','Сумма','За что','Уже учтено','Учесть'));
            $res['data_type']['detail']['type']='table';            
            //-----------------------------------------------------------------//

            $arr_name=array('ID','Выплачено','Выплатил','Подтверждено','Подтвердил','Активный','Счет АК','Счет исполнителя','Сумма','Примечание','Оплата платежей');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            $res['data_type']['dt_pay']['readonly']='readonly';
            $res['data_type']['dt_signature']['readonly']='readonly';
     
            
            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_pay']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_pay']['table']=$res['data_type']['id_person_pay']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_pay']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_signature']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_signature']['table']=$res['data_type']['id_person_signature']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_signature']['readonly']='readonly';
  
            $a=DBFetchNew($this->conn, "SELECT 0, NULL UNION SELECT id, name FROM ".$pref."accounts");
            $res['data_type']['id_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_account']['table']=$res['data_type']['id_account']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT 0 as id, NULL as nam UNION SELECT a.id, concat(m.sname,' ',m.name,', ',if(a.bank_name is NULL or a.bank_name='','',a.bank_name),' ', if(a.card is NULL or a.card='',if(a.type=0,', наличные',''), concat(', ',a.card))) FROM ".$pref."lp_account as a, ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE w.id=a.id_worker and m.id=w.id_man ORDER BY nam");
            $res['data_type']['id_lp_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_lp_account']['table']=$res['data_type']['id_lp_account']['table']+array($b[0] => $b[1]);

            list($signature)=DBQueryNew($this->conn, "SELECT 1 FROM ".$pref."pay_pay WHERE id='$id' and id_person_signature>0 and dt_signature is not NULL");
            if(!$signature){
                $res['data_type']['dt_signature']['type']='button';
                $res['dt_signature']='Подтверждено';
            }
            
            $this->result=$res;
            
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>