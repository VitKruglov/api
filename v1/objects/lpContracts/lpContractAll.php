<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех договорах     format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_contracts.dhtml";
$name_script_info="lp_contract_info.dhtml";

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
    0=>"ID",
    1=>"Активность",
    2=>'Изменил',
    3=>'Дата изменения',
    4=>'Дата подписания',
    5=>'Дата начала действия',
    6=>'Дата окончания действия',
    7=>'Тип',
    8=>'ЭДО',
    9=>'Организация',
    10=>'Исполнитель',
    11=>'Дополнительная информация',
    12=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'PersonChange'=>'',
    'dtChange'=>'',
    'dtContractSign'=>$this->Input->inputText('like[dtContractSign]',8),
    'dtBegin'=>$this->Input->inputText('like[dtBegin]',8),
    'dtEnd'=>$this->Input->inputText('like[dtEnd]',8),
    'objTypeContract'=>$this->Input->inputSelect('search[objTypeContract]',50,"SELECT id,name FROM ".$pref."type_contract"),
    'edo'=>'',
    'objCompany'=>$this->Input->inputSelect('search[objCompany]',50,"SELECT id,name FROM ".$pref."companies"),
    'objWorker'=>$this->Input->inputSelect('search[objWorker]',50,"SELECT tbl1.id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_worker as tbl1, ".$pref."lp_man as tbl2 WHERE tbl2.id=tbl1.id_man"),
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.dt_contract_sign as dtContractSign,
    tbl1.dt_begin as dtBegin,
    tbl1.dt_end as dtEnd,
    tbl1.type as objTypeContract,
    tbl1.edo as edo,
    tbl1.id_company as objCompany,
    tbl1.id_worker as objWorker,
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

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['objTypeContract']=$this->arr['objTypeContract'][$this->result[$k]['objTypeContract']];
        $this->result[$k]['objCompany']=$this->arr['objCompany'][$this->result[$k]['objCompany']];

        if($this->without!='objWorker'){
            $this->arr=getApi3($this->conn, $pref, "lpWorkers","lpWorkerAll","id",$this->result[$k]['objWorker'], 'arrObjContract', NULL, $this->domain,$this->arr,$this->token, $this->session_id);
            $this->result[$k]['objWorker']=$this->arr['lpWorkerAll']['id'][$this->result[$k]['objWorker']];

        }
    }
}

?>