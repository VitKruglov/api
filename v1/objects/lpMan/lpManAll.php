<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех физических лицах         //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_man.dhtml";
$name_script_info="lp_man_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    if(isset($data['search'])){

    }else{
        $min_id=DBFetchNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." ORDER BY id DESC LIMIT 100");
        $min_id=$min_id[count($min_id)-1][0];
        $min_id=$min_id+0;
        $where=" and tbl1.id>=$min_id";
    }
    $session_id=$data['session_id'];
}



//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=sname\" class=\"upmenu\">Фамилия</a>",
    2=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Имя</a>",
    3=>"<a href=\"".$name_script."?sort=mname\" class=\"upmenu\">Отчество</a>",
    4=>'Фото',
    5=>'День рождения',
    6=>'Пол',
    7=>'Телефон',
    8=>'Дополнительный телефон',
    9=>'Электронная почта',
    10=>'Адрес',
    11=>'Примечание',
    12=>'Лид',
    13=>'Комментарии',
    14=>'Документы',
    15=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'sname'=>$this->Input->inputText('like[sname]',20),
    'name'=>$this->Input->inputText('like[name]',20),
    'mname'=>$this->Input->inputText('like[mname]',20),
    'arrPhoto'=>'',
    'bd'=>'',
    'gender'=>$this->Input->inputSelect('search[gender]',50, "SELECT NULL, NULL UNION SELECT 'male', 'муж.' UNION SELECT 'female', 'жен.'"),
    'phone'=>'',
    'phone2'=>'',
    'mail'=>'',
    'address'=>'',
    'note'=>'',
    'objLead'=>'',
    'arrObjNotes'=>'',
    'arrObjDocs'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.sname as sname, 
    tbl1.name as name, 
    tbl1.mname as mname, 
    tbl1.photo as arrPhoto, 
    tbl1.bd as bd, 
    tbl1.gender as gender,
    tbl1.phone as phone,
    tbl1.phone2 as phone2,
    tbl1.mail as mail,
    tbl1.address as address,
    tbl1.note as note,
    tbl1.id_lead as objLead,
    tbl1.id as arrObjNotes,
    tbl1.id as arrObjDocs
    FROM ".$pref.$this->table_name." as tbl1 
    ";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
//дополнительные данные
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->arr=getApi3($this->conn, $pref, "lpNote","lpNoteAll","idMan",$this->result[$k]['arrObjNotes'], NULL, 'array', NULL,$this->arr);
        $this->result[$k]['arrObjNotes']=$this->arr['lpNoteAll']['idMan'][$this->result[$k]['arrObjNotes']];

        $this->arr=getApi3($this->conn, $pref, "lpDoc","lpDocAll","idMan",$this->result[$k]['arrObjDocs'], NULL, 'array', NULL,$this->arr);
        $this->result[$k]['arrObjDocs']=$this->arr['lpDocAll']['idMan'][$this->result[$k]['arrObjDocs']];

        $this->arr=getApi3($this->conn, $pref, "lpLead","lpLeadAll","id",$this->result[$k]['objLead'], NULL, NULL, NULL,$this->arr);
        $this->result[$k]['objLead']=$this->arr['lpLeadAll']['id'][$this->result[$k]['objLead']];

         
            $url=$this->result[$k]['arrPhoto'];
            $this->result[$k]['arrPhoto']=NULL;

            $key="fsfj74na9";

            $str=$id.":".$this->domain.":".$this->session_id.":".$pref.":".$this->token;
        #    if(!is_null($url)){
                $this->result[$k]['arrPhoto']=array();
                $this->result[$k]['arrPhoto']['original']="https://".$this->domain."/get_photo.php?photo=original&token=".encode($str.":".$url,$key);
                $this->result[$k]['arrPhoto']['512']="https://".$this->domain."/get_photo.php?photo=512&token=".encode($str.":512_".$url,"512_".$key);
                $this->result[$k]['arrPhoto']['128']="https://".$this->domain."/get_photo.php?photo=128&token=".encode($str.":128_".$url,"128_".$key);
                $this->result[$k]['arrPhoto']['32']="https://".$this->domain."/get_photo.php?photo=32&token=".encode($str.":32_".$url,"32_".$key);
        #    }
    }
}


?>