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
    list($nbd)=explode("_",$pref);
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

                $res[strtolower($name_column)]='';  //html_entity_decode(
                $res['data_type'][strtolower($name_column)]['type']=$type_column;
    			$i++;
			}
            $arr_name=array('ID','Внимание','Физ.лицо','Учетный номер','Дата начала работы','Заморожен до','Кандидат дата','Конец Дата','Дополнительная информация','Активный','Изменено','Изменил');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_man");
            $res['data_type']['id_man']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_man']['table']=$res['data_type']['id_man']['table']+array($b[0] => $b[1]);
            
            unset($res['data_type']['id_set']);

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

            $arr[$i]='newspeciality';
            $i++;
            $arr[$i]='specialities';
            $i++;
            $arr[$i]='newcontract';
            $i++;
            $arr[$i]='contracts';
            $i++;
            $arr[$i]='newaccount';
            $i++;
            $arr[$i]='accounts';
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            //специализации
            $specialities=DBFetchNew($this->conn, "SELECT tbl1.id_speciality as id, tbl2.name as name, tbl1.note as note, concat('<a href=\"/lrv/public/$nbd/lpSpeciality/lpSpecialityInfo/',tbl1.id_speciality,'_$id\">инфо.</a>')
            FROM ".$pref."lp_speciality as tbl1 
            LEFT JOIN ".$pref."speciality as tbl2 ON tbl2.id=tbl1.id_speciality
            WHERE tbl1.id_worker=$id");

            $res['data_type']['newspeciality']['type']='button-link';
            $res['data_type']['newspeciality']['link']="/lrv/public/$nbd/lpSpeciality/lpSpecialityInfo/0_$id";
            $res['newspeciality']='Добавить специализацию';

            $res['specialities']=$specialities;
            $res['specialities']=$res['specialities']+array('name'=>array('№ специализации','Название','Примечание',''));
            $res['data_type']['specialities']['type']='table';   

            //договора
            $contracts=DBFetchNew($this->conn, "SELECT tbl1.id as id, tbl4.name as id_company, tbl2.name as type, tbl3.name as active, tbl1.dt_begin as dt_begin, tbl1.dt_end as dt_end, concat('<a href=\"/lrv/public/$nbd/lpContracts/lpContractInfo/',tbl1.id,'\">инфо.</a>')
            FROM ".$pref."lp_contract as tbl1 
    LEFT JOIN ".$pref."type_contract as tbl2 ON tbl2.id=tbl1.type
    LEFT JOIN (SELECT '1' as id,'Да' as name UNION SELECT '2','Нет') as tbl3 ON tbl3.id=tbl1.state
    LEFT JOIN ".$pref."companies as tbl4 ON tbl4.id=tbl1.id_company
            WHERE tbl1.id_worker=$id");

            $res['data_type']['newcontract']['type']='button-link';
            $res['data_type']['newcontract']['link']="/lrv/public/$nbd/lpContracts/lpContractInfo/0_$id";
            $res['newcontract']='Добавить договор';

            $res['contracts']=$contracts;
            $res['contracts']=$res['contracts']+array('name'=>array('№','Организация','Тип','Активность','Дата начала','Дата окончания',''));
            $res['data_type']['contracts']['type']='table';   

            //счета
            $accounts=DBFetchNew($this->conn, "SELECT tbl1.id as id, if(tbl1.main=1, 'Да','Нет') as main, if(tbl1.type=0,'Наличные','Безналичные') as type, tbl1.card as card, tbl1.account as account, concat('<a href=\"/lrv/public/$nbd/lpAccount/lpAccountInfo/',tbl1.id,'\">инфо.</a>')
            FROM ".$pref."lp_account as tbl1 WHERE tbl1.id_worker=$id");

            $res['data_type']['newaccount']['type']='button-link';
            $res['data_type']['newaccount']['link']="/lrv/public/$nbd/lpAccount/lpAccountInfo/0_$id";
            $res['newaccount']='Добавить счет';

            $res['accounts']=$accounts;
            $res['accounts']=$res['accounts']+array('name'=>array('№','Основной','Тип','Номер карты','Номер счета',''));
            $res['data_type']['accounts']['type']='table';   

            //расширенные и клиентозависимые требования
            $query="SELECT id_skill, id_client FROM ".$pref."lp_skill WHERE id_worker=$id and state=1";

            $result=DBFetchNew($this->conn, $query);
            for($i=0;$i<count($result);$i++){
                list($id_skill, $id_client)=$result[$i];
                if(is_null($id_client))
                    $arr_r[$id_skill]=1;
                else
                    $arr_r[$id_skill][$id_client]=1;
            }

            $clients=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."clients");
            $reqs=DBFetchNew($this->conn, "SELECT concat(name, if(clients=1,' (клиентозависимый)','')), id, clients FROM ".$pref."global_requirements ORDER by type, name");

            for($j=0;$j<count($reqs);$j++){
                list($n,$id_r,$cl)=$reqs[$j];
                if($cl==0){
                    if($arr_r[$id_r]==1){
                        $s="<input type=\"hidden\" name=\"input[skill][$id_r]\" value=\"1\">";
                        $checked_use_no='';
                        $checked_use_yes='checked';                    
                    }else{
                        $s='';
                        $checked_use_no='checked';
                        $checked_use_yes='';                        
                    }

                    $reqs[$j][1]="<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r]\" id=\"input[reqs][1][$id_r]\" value=\"0\" $checked_use_no>
                                <label class=\"form-check-label\">Нет</label>
                            </div>
                            <div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r]\" id=\"input[reqs][1][$id_r]\" value=\"1\" $checked_use_yes>
                                <label class=\"form-check-label\">Да</label>
                            </div>$s";

                }else{
                    $reqs[$j][1]='';
                    for($k=0;$k<count($clients);$k++){
                        list($id_cl,$n_cl)=$clients[$k];
                        $reqs[$j][1].=$n_cl."&nbsp;";

                        if($arr_r[$id_r][$id_cl]==1){
                            $s="<input type=\"hidden\" name=\"input[skill][$id_r][$id_cl]\" value=\"1\">";
                            $checked_use_no='';
                            $checked_use_yes='checked';                    
                        }else{
                            $s='';
                            $checked_use_no='checked';
                            $checked_use_yes='';                        
                        }
    
    
                        $reqs[$j][1].="<div class=\"form-check form-check-inline\">
                                    <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r][$id_cl]\" id=\"input[reqs][1][$id_r][$id_cl]\" value=\"0\" $checked_use_no>
                                    <label class=\"form-check-label\">Нет</label>
                                </div>
                                <div class=\"form-check form-check-inline\">
                                    <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r][$id_cl]\" id=\"input[reqs][1][$id_r][$id_cl]\" value=\"1\" $checked_use_yes>
                                    <label class=\"form-check-label\">Да</label>
                                </div>$s<br>";
                    }
                }
                unset($reqs[$j][2]);
            }

            $res['reqs']=$reqs;
            $res['reqs']=$res['reqs']+array('name'=>array('Навык','Есть у исполнителя'));
            $res['data_type']['reqs']['type']='table';
            $res['data_type']['reqs']['name']='';   

            $arr_name=array('ID','Внимание','Физ.лицо','Учетный номер','Дата начала работы','Заморожен до','Кандидат дата','Конец Дата','Дополнительная информация','Активный','Изменено','Изменил','','Специализации','','Договора','','Счета');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_man");
            $res['data_type']['id_man']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_man']['table']=$res['data_type']['id_man']['table']+array($b[0] => $b[1]);

        #    $a=DBFetchNew($this->conn, "SELECT id, note FROM ".$pref."lp_requirements");
        #    $res['data_type']['id_set']['table']=array();
         #   foreach($a as $j => $b) $res['data_type']['id_set']['table']=$res['data_type']['id_set']['table']+array($b[0] => $b[1]);
         unset($res['data_type']['id_set']);

            //текущий баланс исполнителя
            list($balance_now)=DBQueryNew($this->conn, "SELECT sum(b.sum*t.type) FROM ".$pref."pay_balance as b,".$pref."lp_contract as c, ".$pref."pay_type as t WHERE c.id_worker=$id and b.id_contract=c.id and t.id=b.id_type");

            $res['data_type']=array_slice($res['data_type'],0,2)+array('balance_now'=>array('name'=>'Текущий баланс,руб','type'=>'varchar'))+array_slice($res['data_type'],2);
            $res=array_slice($res,0,2)+array('balance_now'=>$balance_now)+array_slice($res,2);
            $res['data_type']['balance_now']['readonly']='readonly';


            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>