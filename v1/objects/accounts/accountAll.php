<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех счетах АК     format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="accounts.dhtml";
$name_script_info="account_info.dhtml";


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
    1=>"Активность",
    2=>"Изменил",
    3=>"Дата изменения",
    4=>"Главный",
    5=>"Наличные",
    6=>'Организация',
    7=>'Название',    
    8=>"Номер карты",
    9=>'Название банка',
    10=>'БИК',
    11=>"Номер счета",
    12=>'Примечание',
    13=>''
);

//строка поиска
#$inputLine = new Input($db);
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'main'=>$this->Input->inputSelect('search[main]',50,"SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT 0, 'Наличные' UNION SELECT 1, 'Безнал'"),
    'idCompany'=>$this->Input->inputSelect('search[idCompany]',50, "SELECT id, name FROM ".$pref."companies"),
    'name'=>$this->Input->inputText('like[name]',10),
    'card'=>$this->Input->inputText('like[card]',10),
    'bankName'=>$this->Input->inputText('like[bankName]',10),
    'bankBik'=>$this->Input->inputText('like[bankBik]',10),
    'account'=>$this->Input->inputText('like[account]',10),
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id,     
tbl1.state as state,
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.main as main, 
tbl1.type as type, 
tbl1.id_company as idCompany,
tbl1.name as name, 
tbl1.card as card, 
tbl1.bank_name as bankName,
tbl1.bank_bik as bankBik,
tbl1.account as account,
tbl1.note as note
FROM ".$pref.$this->table_name." as tbl1 ";

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

        list($this->result[$k]['personChange'])=DBQueryNew($this->conn, "SELECT realname FROM ".$pref."persons WHERE id=".$this->result[$k]['personChange']);
    }
}

?>