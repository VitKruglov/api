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
            $arr_name=array('ID','Адрес','ID объекта ФИАС','Почтовый индекс','Округ','ID ФИАС региона','Регион','ID ФИАС области','Область','ID ФИАС города','Город','Городской округ','ID ФИАС района','Район','ID ФИАС улицы','Улица','ID ФИАС дома','Дом','Номер','Координаты Широта','Долгота','Уровень','');

            foreach($arr_name as $i=>$name){
                $res['data_type'][$arr[$i]]['name']=$name;
                if($i>1) $res['data_type'][$arr[$i]]['readonly']='readonly';
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
            $arr_name=array('ID','Адрес','ID объекта ФИАС','Почтовый индекс','Округ','ID ФИАС региона','Регион','ID ФИАС области','Область','ID ФИАС города','Город','Городской округ','ID ФИАС района','Район','ID ФИАС улицы','Улица','ID ФИАС дома','Дом','Номер','Координаты Широта','Долгота','Уровень','');

            foreach($arr_name as $i=>$name){
                $res['data_type'][$arr[$i]]['name']=$name;
                if($i>1) $res['data_type'][$arr[$i]]['readonly']='readonly';
            }
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>