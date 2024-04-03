<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о расширенных и клиентозависимых требованиях    format2   //
//                                                             //
//-------------------------------------------------------------//


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

if(isset($data['search']['idWorker'])){
    $where.=" and tbl1.id_worker=".$data['search']['idWorker'];
    unset($data['search']['idWorker']);
}

if(isset($data['search']['idSkill'])){
    $where.=" and tbl1.id_skill=".$data['search']['idSkill'];
    unset($data['search']['idSkill']);
}

if(isset($data['search']['state'])){
    $where.=" and tbl1.state=".$data['search']['state'];  
    unset($data['search']['state']);
}else
    $where.=" and tbl1.state=1 GROUP BY tbl1.id_skill, tbl1.id_worker";  

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"Изменил",
    2=>"Дата изменения",
    3=>"Активен",       
    4=>'Навык',
    5=>'Исполнитель',
    6=>'Клиенты',
    7=>'Примечание',
    8=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'personChange'=>'',
    'dtChange'=>'',
    'state'=>'',
    'objSkill'=>'',
    'idWorker'=>'',
    'arrClient'=>'',
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.state as state, 
    tbl1.id_skill as objSkill,
    tbl1.id_worker as idWorker,
    group_concat(tbl1.id_client) as arrClient,
    tbl1.note as note
    FROM ".$pref.$this->table_name." as tbl1 ";

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

        if(!is_null($this->result[$k]['arrClient'])){
            $arr_clients=explode(",",$this->result[$k]['arrClient']);
            $i=0;
            $this->result[$k]['arrClient']=array();
            foreach($arr_clients as $val){
                $this->result[$k]['arrClient'][$i]=$this->arr['objClient'][$val];
                $i++;
            }
        }

        $this->result[$k]['objSkill']=$this->arr['objGlobalRequirements'][$this->result[$k]['objSkill']];
/*        $this->result[$k]['arrObjSkills']=array();
        $res_req=DBFetchNew($this->conn, "SELECT s.id_skill, g.name, g.clients FROM ".$pref."lp_skill_desc as s,".$pref."global_requirements as g WHERE g.id=s.id_skill and s.id_set=".$id);
        
        for($i=0;$i<count($res_req);$i++){
            list($id_skill, $name, $clients)=$res_req[$i];
            $this->result[$k]['arrObjSkills'][$i]=array('id'=>$id_skill,'name'=>$name);
            if($clients==1 and isset($id_worker)){
                $arr_clients=array();
                $res_skill_client=DBFetchNew($this->conn, "SELECT c.id,c.name FROM ".$pref."lp_skill_client as s,".$pref."clients as c WHERE s.id_worker=$id_worker and s.id_skill=$id_skill and c.id=s.id_client");
                for($j=0;$j<count($res_skill_client);$j++){
                    list($id_client,$name_client)=$res_skill_client[$j];
                    array_push($arr_clients,array('id'=>$id_client,'name'=>$name_client));
                }
                $this->result[$k]['arrObjSkills'][$i]=array('id'=>$id_skill,'name'=>$name,'arrClients'=>$arr_clients);
            }
        }
*/
    }
}

?>