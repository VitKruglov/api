<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о пользователе по ID                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
            $pref=$data['pref'];
    if(!is_null($data['data_from_include_api']))
        $newpass=$data['data_from_include_api'];
}

$result=array();

if (count($this->urlData)==1){
    $id=$this->urlData[0];
    if($id==0){
        $result=DBQueryNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." LIMIT 1");
        $id=$result[0];
    }
    if($id>0){
        //меняем пароль
        if(isset($newpass))
            DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET password='$newpass' WHERE id=".$id);
        
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;
        $result=DBQueryNew($this->conn, $query);

        list($id_owner)=explode("_",$pref);

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

            $arr[$i]='newpass';
            $i++;
            
            $res=array();
            $res['data_type']=array();

            if($id_owner>0) $str_id_owner="/".$id_owner;
            $res['permis']="<a href='/lrv/public".$str_id_owner."/permis/permisPerson/$id'>права доступа</a>";
            $res['data_type']['permis']['type']='link';
            $res['data_type']['permis']['name']='';

            $res['data_type']['newpass']['type']='button';
            $res['data_type']['newpass']['name']='';
            $res['newpass']='Сменить пароль';

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            $arr_name=array('ID','Логин','Пароль','Почта','Роль','ФИО','Телефон','Страница по умолчанию','IP','Группа','Cookie','Дополнительная информация','Часовой пояс ','token','','','');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."unit_name ORDER BY name");
            $res['data_type']['id_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_unit']['table']=$res['data_type']['id_unit']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 1, 'сотрудник' UNION SELECT 2, 'не сотрудник'");
            $res['data_type']['id_group']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_group']['table']=$res['data_type']['id_group']['table']+array($b[0] => $b[1]);            

            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>