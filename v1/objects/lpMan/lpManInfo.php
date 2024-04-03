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
    if(isset($data['data_from_include_api']['new_photo'])){//меняем название файла с фото
        $query="UPDATE ".$pref.$this->table_name." SET photo='".$data['data_from_include_api']['new_photo']."' WHERE id=".$this->urlData[0];
        $result=DBExecuteNew($this->conn, $query);
    }
    elseif(!is_null($data['data_from_include_api']) and is_array($data['data_from_include_api']))
        $arr_fias=$data['data_from_include_api'];
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
            $arr_name=array('ID','Фамилия','Имя','Отчество','Название фото','Дата рождения','Пол','Телефон','Дополнительный телефон','Электронная почта','Адрес','Адрес ФИАС','Координаты широта','Координаты долгота','Дополнительная информация','Лид','Активный','Изменено','Изменил');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT NULL,NULL UNION SELECT id, concat(if(name IS NOT NULL and name!='',name,''),' ',if(phone IS NOT NULL and phone!='',phone,''),' ',if(address IS NOT NULL and address!='',address,'')) FROM ".$pref."lp_lead");
            $res['data_type']['id_lead']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_lead']['table']=$res['data_type']['id_lead']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT null,'Все' UNION SELECT 'm', 'Мужской' UNION SELECT 'w', 'Женский'");
            $res['data_type']['gender']['table']=array();
            foreach($a as $j => $b) $res['data_type']['gender']['table']=$res['data_type']['gender']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '', ' ' as name UNION SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY name");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);
            
            $this->result=$res;
    }
    if($id>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            $arr[0]='img';
            $arr_type[0]='img';
            //получаем название столбцов
			$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
			$res_columns=DBFetchNew($this->conn, $query);
			for($i=0;$i<count($res_columns);$i++)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i+1]=strtolower($name_column);
                $arr_type[$i+1]=$type_column;
			}
            $i++;
            $arr[$i]='newdoc';
            $i++;
            $arr[$i]='docs';
            $i++;
            $arr[$i]='newnote';
            $i++;
            $arr[$i]='notes';
            $i++;
            $arr[$i]='fiassearch';
            $i++;
            $arr[$i]='newfias';
            $i++;
            $arr[$i]='addphoto';
            $i++;
            
            $res=array();
            $res[$arr[0]]='';
            $res['data_type'][$arr[0]]['type']=$arr_type[0];

            foreach($result as $key=>$val){
                $res[$arr[$key+1]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key+1]]['type']=$arr_type[$key+1];
            }

            $res['name1']=$data['data_from_include_api'];

            if(strlen($res['photo'])>4){
                if($_SERVER['HTTPS']) $https="https://";
                else $https="http://";
                $res['img']=$https.$this->domain."/content/photos/$id/".$res['photo'];
            }else{
                $res['img']='https://outsourcing.complat.ru/api/404.jpg';
            }

            //документы
            $docs=DBFetchNew($this->conn, "SELECT tbl1.id as id, tbl2.name as type, tbl1.ser as ser, tbl1.number as number, concat('<a href=\"/lrv/public/$nbd/lpDoc/lpDocInfo/',tbl1.id,'\">инфо.</a>')
            FROM ".$pref."lp_doc as tbl1 
            LEFT JOIN ".$pref."type_document as tbl2 ON tbl2.id=tbl1.id_type
            WHERE tbl1.id_man=$id");

            $res['data_type']['newdoc']['type']='button-link';
            $res['data_type']['newdoc']['link']="/lrv/public/$nbd/lpDoc/lpDocInfo/0_$id";
            $res['newdoc']='Добавить документ';

            $res['docs']=$docs;
            $res['docs']=$res['docs']+array('name'=>array('№','Тип документа','Серия','Номер',''));
            $res['data_type']['docs']['type']='table';   

             //комментарии
            $docs=DBFetchNew($this->conn, "SELECT tbl1.id as id, tbl2.name as importance, tbl1.note as note, concat('<a href=\"/lrv/public/$nbd/lpNote/lpNoteInfo/',tbl1.id,'\">инфо.</a>')
            FROM ".$pref."lp_note as tbl1 
            LEFT JOIN (SELECT 1 as id, 'Обычный' as name UNION SELECT 2, 'ВАЖНО') as tbl2 ON tbl2.id=tbl1.importance
            WHERE tbl1.id_man=$id");

            $res['data_type']['newnote']['type']='button-link';
            $res['data_type']['newnote']['link']="/lrv/public/$nbd/lpNote/lpNoteInfo/0_$id";
            $res['newnote']='Добавить комментарий';

            $res['notes']=$docs;
            $res['notes']=$res['notes']+array('name'=>array('№','Важность','Описание',''));
            $res['data_type']['notes']['type']='table';   

            //строка поиска адреса по справочнику ФИАС
            if(isset($arr_fias)){
                $res['data_type']['fiassearch']['type']='select';  
                $res['data_type']['fiassearch']['table']=$arr_fias;
                $res['data_type']['newfias']['type']='button';
                $res['newfias']='Добавить адрес ФИАС';
            }else{
                $res['data_type']['fiassearch']['type']='varchar';  
                $res['data_type']['fiassearch']['placeholder']=' placeholder="введите адрес вручную"';
                $res['fiassearch']='';
                $res['data_type']['newfias']['type']='button';
                $res['newfias']='Найти адрес ФИАС';
            }
         

            $res['data_type']['addphoto']['type']='file';
            $res['data_type']['addphoto']['name']='Добавить фото';
            $res['addphoto']='Добавить фото';

            $arr_name=array('Фотография','ID','Фамилия','Имя','Отчество','Название фото','Дата рождения','Пол','Телефон','Дополнительный телефон','Электронная почта','Адрес','Адрес ФИАС','Координаты широта','Координаты долгота','Дополнительная информация','Лид','Активный','Изменено','Изменил','','Документы','','Комментарии','Поиск адреса в справочнике ФИАС','');
            foreach($arr_name as $i=>$name_td)
                $res['data_type'][$arr[$i]]['name']=$name_td;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT NULL,NULL UNION SELECT id, concat(if(name IS NOT NULL and name!='',name,''),' ',if(phone IS NOT NULL and phone!='',phone,''),' ',if(address IS NOT NULL and address!='',address,'')) FROM ".$pref."lp_lead");
            $res['data_type']['id_lead']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_lead']['table']=$res['data_type']['id_lead']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT NULL,NULL UNION SELECT 'all','Все' UNION SELECT 'male', 'Мужской' UNION SELECT 'female', 'Женский'");
            $res['data_type']['gender']['table']=array();
            foreach($a as $j => $b) $res['data_type']['gender']['table']=$res['data_type']['gender']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '', ' ' as name UNION SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY name");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>