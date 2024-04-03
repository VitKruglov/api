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

if(isset($data['idObject']))
    $idObject=$data['idObject'];
elseif(isset($this->getParam['get']['idObject']))
    $idObject=$this->getParam['get']['idObject'];

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

            $arr_name=array('ID','Изменено','Изменил','Активный','№ заказа','Объект','Внимание',' ','Дата и время начала','Дата и время конца','Кол-во исполнителей','Ставка','Дополнительная информация');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id, id FROM ".$pref."orders");
            $res['data_type']['id_order']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_order']['table']=$res['data_type']['id_order']['table']+array($b[0] => $b[1]);

            //объект
            if($idObject>0)
                $where=" and ".$pref."clients_object.id=$idObject";

            $a=DBFetchNew($this->conn, "SELECT id, name, id_group, address FROM ".$pref."clients_object WHERE 1=1 $where");
            $res['data_type']['id_object']['table']=array();
            foreach($a as $j => $b){
                $b1=array();
                $b1['name'] = $b[1];
                $b1['id_group'] = array();
                $b1['address'] = $b[3];

                list($b1['id_group'][$b[2]]['name'], $b1['id_group'][$b[2]]['note'], $b1['id_group'][$b[2]]['id_client'])=DBQueryNew($this->conn, "SELECT name,note,id_client FROM ".$pref."clients_group WHERE id=".$b[2]);

                $id_client=$b1['id_group'][$b[2]]['id_client'];
                $b1['id_group'][$b[2]]['id_client']=array();

                list($b1['id_group'][$b[2]]['id_client'][$id_client]['name'])=DBQueryNew($this->conn, "SELECT name FROM ".$pref."clients WHERE id=".$id_client);

                $res['data_type']['id_object']['table']=$res['data_type']['id_object']['table']+
                    array($b[0]=>$b1);
            } 
            
            $a=DBFetchNew($this->conn, "SELECT '1', 'Да' UNION SELECT '0', 'Нет'");
            $res['data_type']['attention']['table']=array();
            foreach($a as $j => $b) $res['data_type']['attention']['table']=$res['data_type']['attention']['table']+array($b[0] => $b[1]); 
            $res['data_type']['attention']['type']='checkbox';
            $res['data_type']['attention']['value']='1';


            if($idObject>0)
                $where=" and ".$pref."clients_department.id_object=$idObject";

            $a=DBFetchNew($this->conn, "SELECT ".$pref."clients_rate.id , concat(".$pref."speciality.name,' / ',".$pref."clients_department.name) , rate_hour, hours, rate_day, rate_unit, id_type_unit,id_req, id_set
            FROM ".$pref."clients_rate, ".$pref."speciality, ".$pref."clients_department WHERE ".$pref."speciality.id=".$pref."clients_rate.id_speciality and ".$pref."clients_department.id=".$pref."clients_rate.id_department $where");
            $res['data_type']['id_rate']['table']=array();
            $res['data_type']['id_rate']['table'][0]['name']='';
            foreach($a as $j => $b) {
                $b1=array();
                $b1['name'] = $b[1];
                $b1['rate_hour'] = $b[2];
                $b1['hours'] = $b[3];
                $b1['rate_day'] = $b[4];
                $b1['rate_unit'] = $b[5];
                $b1['id_type_unit'] = $b[6];
                $b1['id_req'] = array();

                list($b1['id_req'][$b[7]]['gender'], $b1['id_req'][$b[7]]['age_after'], $b1['id_req'][$b[7]]['age_before'], $b1['id_req'][$b[7]]['national'], $b1['id_req'][$b[7]]['pasport'], $b1['id_req'][$b[7]]['med'])=DBQueryNew($this->conn, "SELECT if(gender='m','муж.',if(gender='w','жен.','все')),age_after, age_before, if(national='y','да',if(national='n','нет','')),if(pasport='y','да',if(pasport='n','нет','')),if(med='y','да',if(med='n','нет','')) FROM ".$pref."clients_requirements WHERE id=".$b[7]);

                if($b[8]>0){
                    $res_set=DBFetchNew($this->conn, "SELECT g.id, g.name, if(s.hidden is NULL, g.hidden, s.hidden), if(s.required is NULL, g.required, s.required) FROM ".$pref."global_requirements as g, ".$pref."set_req as s WHERE s.id_set=".$b[8]." and g.id=s.id_requirement");
                    foreach($res_set as $k1=>$val3){
                        $b1['id_set'][$b[8]][$val3[0]]['name_requirements']=$val3[1];
                        $b1['id_set'][$b[8]][$val3[0]]['hidden_requirements']=$val3[2];
                        $b1['id_set'][$b[8]][$val3[0]]['required_requirements']=$val3[3];
                    }
                }else
                    $b1['id_set'] = NULL;

                $res['data_type']['id_rate']['table']=$res['data_type']['id_rate']['table']+
                    array($b[0]=>$b1);
            }
            $this->result=$res;
    }
    if($id>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        $idObject=$result[5];
        $id_rate=$result[11];
        

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

            $arr[$i]='reqs';
            $i++;
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            //расширенные и клиентозависимые требования
            list($id_set)=DBQueryNew($this->conn, "SELECT id_set FROM ".$pref."orders_requirements WHERE id_request=$id");
            if($id_set>0){
                $query="SELECT id_requirement, hidden, required FROM ".$pref."set_req WHERE id_set=$id_set";

                $result=DBFetchNew($this->conn, $query);
                for($i=0;$i<count($result);$i++){
                    list($id_requirement, $hidden, $required)=$result[$i];
                    $arr_r[$id_requirement]['use']=1;
                    if($hidden==1)
                        $arr_r[$id_requirement]['hidden']=1;
                    if($required==1)
                        $arr_r[$id_requirement]['required']=1;
                }

            }

            $reqs=DBFetchNew($this->conn, "SELECT name, id, hidden, required FROM ".$pref."global_requirements ORDER by type, name");

            for($j=0;$j<count($reqs);$j++){
                list($n,$id_r,$hidden, $required)=$reqs[$j];
                if($arr_r[$id_r]['use']==1){
                    $checked_use_no='';
                    $checked_use_yes='checked';                    
                }else{
                    $checked_use_no='checked';
                    $checked_use_yes='';                        
                }

                if($hidden == 0){
                    $checked_hidden_no='checked';
                    $checked_hidden_yes='';
                }else{
                    $checked_hidden_no='';
                    $checked_hidden_yes='checked';
                }
                if($arr_r[$id_r]['hidden']==1){
                    $checked_hidden_no='';
                    $checked_hidden_yes='checked';                    
                }
                if($arr_r[$id_r]['hidden']==0){
                    $checked_hidden_no='checked';
                    $checked_hidden_yes='';                    
                }
                
                if($required == 0){
                    $checked_required_no='checked';
                    $checked_required_yes='';
                }else{
                    $checked_required_no='';
                    $checked_required_yes='checked';
                }
                if($arr_r[$id_r]['required']==1){
                    $checked_required_no='';
                    $checked_required_yes='checked';                    
                }
                if($arr_r[$id_r]['required']==0){
                    $checked_required_no='checked';
                    $checked_required_yes='';                    
                }

                $reqs[$j][1]="<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r]\" id=\"input[reqs][$id_r][1][0]\" value=\"0\" $checked_use_no>
                                <label class=\"form-check-label\">Нет</label>
                            </div>
                            <div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][1][$id_r]\" id=\"input[reqs][$id_r][1][0]\" value=\"1\" $checked_use_yes>
                                <label class=\"form-check-label\">Да</label>
                            </div>";
                $reqs[$j][2]="<div class=\"form-check form-check-inline\">
                            <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][2][$id_r]\" id=\"input[reqs][$id_r][2][0]\" value=\"0\" $checked_hidden_no>
                            <label class=\"form-check-label\">Нет</label>
                        </div>
                        <div class=\"form-check form-check-inline\">
                            <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][2][$id_r]\" id=\"input[reqs][$id_r][2][0]\" value=\"1\" $checked_hidden_yes>
                            <label class=\"form-check-label\">Да</label>
                        </div>";
                $reqs[$j][3]="<div class=\"form-check form-check-inline\">
                        <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][3][$id_r]\" id=\"input[reqs][$id_r][3][0]\" value=\"0\" $checked_required_no>
                        <label class=\"form-check-label\">Нет</label>
                    </div>
                    <div class=\"form-check form-check-inline\">
                        <input class=\"form-check-input\" type=\"radio\" name=\"input[reqs][3][$id_r]\" id=\"input[reqs][$id_r][3][0]\" value=\"1\" $checked_required_yes>
                        <label class=\"form-check-label\">Да</label>
                    </div>";
            }

            $res['reqs']=$reqs;
            $res['reqs']=$res['reqs']+array('name'=>array('Требование','Применимо к заказу','Скрытое','Строго'));
            $res['data_type']['reqs']['type']='table';   
        
            #$res['data_type']['reqs']['type']='text';
            #$res['reqs']="здесь должны быть расширенные требования";

            $arr_name=array('ID','Изменено','Изменил','Активный','№ заказа','Объект','Внимание',' ','Дата и время начала','Дата и время конца','Кол-во исполнителей','Ставка','Дополнительная информация','');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id, id FROM ".$pref."orders");
            $res['data_type']['id_order']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_order']['table']=$res['data_type']['id_order']['table']+array($b[0] => $b[1]);

            //объект
            if($idObject>0)
                $where=" and ".$pref."clients_object.id=$idObject";

            $a=DBFetchNew($this->conn, "SELECT id, name, id_group, address FROM ".$pref."clients_object WHERE 1=1 $where");
            $res['data_type']['id_object']['table']=array();
            foreach($a as $j => $b){
                $b1=array();
                $b1['name'] = $b[1];
                $b1['id_group'] = array();
                $b1['address'] = $b[3];

                list($b1['id_group'][$b[2]]['name'], $b1['id_group'][$b[2]]['note'], $b1['id_group'][$b[2]]['id_client'])=DBQueryNew($this->conn, "SELECT name,note,id_client FROM ".$pref."clients_group WHERE id=".$b[2]);

                $id_client=$b1['id_group'][$b[2]]['id_client'];
                $b1['id_group'][$b[2]]['id_client']=array();

                list($b1['id_group'][$b[2]]['id_client'][$id_client]['name'])=DBQueryNew($this->conn, "SELECT name FROM ".$pref."clients WHERE id=".$id_client);

                $res['data_type']['id_object']['table']=$res['data_type']['id_object']['table']+
                    array($b[0]=>$b1);
            } 
           
            
            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['attention']['table']=array();
            foreach($a as $j => $b) $res['data_type']['attention']['table']=$res['data_type']['attention']['table']+array($b[0] => $b[1]); 
            $res['data_type']['attention']['type']='checkbox';
            $res['data_type']['attention']['value']=1;

            //ставки и требования
            if($idObject>0)
                $where=" and ".$pref."clients_department.id_object=$idObject";
            if($id_rate>0)
                $where=" and ".$pref."clients_rate.id=$id_rate";

            $a=DBFetchNew($this->conn, "SELECT ".$pref."clients_rate.id , concat(".$pref."speciality.name,' / ',".$pref."clients_department.name) , rate_hour, hours, rate_day, rate_unit, id_type_unit,id_req, id_set
            FROM ".$pref."clients_rate, ".$pref."speciality, ".$pref."clients_department WHERE ".$pref."speciality.id=".$pref."clients_rate.id_speciality and ".$pref."clients_department.id=".$pref."clients_rate.id_department $where");
            $res['data_type']['id_rate']['table']=array();
            foreach($a as $j => $b) {
                $b1=array();
                $b1['name'] = $b[1];
                $b1['rate_hour'] = $b[2];
                $b1['hours'] = $b[3];
                $b1['rate_day'] = $b[4];
                $b1['rate_unit'] = $b[5];
                $b1['id_type_unit'] = $b[6];
                $b1['id_req'] = array();

                list($b1['id_req'][$b[7]]['gender'], $b1['id_req'][$b[7]]['age_after'], $b1['id_req'][$b[7]]['age_before'], $b1['id_req'][$b[7]]['national'], $b1['id_req'][$b[7]]['pasport'], $b1['id_req'][$b[7]]['med'])=DBQueryNew($this->conn, "SELECT if(gender='m','муж.',if(gender='w','жен.','все')),age_after, age_before, if(national='y','да',if(national='n','нет','')),if(pasport='y','да',if(pasport='n','нет','')),if(med='y','да',if(med='n','нет','')) FROM ".$pref."clients_requirements WHERE id=".$b[7]);

                if($b[8]>0){
                    $res_set=DBFetchNew($this->conn, "SELECT g.id, g.name, if(s.hidden is NULL, g.hidden, s.hidden), if(s.required is NULL, g.required, s.required) FROM ".$pref."global_requirements as g, ".$pref."set_req as s WHERE s.id_set=".$b[8]." and g.id=s.id_requirement");
                    foreach($res_set as $k1=>$val3){
                        $b1['id_set'][$b[8]][$val3[0]]['name_requirements']=$val3[1];
                        $b1['id_set'][$b[8]][$val3[0]]['hidden_requirements']=$val3[2];
                        $b1['id_set'][$b[8]][$val3[0]]['required_requirements']=$val3[3];
                    }
                }else
                    $b1['id_set'] = NULL;

                $res['data_type']['id_rate']['table']=$res['data_type']['id_rate']['table']+
                    array($b[0]=>$b1);
            }
            
            $this->result=$res;
            
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>