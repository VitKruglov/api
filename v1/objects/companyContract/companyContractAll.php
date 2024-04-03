<?php
//---------------------------------------------------------------------------//
//                                                                            //
//        получение информации о всех договорах контрагентах     format2     //
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
    1=>"Дата изменения",
    2=>"Изменил",
    3=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    4=>"<a href=\"".$name_script."?sort=id_company\" class=\"upmenu\">Организация</a>",
    5=>"<a href=\"".$name_script."?sort=id_contractor\" class=\"upmenu\">Контрагент</a>",
    6=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    7=>"<a href=\"".$name_script."?sort=number\" class=\"upmenu\">Номер</a>",
    8=>'Дата начала действия',
    9=>'Дата окончания действия',
    10=>'Дата подписания',
    11=>'НДС %',
    12=>'НДС тип расчета',
    13=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Дополнительная информация</a>",
    14=>'Счет контрагента',
    15=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'dtChange'=>'',
    'personChange'=>'',
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'objCompany'=>$this->Input->inputSelect('search[objCompany]',50,"SELECT id,name FROM ".$pref."companies"),
    'objContractor'=>$this->Input->inputSelect('search[objContractor]',50,"SELECT id,name FROM ".$pref."contractor"),
    'name'=>$this->Input->inputText('like[name]',50),
    'number'=>$this->Input->inputText('like[number]',50),
    'dtBegin'=>$this->Input->inputText('like[dtBegin]',8),
    'dtEnd'=>$this->Input->inputText('like[dtEnd]',8),
    'dtContractSign'=>$this->Input->inputText('like[dtContractSign]',8),
    'nds'=>$this->Input->inputText('like[nds]',5),  
    'ndsType'=>$this->Input->inputText('like[ndsType]',10),  
    'note'=>$this->Input->inputText('like[note]',50),    
    'objAccount'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.dt_change as dtChange,
    tbl1.id_person_change as personChange,
    tbl1.state as state,
    tbl1.id_company as objCompany,
    tbl1.id_contractor as objContractor,
    tbl1.name as name, 
    tbl1.number as number, 
    tbl1.dt_begin as dtBegin,
    tbl1.dt_end as dtEnd,
    tbl1.dt_contract_sign as dtContractSign,
    tbl1.nds as nds, 
    tbl1.nds_type as ndsType, 
    tbl1.note as note,
    tbl1.id_account as objAccount
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
        $this->result[$k]['objTypeContract']=$this->arr['objTypeContract'][$this->result[$k]['objTypeContract']];
        $this->result[$k]['objCompany']=$this->arr['objCompany'][$this->result[$k]['objCompany']];
        $this->result[$k]['objContractor']=$this->arr['objContractor'][$this->result[$k]['objContractor']];
        $this->result[$k]['objAccount']=$this->arr['objAccount'][$this->result[$k]['objAccount']];        
    }

}

?>