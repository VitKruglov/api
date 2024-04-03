<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех клиентах     format2     //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients.dhtml";
$name_script_info="client_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    3=>"<a href=\"".$name_script."?sort=address\" class=\"upmenu\">Адрес</a>",
    4=>"<a href=\"".$name_script."?sort=id_fias\" class=\"upmenu\">Адрес ФИАС</a>",
    5=>"<a href=\"".$name_script."?sort=id_person\" class=\"upmenu\">Ответственный менеджер</a>",
    6=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Дополнительная информация</a>",
    7=>'Граница закрытого периода',
    8=>"Изменил границу закрытого периода",
    9=>"Дата изменения границы закрытого периода",
    10=>"Закрыл клиента",
    11=>"Дата закрытия клиента",
    12=>"Изменил",
    13=>"Дата изменения",
    14=>'Сайт',
    15=>'Контакт',
    16=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'name'=>$this->Input->inputText('like[name]',50),
    'address'=>$this->Input->inputText('like[address]',30),
    'objFiass'=>$this->Input->inputSelect('search[objFiass]',50,'SELECT id, address FROM '.$pref.'fias'),
    'objPersonResponsible'=>$this->Input->inputSelect('search[objPersonResponsible]',50,'SELECT id, realname FROM '.$pref.'persons'),
    'note'=>$this->Input->inputText('like[note]',50),    
    'closePeriod'=>'',
    'personClosePeriod'=>'',
    'dtClosePeriod'=>'',
    'personClose'=>'',
    'dtClose'=>'',
    'personChange'=>'',
    'dtChange'=>'',
    'www'=>'',
    'contact'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.name as name, 
    tbl1.address as address, 
    tbl1.id_fiass as objFiass,
    tbl1.id_person as objPersonResponsible,
    tbl1.note as note ,
    tbl1.close_period as closePeriod,
    tbl1.id_person_close_period as personClosePeriod,
    tbl1.dt_close_period as dtClosePeriod,
    tbl1.id_person_close as personClose,
    tbl1.dt_close as dtClose,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.www as www,
    tbl1.contact as contact
    FROM ".$pref.$this->table_name." as tbl1 
";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['personClose']=$this->arr['person'][$this->result[$k]['personClose']];
        $this->result[$k]['personClosePeriod']=$this->arr['person'][$this->result[$k]['personClosePeriod']];
        $this->result[$k]['objPersonResponsible']=$this->arr['objPerson'][$this->result[$k]['objPersonResponsible']];
        $this->result[$k]['objFiass']=$this->arr['objFiass'][$this->result[$k]['objFiass']];
        
    }

}

?>