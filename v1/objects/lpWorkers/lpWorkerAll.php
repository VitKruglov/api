<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о исполнителях    format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_worker.dhtml";
$name_script_info="lp_worker_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['id_man']))
        $id_man=$data['id_man'];    
    if(isset($data['sort']))
        $sort=$data['sort'];

    //показать всех активных
    if($data['search']['active']=='yes' or $active=='yes'){
        $where=" and (tbl1.dt_pause is NULL or UNIX_TIMESTAMP(tbl1.dt_pause)<".time().") and (tbl1.dt_candidate is NULL or tbl1.dt_candidate='') and (tbl1.dt_end is NULL or tbl1.dt_end='')";

        $active='yes';
        unset($data['search']['active']);
    }

    //фильтрация по наличию действующей медкнижки
    if($data['search']['med']=='yes' or $med=='yes'){
       $med='yes';
        unset($data['search']['med']);
    }

    //фильтрация по свободным на дату
    if($data['search']['dtFree']){
        $dt_free=$data['search']['dtFree'];
        unset($data['search']['dtFree']);

        list($not_id_worker)=DBQueryNew($this->conn, "SELECT group_concat(c.id_worker) FROM ".$pref."lp_contract as c, ".$pref."orders_shifts as s WHERE LEFT(s.dt_begin,10)='$dt_free' and c.id=s.id_contract GROUP BY c.id_worker");  
        if(!is_null($not_id_worker))
            $where.=" and tbl1.id NOT IN ($not_id_worker)";      
    }

    //фильтрация по исполнителям, которые уже работали у клиента
    #$data['search']['idClient']=3;
    if($data['search']['idClient']){
        $id_client=$data['search']['idClient'];
        unset($data['search']['idClient']);

        list($workers_id_client)=DBQueryNew($this->conn, "SELECT group_concat(t.id) FROM (SELECT c.id_worker as id FROM ".$pref."clients_group as g, ".$pref."clients_object as o, ".$pref."orders_requests as r, ".$pref."orders_shifts as s, ".$pref."lp_contract as c WHERE g.id_client=$id_client and o.id_group=g.id and r.id_object=o.id and s.id_request=r.id and c.id=s.id_contract GROUP BY c.id_worker) as t");  
        if(!is_null($workers_id_client))
            $where.=" and tbl1.id IN ($workers_id_client)";  
        else
            $where.=" and 1=2";
    }

    //фильтрация по исполнителям, которые уже работали на объекте
    #$data['search']['idObject']=2;
    if($data['search']['idObject']){
        $id_object=$data['search']['idObject'];
        unset($data['search']['idObject']);

        list($workers_id_object)=DBQueryNew($this->conn, "SELECT group_concat(t.id) FROM (SELECT c.id_worker as id FROM ".$pref."orders_requests as r, ".$pref."orders_shifts as s, ".$pref."lp_contract as c WHERE r.id_object=$id_object and s.id_request=r.id and c.id=s.id_contract GROUP BY c.id_worker) as t");  
        if(!is_null($workers_id_object))
            $where.=" and tbl1.id IN ($workers_id_object)";      
        else
            $where.=" and 1=2";
    }
    

#$data['search']['speciality']=json_encode(array(3));
    //фильтрация по специализации
    if($data['search']['speciality']){
        $speciality=json_decode($data['search']['speciality'],true);
        unset($data['search']['speciality']);
    }

#$data['search']['skill']=json_encode(array(6,12));
    //фильтрация по специализации
    if($data['search']['skill']){
        $skill=json_decode($data['search']['skill'],true);
        unset($data['search']['skill']);
    }

    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"ID",
    1=>"ФИО",
    2=>"Пол",
    3=>"Физическое лицо",
    4=>"Учетный номер",
    5=>"Дата начала работы",
    6=>"Дата окончания работы",
    7=>"Заморожен до",
    8=>"Кандидат дата",
    9=>'Дополниительная информация',
    10=>'Внимание',
    11=>'Контракты',
    12=>'Специализации',
    13=>'Расширенные возможности',
    14=>'Наличие действующей медкнижки',
    15=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'fio'=>$this->Input->inputText('like[fio]',10),
    'gender'=>$this->Input->inputText('search[gender]',10),
    'objMan'=>$this->Input->inputSelect('search[objMan]',50, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_man"),
    'number'=>$this->Input->inputText('like[number]',10),
    'dtBegin'=>$this->Input->inputText('like[dtBegin]',10),
    'dtEnd'=>$this->Input->inputText('like[dtEnd]',10),
    'dtPause'=>$this->Input->inputText('like[dtPause]',10),
    'dtCandidate'=>$this->Input->inputText('like[dtCandidate]',10),
    'note'=>'',
    'attention'=>'',
    'arrObjContract'=>'',
    'arrObjSpeciality'=>'',
    'arrSkill'=>'',
    'medBook'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl2.name as fio,
    tbl2.gender as gender,
    tbl1.id_man as objMan, 
    tbl1.number as number, 
    tbl1.dt_begin as dtBegin, 
    tbl1.dt_end as dtEnd, 
    tbl1.dt_pause as dtPause, 
    tbl1.dt_candidate as dtCandidate,
    tbl1.note as note,
    tbl1.note2 as attention,
    tbl1.id as arrObjContract, 
    tbl1.id_man as arrObjSpeciality,
    tbl1.id as arrSkill,
    NULL as medBook
    FROM ".$pref.$this->table_name." as tbl1 
    LEFT JOIN (SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,'')) as name, gender as gender FROM ".$pref."lp_man) as tbl2 ON tbl2.id=tbl1.id_man
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
        $not_del=0;
        $not_del_med=0;
        $not_del_spec=0;
        $not_del_skill=0;

        $this->arr=getApi3($this->conn, $pref, "lpMan","lpManAll","id",$this->result[$k]['objMan'], NULL, NULL, $this->domain,$this->arr,$this->token, $this->session_id);
        $this->result[$k]['objMan']=$this->arr['lpManAll']['id'][$this->result[$k]['objMan']];

        $this->arr=getApi3($this->conn, $pref, "lpSpeciality","lpSpecialityAll","idWorker",$id, NULL, 'array', NULL ,$this->arr);
        $this->result[$k]['arrObjSpeciality']=$this->arr['lpSpecialityAll']['idWorker'][$id];

        if($this->without!='arrObjContract'){
            $this->arr=getApi3($this->conn, $pref, "lpContracts","lpContractAll","objWorker",$id,"objWorker", 'array', NULL ,$this->arr);
            $this->result[$k]['arrObjContract']=$this->arr['lpContractAll']['objWorker'][$id];
        }
        if(isset($this->result[$k]['arrObjContract']['id']) and isset($this->result[$k]['arrObjContract']['objWorker']))
            unset($this->result[$k]['arrObjContract']['objWorker']);
        else
            foreach($this->result[$k]['arrObjContract'] as $w => $val3)
                unset($this->result[$k]['arrObjContract'][$w]['objWorker']);

        $this->arr=getApi3($this->conn, $pref, "lpSkill","lpSkillAll","idWorker",$id, NULL, 'array', NULL ,$this->arr);
        $this->result[$k]['arrSkill']=$this->arr['lpSkillAll']['idWorker'][$id];
    #    if($this->result[$k]['arrSkill']>0) 
     #       $this->result[$k]['arrSkill']=getApi2($this->conn, $pref, "lpSkill","lpSkillAll",array('id_worker'=>$id), NULL, NULL, NULL, $this->arr);
/*
        //проверка на наличие действующего контракта - работает, но Киреев пока решил не применять в фильтре Активные
        if($active=='yes'){
            if(isset($this->result[$k]['objContract']['id'])){
                    if(isset($this->result[$k]['objContract']['dtBegin']))
                        $dtBegin=$this->result[$k]['objContract']['dtBegin'];
                    else
                        $dtBegin=NULL;
                    if(isset($this->result[$k]['objContract']['dtEnd']))
                        $dEnd=$this->result[$k]['objContract']['dtEnd'];
                    else
                        $dtEnd=NULL;
                    if($this->result[$k]['objContract']['state']==1 and $dtBegin!=NULL and $dtBegin<=date("Y-m-d",time()) and ($dtEnd==NULL or $dtEnd>date("Y-m-d",time())))
                        $not_del=1;
            }else{
                foreach($this->result[$k]['objContract'] as $t => $val2){
                    if(isset($val2['dtBegin']))
                        $dtBegin=$val2['dtBegin'];
                    else
                        $dtBegin=NULL;
                    if(isset($val2['dtEnd']))
                        $dEnd=$val2['dtEnd'];
                    else
                        $dtEnd=NULL;
                    if($val2['state']==1 and $dtBegin!=NULL and $dtBegin<=date("Y-m-d",time()) and ($dtEnd==NULL or $dtEnd>date("Y-m-d",time())))
                        $not_del=1;
                    else
                        unset($this->result[$k]['objContract'][$t]);
                }
            }
            if($not_del==0)       //нет действующих контрактов- удаляем исполнителя
                unset($this->result[$k]);
        }
*/
        //медкнижка
        $this->result[$k]['medBook']=NULL;        
        if(isset($this->result[$k]['objMan']['objDocs']['id'])){
            if($this->result[$k]['objMan']['objDocs']['idType']['id']==2 and $this->result[$k]['objMan']['objDocs']['dtEnd']>=date("Y-m-d",time())){
                $not_del_med=1;
                $this->result[$k]['medBook']=1;
            }elseif($this->result[$k]['objMan']['objDocs']['idType']['id']==2 and $this->result[$k]['medBook']!=1){
                $this->result[$k]['medBook']=0;
            }
        }else
            foreach($this->result[$k]['objMan']['objDocs'] as $t => $val2){
                if(isset($val2['idType']['id']) and isset($val2['dtEnd']))
                    if($val2['idType']['id']==2 and $val2['dtEnd']>=date("Y-m-d",time())){
                        $not_del_med=1;
                        $this->result[$k]['medBook']=1;
                    }elseif($val2['idType']['id']==2 and $this->result[$k]['medBook']!=1){
                        $this->result[$k]['medBook']=0;
                    }
            }

        if($med=='yes' and $not_del_med==0)       //нет действующей медкнижки- удаляем исполнителя
            unset($this->result[$k]);


        if($speciality){
            if(isset($this->result[$k]['objSpeciality'])){
                if(isset($this->result[$k]['objSpeciality']['idSpeciality'])){
                    if(in_array($this->result[$k]['objSpeciality']['idSpeciality'],$speciality))
                        $not_del_spec=1;
                }else
                    foreach($this->result[$k]['objSpeciality'] as $t => $val2){
                        if(isset($val2['idSpeciality']))
                            if(in_array($val2['idSpeciality'],$speciality))
                                $not_del_spec=1;
                    }
            }
            if($not_del_spec==0)       //нет специализации- удаляем исполнителя
                unset($this->result[$k]);
        }

        if($skill){
        #    $arr_skill=array();
            if(isset($this->result[$k]['objSkill']['objSkills'])){
                    foreach($this->result[$k]['objSkill']['objSkills'] as $t => $val2){
                        if(isset($val2['id'])){
                         #   array_push($arr_skill,$val2['id']);
                            if(in_array($val2['id'],$skill))
                                $not_del_skill=1;
                        }    
                    }
            }
            if($not_del_skill==0)       //нет скила- удаляем исполнителя
                unset($this->result[$k]);
                
       }
    }
}


?>