<?php

//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех заявках за период        //
//                                                             //
//-------------------------------------------------------------//

$name_script="requests.dhtml";
$name_script_info="request_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['dt'])){
        $dt=$data['search']['dt'];
        unset($data['search']['dt']);
    }elseif(isset($data['like']['dt'])){
        $dt=$data['like']['dt'];
        unset($data['like']['dt']);
    }elseif(isset($this->getParam['get']['dt']))
        $dt=$this->getParam['get']['dt'];

    if(isset($data['search']['dtBegin'])){
        $dt_from=$data['search']['dtBegin'];
        unset($data['search']['dtBegin']);
    }elseif(isset($data['like']['dtBegin'])){
        $dt_from=$data['like']['dtBegin'];
        unset($data['like']['dtBegin']);
    }elseif(isset($this->getParam['get']['from']))
        $dt_from=$this->getParam['get']['from'];

    if(isset($data['search']['dtEnd'])){
        $dt_to=$data['search']['dtEnd'];
        unset($data['search']['dtEnd']);
    }elseif(isset($data['like']['dtEnd'])){
        $dt_to=$data['like']['dtEnd'];
        unset($data['like']['dtEnd']);
    }elseif(isset($this->getParam['get']['to']))
        $dt_to=$this->getParam['get']['to'];        

    if(isset($data['search']['minPercent'])){
        $minPercent=(int)$data['search']['minPercent'];
        unset($data['search']['minPercent']);
    }elseif(isset($data['like']['minPercent'])){
        $minPercent=(int)$data['like']['minPercent'];
        unset($data['like']['minPercent']);
    }elseif(isset($this->getParam['get']['minPercent']))
        $minPercent=(int)$this->getParam['get']['minPercent'];

    if(isset($data['search']['maxPercent'])){
        $maxPercent=(int)$data['search']['maxPercent'];
        unset($data['search']['maxPercent']);
    }elseif(isset($data['like']['maxPercent'])){
        $maxPercent=(int)$data['like']['maxPercent'];
        unset($data['like']['maxPercent']);
    }elseif(isset($this->getParam['get']['maxPercent']))
        $maxPercent=(int)$this->getParam['get']['maxPercent'];

    if(isset($data['search']['idGroup'])){
        $where.=" and tbl2.id_group=".$data['search']['idGroup'];
        unset($data['search']['idGroup']);
    } 

    if(isset($data['search']['idClient'])){
        $where.=" and tbl3.id_client=".$data['search']['idClient'];
        unset($data['search']['idClient']);
    } 

    if(isset($data['search']['objOrder']) and is_array($data['search']['objOrder'])){
        $s='';
        foreach($data['search']['objOrder'] as $k=>$val){
            $s.=','.$val;
        }
        $s=substr($s,1);
        $where.=" and tbl1.id_order IN ($s)";
        unset($data['search']['objOrder']);
    } 

    if(isset($data['search']['objObject']) and is_array($data['search']['objObject'])){
        $s='';
        foreach($data['search']['objObject'] as $k=>$val){
            $s.=','.$val;
        }
        $s=substr($s,1);
        $where.=" and tbl1.id_object IN ($s)";
        unset($data['search']['objObject']);
    } 

    if(strlen($dt)==10)
        $where.=" and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')='$dt'";
    elseif(strlen($dt_from)==10 and strlen($dt_to)==10)
        $where.=" and (DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')>='$dt_from' and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')<='$dt_to')";
    elseif(strlen($dt_from)==10)
        $where.=" and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')>='$dt_from'";
    else
        $where.=" and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')>=DATE_FORMAT(NOW(),'%Y-%m-01') ";

    if(isset($data['search']['id'])) $where=NULL;

    $session_id=$data['session_id'];
}

list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    3=>"Изменил",
    4=>"Дата изменения",
    5=>"<a href=\"".$name_script."?sort=alert\" class=\"upmenu\"><font color=red>Внимание</font></a>",
    6=>"<a href=\"".$name_script."?sort=id_order\" class=\"upmenu\">Заказ</a>",
    7=>"<a href=\"".$name_script."?sort=id_object\" class=\"upmenu\">Объект</a>",
    8=>"<a href=\"".$name_script."?sort=id_group\" class=\"upmenu\">Группа</a>",
    9=>"<a href=\"".$name_script."?sort=id_client\" class=\"upmenu\">Клиент</a>",
    10=>"<a href=\"".$name_script."?sort=dt_begin\" class=\"upmenu\">Начало</a>",
    11=>"<a href=\"".$name_script."?sort=dt_end\" class=\"upmenu\">Конец</a>",
    12=>"<a href=\"".$name_script."?sort=cnt\" class=\"upmenu\">Кол-во</a>",
    13=>"<a href=\"".$name_script."?sort=id_rate\" class=\"upmenu\">Ставка</a>",
    14=>'Отдельные требования к заказу',
    15=>'Назначенные смены',    
    16=>'Дополнительная информация',
    17=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'alert'=>$this->Input->inputText('like[alert]',15), 
    'objOrder'=>$this->Input->inputSelect('search[objOrder]',100,'SELECT id, concat("№",id," ",note) FROM '.$pref.'orders'),
    'objObject'=>$this->Input->inputSelect('search[objObject]',50,'SELECT id, name FROM '.$pref.'clients_object'),
    'idGroup'=>$this->Input->inputSelect('search[idGroup]',50,'SELECT id, name FROM '.$pref.'clients_group'),
    'idClient'=>$this->Input->inputSelect('search[idClient]',50,'SELECT id, name FROM '.$pref.'clients'),
    'dtBegin'=>$this->Input->inputText('like[dtBegin]',5),    
    'dtEnd'=>$this->Input->inputText('like[dtEnd]',5),    
    'cnt'=>'',
    'objRate'=>$this->Input->inputSelect('search[objRate]',50,"SELECT ".$pref."clients_rate.id as id, concat(".$pref."speciality.name,' / ',".$pref."clients_department.name) as name 
    FROM ".$pref."clients_rate, ".$pref."speciality, ".$pref."clients_department WHERE ".$pref."speciality.id=".$pref."clients_rate.id_speciality and ".$pref."clients_department.id=".$pref."clients_rate.id_department"),
    'objRequirement'=>'',
    'cntShifts'=>'',
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.alert as alert, 
tbl1.id_order as objOrder, 
tbl1.id_object as objObject,
tbl2.id_group as idGroup,
tbl3.id_client as idClient,
tbl1.dt_begin as dtBegin,
tbl1.dt_end as dtEnd,
tbl1.cnt as cnt,
tbl1.id_rate as objRate,
tbl1.id as objRequirement,
NULL as cntShifts,
tbl1.note as note
FROM ".$pref.$this->table_name." as tbl1 
LEFT JOIN ".$pref."clients_object as tbl2 ON tbl2.id=tbl1.id_object
LEFT JOIN ".$pref."clients_group as tbl3 ON tbl3.id=tbl2.id_group
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

        $this->arr=getApi3($this->conn, $pref, "clientsObject","clientObjectAll","id",$this->result[$k]['objObject'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objObject']=$this->arr['clientObjectAll']['id'][$this->result[$k]['objObject']];

        $this->arr=getApi3($this->conn, $pref, "orders","orderAll","id",$this->result[$k]['objOrder'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objOrder']=$this->arr['orderAll']['id'][$this->result[$k]['objOrder']];

        $this->arr=getApi3($this->conn, $pref, "clientsRate","clientRateAll","id",$this->result[$k]['objRate'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objRate']=$this->arr['clientRateAll']['id'][$this->result[$k]['objRate']];

        $this->arr=getApi3($this->conn, $pref, "ordersReq","orderReqAll","objRequest",$id, NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objRequirement']=$this->arr['orderReqAll']['objRequest'][$id];

        //назначенные смены
        $this->result[$k]['cntShifts']=$this->arr['requestCntShifts'][$id];

        $percent=(int)100*$this->result[$k]['cntShifts']/$this->result[$k]['cnt'];

        if(isset($maxPercent) and isset($minPercent)){
            if(!($percent>=$minPercent and $percent<=$maxPercent)){
                unset($this->result[$k]);
                $rekey=true;
            }
        }elseif(isset($maxPercent)){
            if(!($percent<=$maxPercent)){
                unset($this->result[$k]);
                $rekey=true;
            }
        }elseif($minPercent>0){
            if($percent<$minPercent){
                unset($this->result[$k]);
                $rekey=true;
            }
        }

    }
 
}

?>