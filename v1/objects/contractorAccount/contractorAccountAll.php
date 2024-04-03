<?php
//---------------------------------------------------------------------------//
//                                                                            //
//        получение информации о всех счетах контрагентах     format2     //
//                                                                            //
//---------------------------------------------------------------------------//


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
    2=>"Дата изменения",
    3=>"Изменил",
    5=>"Основной",
    6=>"Название",
    7=>"Тип оплаты",
    8=>"Номер карты",
    9=>'Название банка',
    10=>'БИК',
    11=>"Номер счета",
    12=>'Дополнительная информация',
    13=>'Дата начала',
    14=>'Дата окончания',
    15=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'dtChange'=>'',
    'personChange'=>'',
    'main'=>$this->Input->inputSelect('search[main]',50,"SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),
    'name'=>$this->Input->inputText('like[name]',20),
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT 0, 'Наличные' UNION SELECT 1, 'Безнал'"),
    'card'=>$this->Input->inputText('like[card]',20),
    'bankName'=>$this->Input->inputText('like[bankName]',20),
    'bankBik'=>$this->Input->inputText('like[bankBik]',20),
    'account'=>$this->Input->inputText('like[account]',20),  
    'note'=>$this->Input->inputText('like[note]',50),    
    'dtBegin'=>'',
    'dtEnd'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.dt_change as dtChange,
    tbl1.id_person_change as personChange,
    tbl1.main as main, 
    tbl1.name as name, 
    tbl1.type as type, 
    tbl1.card as card, 
    tbl1.bank_name as bankName,
    tbl1.bank_bik as bankBik,
    tbl1.account as account,
    tbl1.note as note,
    tbl1.dt_begin as dtBegin,
    tbl1.dt_end as dtEnd
    FROM ".$pref.$this->table_name." as tbl1 
";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
$this->arr['objGroup'][0]='Все';
$this->arr['objObject'][0]='Все';
$this->arr['objDepartment'][0]='Все';
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
    
    }

}

?>