<?php
//-------------------------------------------------------------//
//                                                             //
//                 одинаковая часть для всех                   //
//                   object файлов                              //
//-------------------------------------------------------------//
$t_start=microtime(true);

if(isset($urlParam['pref']))
    if($urlParam['pref']==0 or $urlParam['pref']=="0_") $urlParam['pref']="1_";
    
$$objName->method = $method;
$$objName->urlData = $urlData;    //данные из адресной строки до ?
$$objName->urlParam = $urlParam;  //данные из адресной строки после ?
$$objName->postParam = $_POST;    //данные из POST
$$objName->getParam = $_GET;    //данные из GET
$$objName->formdata = $formData;
$$objName->session_id = $session_id;
$$objName->config = $config;
$$objName->token = $token;
$$objName->domain = $domain;
$$objName->lrv = $lrv;

if((!isset($_POST) or is_null($_POST)) and count($formData)!=0){
    $$objName->postParam=$formData;
}

if($method=='PUT' or isset($formData['input'])){
    $$objName->postParam = $formData;   //данные из формы подставляем в POST
    if(!isset($$objName->postParam['input']['id']) and $urlData[0]>0)
        $$objName->postParam['input']['id']=$urlData[0];
}

if($method === 'GET' && $nameFunction){

    //------------------ создаем глобальные массивы------------------------//
    $pref=$$objName->urlParam['pref'];
    $$objName->arr=array();
    $db_local = DBConnect($tmpl, $config, $pref);

    $$objName->arr['person']=array();
    $$objName->arr['objPerson']=array();
    $arr_person=DBFetchNew($db_local, "SELECT id,realname FROM ".$pref."persons");
    foreach($arr_person as $key_person=>$val){
        $$objName->arr['person'][$val[0]]=$val[1];
        $$objName->arr['objPerson'][$val[0]]=array('id'=>$val[0],'name'=>$val[1]);
    }
    

    $db_main = DBConnect($tmpl, $config);
    $$objName->arr['owner']=array();
    $arr_owner=DBFetchNew($db_main, "SELECT id,dt_close FROM owner");
    foreach($arr_owner as $key_person=>$val)
        $$objName->arr['owner'][$val[0]]=array('id'=>$val[0],'dtClose'=>$val[1]);

    $$objName->arr['objTypeUnit']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name, name_full FROM ".$pref."clients_type_units");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeUnit'][$val[0]]=array('id'=>$val[0],'value'=>$val[1],'fullName'=>$val[2]);

    $$objName->arr['objFiass']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, address FROM ".$pref."fias");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objFiass'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objSpeciality']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."speciality");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objSpeciality'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objTypeContract']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."type_contract");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeContract'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objTypeServices']=array();
    $arr_type_services=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."type_services");
    foreach($arr_type_services as $key=>$val)
        $$objName->arr['objTypeServices'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objTypeDocument']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."type_document");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeDocument'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objGroup']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."clients_group");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objGroup'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objDepartment']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."clients_department");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objDepartment'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objObject']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."clients_object");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objObject'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objClient']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."clients");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objClient'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objCompany']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."companies");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objCompany'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objContractor']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."contractor");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objContractor'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objAccount']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."contractor_account");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objAccount'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objCompanyContract']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name, number FROM ".$pref."contractor");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objCompanyContract'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]." №".$val[2]);

    $$objName->arr['objReq']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, gender,age_after, age_before, national,passport,med FROM ".$pref."clients_requirements");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objReq'][$val[0]]=array('id'=>$val[0],'gender'=>$val[1],'ageAfter'=>$val[2], 'ageBefore'=>$val[3], 'national'=>$val[4], 'passport'=>$val[5], 'med'=>$val[6]);

    $$objName->arr['objTypeReq']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT 1 as id,'Одежда' as name UNION SELECT 2,'Навык' UNION SELECT 3,'Документ'");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeReq'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objGlobalRequirements']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name, clients FROM ".$pref."global_requirements");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objGlobalRequirements'][$val[0]]=array('id'=>$val[0],'name'=>$val[1],'clients'=>$val[2]);

    $$objName->arr['orderRequests']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id_order, count(id),min(dt_begin), max(dt_end) FROM ".$pref."orders_requests GROUP BY id_order");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['orderRequests'][$val[0]]=array('cntRequests'=>$val[1],'dtMin'=>$val[2],'dtMax'=>$val[3]);

    $$objName->arr['requestCntShifts']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id_request,count(id) FROM ".$pref."orders_shifts WHERE state=1 GROUP BY id_request");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['requestCntShifts'][$val[0]]=$val[1];

    $$objName->arr['objContract']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT c.id, concat(t.name,' ',if(m.sname is not null and m.sname!='',m.sname,''),' ',if(m.name is not null and m.name!='',m.name,'')) FROM ".$pref."lp_contract as c, ".$pref."lp_worker as w, ".$pref."lp_man as m, ".$pref."type_contract as t WHERE t.id=c.type and w.id=c.id_worker and m.id=w.id_man");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objContract'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objTypeCondition']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."type_condition");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeCondition'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    $$objName->arr['objTypeSource']=array();
    $arr_type_unit=DBFetchNew($db_local, "SELECT id, name FROM ".$pref."type_source");
    foreach($arr_type_unit as $key=>$val)
        $$objName->arr['objTypeSource'][$val[0]]=array('id'=>$val[0],'value'=>$val[1]);

    //---------------------------------------------------------------------//

    $$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL
    $arr_result=array('method'=>$method);

    //разбираем ответ и вычлиняем sort и search и colspan на будущее
    $res=$$objName->result;
    $sort=$res['sort'];
    $search=$res['search'];
    $colspan=$res['colspan'];   
    $data2=$res['data'];
    $data_type=$res['data_type'];
    $get=$res['get'];
    $query=$res['query'];
    $time=$res['time'];

    unset($res['sort'],$res['search'],$res['colspan'],$res['data'], $res['data_type'], $res['get'], $res['query'], $res['time']);

    //удаляем дублирующие объекты для методов All
    $arr_obj=array();
    $key=key($res);
    findObj(0,$res[$key]);
    $arr_obj_copy=$arr_obj;
    foreach($res as $i=>$val){
        $arr_obj=$arr_obj_copy;
        if(is_array($arr_obj)){
            if(count($arr_obj))
                $res[$i]=delObj(0,$res[$i]);
        }
    }

    //меняем формат имен полей для методов Info
    if(is_array($data_type)>0){
        foreach($res as $name_column=>$val){
            $a=explode('_',$name_column);
            if(count($a)>1){
                $new_name=$a[0];
                for($j=1;$j<count($a);$j++)
                    $new_name.=mb_strtoupper(substr($a[$j],0,1)).substr($a[$j],1);
                $res[$new_name]=$val;
                unset($res[$name_column]);
            }
        }
        foreach($data_type as $name_column=>$val){
            $a=explode('_',$name_column);
            if(count($a)>1){
                $new_name=$a[0];
                for($j=1;$j<count($a);$j++)
                    $new_name.=mb_strtoupper(substr($a[$j],0,1)).substr($a[$j],1);
                $data_type[$new_name]=$val;
                unset($data_type[$name_column]);
            }
        }
    }

    if(is_array($res) and strpos($nameFunction,'Info')== false)
        if(count($res)>0){
            $arr2=array();
            foreach($res as $j=>$val){
                array_push($arr2,$val);
            }
            unset($res);
            $res=$arr2;
        } 


    $time=microtime(true)-$t_start;

    if($lrv==1)
        $arr_result=$arr_result + array('data'=>$res, 'data2'=>$data2, 'data_type'=>$data_type, 'sort'=>$sort, 'search'=>$search, 'colspan'=>$colspan, 'get'=>$get, 'query'=>$query, 'time'=>$time, 'cnt_query'=>$GLOBALS['cnt_query']);
    else
        $arr_result=$arr_result + array('data'=>$res, 'data2'=>$data2, 'data_type'=>$data_type, 'time'=>$time, 'cnt_query'=>$GLOBALS['cnt_query']);

    if($$objName->result['error']){
        // устанавливаем код ответа - 400 Неправильный запрос
        http_response_code(400);
        echo json_encode($$objName->result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }else{
        // устанавливаем код ответа - 200 OK
        http_response_code(200);
        $arr_result=array('result'=>'Ok')+$arr_result;
        //--------------------------- пишем лог в базу ---------------------------------//
        if($$objName->urlParam['search']['id']>0)
            $id=$$objName->urlParam['search']['id'];
        elseif($$objName->urlData[0]>0)
            $id=$$objName->urlData[0];
        elseif($$objName->postParam['input']['id']>0)
            $id=$$objName->postParam['input']['id'];
        if($id>0)
            DBExecuteNew($db_local, "INSERT INTO ".$pref."log (id_person, name, dt, id_field) VALUE ($session_id,'".$nameFunction."','".date("Y-m-d H:i:s",time())."',$id)");
    }

    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}elseif($method === 'POST' && $nameFunction){

    $$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL
    
    $arr_result=array('method'=>$method);
    $arr_result=$arr_result + array('data'=>$$objName->result);

    if($$objName->result['error']){
        // устанавливаем код ответа - 400 Неправильный запрос
        http_response_code(400);
        //        header('HTTP/1.0 400 Bad Request');
    }else{
        // устанавливаем код ответа - 200 OK
        http_response_code(200);
        $arr_result=array('result'=>'Ok')+$arr_result;

        //--------------------------- пишем лог в базу ---------------------------------//
        if($$objName->result['Id']>0)
            DBExecuteNew($db, "INSERT INTO ".$pref."log (id_person, name, dt, id_field) VALUE ($session_id,'".$nameFunction."','".date("Y-m-d H:i:s",time())."',".$$objName->result['Id'].")");
    }

#    $arr_result=array('method'=>$method, 'post'=>$$objName->postParam );
    if(isset($$objName->result['image'])){
        header_remove("Content-Type: application/json; charset=UTF-8");
        if($$objName->result['type']=='jpeg' or $$objName->result['type']=='jpg'){
            header('Content-Type: image/jpeg');
            imagejpeg($$objName->result['image']);
        }elseif($$objName->result['type']=='gif'){
            header('Content-Type: image/gif');
            imagegif($$objName->result['image']);
        }elseif($$objName->result['type']=='bmp'){
            header('Content-Type: image/bmp');
            imagebmp($$objName->result['image']);
        }elseif($$objName->result['type']=='png'){
            header('Content-Type: image/png');
            imagepng($$objName->result['image']);
        }
    }else
        echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}elseif($method === 'PUT' && $nameFunction){
    $id=$$objName->postParam['input']['id'];

    $$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL

    $arr_result=array('method'=>$method);
    $arr_result=$arr_result + array('data'=>$$objName->result);

    if($$objName->result['error']){
        // устанавливаем код ответа - 400 Неправильный запрос
        http_response_code(400);
        $arr_result=array('result'=>'error')+$arr_result;
        //        header('HTTP/1.0 400 Bad Request');
    }else{
        // устанавливаем код ответа - 200 OK
        http_response_code(200);
        $arr_result=array('result'=>'Ok')+$arr_result;

        //--------------------------- пишем лог в базу ---------------------------------//
        if($id>0)
            DBExecuteNew($db, "INSERT INTO ".$pref."log (id_person, name, dt, id_field) VALUE ($session_id,'".$nameFunction."','".date("Y-m-d H:i:s",time())."',".$id.")");
    }

    if($nameFunction=='lpManPhotoPut'){
        echo "<pre>"; print_r($arr_result); echo "</pre>";
    }else
        echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);    
}elseif($method === 'DELETE' && $nameFunction){

    $$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL
    
    $arr_result=array('method'=>$method);
    $arr_result=$arr_result + array('data'=>$$objName->result);

    if($$objName->result['error']){
        // устанавливаем код ответа - 400 Неправильный запрос
        http_response_code(400);
        //        header('HTTP/1.0 400 Bad Request');
    }else{
        // устанавливаем код ответа - 200 OK
        http_response_code(200);
        $arr_result=array('result'=>'Ok')+$arr_result;

        //--------------------------- пишем лог в базу ---------------------------------//
        if($$objName->result['Id']>0)
            DBExecuteNew($db, "INSERT INTO ".$pref."log (id_person, name, dt, id_field) VALUE ($session_id,'".$nameFunction."','".date("Y-m-d H:i:s",time())."',".$$objName->result['Id'].")");
    }

    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}

?>
