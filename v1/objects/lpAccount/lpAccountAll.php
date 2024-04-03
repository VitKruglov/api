<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех счетах физического лица         //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_account.dhtml";
$name_script_info="lp_account_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}


//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"Изменил",
    3=>"Дата изменения",
    4=>'Исполнитель',
    5=>"Главный",
    6=>"Тип оплаты",
    7=>"Номер карты",
    8=>'Название банка',
    9=>'БИК',
    10=>"Номер счета",
    11=>'Примечание',
    12=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'idWorker'=>$this->Input->inputSelect('search[idWorker]',50, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_man"),
    'main'=>$this->Input->inputSelect('search[main]',50,"SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT 0, 'Наличные' UNION SELECT 1, 'Безнал'"),
    'card'=>$this->Input->inputText('like[card]',20),
    'bankName'=>$this->Input->inputText('like[bankName]',20),
    'bankBik'=>$this->Input->inputText('like[bankBik]',20),
    'account'=>$this->Input->inputText('like[account]',20),
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.id_worker as idWorker,
    tbl1.main as main, 
    tbl1.type as type, 
    tbl1.card as card, 
    tbl1.bank_name as bankName,
    tbl1.bank_bik as bankBik,
    tbl1.account as account,
    tbl1.note as note
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

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
    }
}
?>