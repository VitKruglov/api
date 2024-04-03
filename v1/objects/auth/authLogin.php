<?php
//-------------------------------------------------------------//
//                                                             //
//         проверка логина , пароля                            //
//                                                             //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam))
    $data=$this->urlParam;
if(isset($this->postParam))
    $postParam=$this->postParam;


    if(isset($data['pref']))
        $pref=$data['pref'];

    if(isset($data['id_owner']))
        $id_owner=$data['id_owner'];        
    elseif(isset($postParam['id_owner']))
        $id_owner=$postParam['id_owner'];
    elseif(isset($this->formdata['id_owner']))
        $id_owner=$this->formdata['id_owner'];
    elseif(isset($data['idOwner']))
        $id_owner=$data['idOwner'];        
    elseif(isset($postParam['idOwner']))
        $id_owner=$postParam['idOwner'];
    elseif(isset($this->formdata['idOwner']))
        $id_owner=$this->formdata['idOwner'];

    if(isset($data['login']))
        $login=$data['login'];        
    elseif(isset($postParam['login']))
        $login=$postParam['login'];
    elseif(isset($this->formdata['login']))
        $login=$this->formdata['login'];

    if(isset($data['password']))
        $password=$data['password'];     
    elseif(isset($postParam['password']))
        $password=$postParam['password'];         
    elseif(isset($this->formdata['password']))
        $password=$this->formdata['password'];  
    
    if(isset($data['domain']))
        $domain=$data['domain'];     
    elseif(isset($postParam['domain']))
        $domain=$postParam['domain'];      
    elseif(isset($this->formdata['domain']))
        $domain=$this->formdata['domain'];      

    if(isset($data['remember']))
        $remember=$data['remember'];      
    elseif(isset($postParam['remember']))
        $remember=$postParam['remember'];  
    elseif(isset($this->formdata['remember']))
        $remember=$this->formdata['remember'];  

    if(isset($data['remote_addr']))
        $remote_addr=$data['remote_addr'];   
    elseif(isset($postParam['remote_addr']))
        $remote_addr=$postParam['remote_addr'];          
    elseif(isset($this->formdata['remote_addr']))
        $remote_addr=$this->formdata['remote_addr'];    

    if(isset($data['cookie']))
        $cookie=$data['cookie'];   
    elseif(isset($postParam['cookie']))
        $cookie=$postParam['cookie'];           
    elseif(isset($this->formdata['cookie']))
        $cookie=$this->formdata['cookie'];   

    $session_timeout=time()+30*60; //30 мин
    $refresh_timeout=time()+30*24*60*60;

#    if($domain=='outsourcing.complat.ru') $domain='ma-test-1.rdtn.ru';

#    $this->result=array('auth'=>1,'login'=>$login, 'pass'=>$password,'id_owner'=>$id_owner);
    

	// check login/password
	if ((!$password or mb_strlen($password)>25) and !$cookie) {
        $this->result=array('auth'=>0,'message'=>401,'urlParam'=>$this->urlParam,'postParam'=>$this->postParam,'server_name'=>$_SERVER['SERVER_NAME'],'post'=>$_POST, 'urlData'=>$this->$urlData,'formdata'=>$this->$formData);
	} else  {
        if($password){   // аутентификация по паролю
            list($id_person, $pass)=DBQueryNew($this->conn, "SELECT p.id, p.password FROM persons as p WHERE p.name='$login' AND p.id_group=1");
            $result = password_verify($password, $pass);
            if($result)
                $qa=DBQueryNew($this->conn, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.timezone FROM persons as p WHERE p.id=$id_person");
        }elseif($cookie)// аутентификация по кукисам
            $qa=DBQueryNew($this->conn, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.timezone FROM persons as p WHERE p.name='$login' AND p.cookie='".addslashes($cookie)."' AND p.id_group=1");        

		if(!$qa[0]) {
			//ищем пользователя по аутсорсерам
            if(isset($domain))
                $servername=$domain;
            else
			    $servername = strtolower($_SERVER['SERVER_NAME']);
                
            if($id_owner>0)
			    list($id_owner, $ownerrealname)=DBQueryNew($this->conn, "SELECT id, realname FROM owner WHERE id=$id_owner");
            else
                list($id_owner, $ownerrealname)=DBQueryNew($this->conn, "SELECT id, realname FROM owner WHERE domain='$servername' ORDER BY id DESC");

			if($id_owner>0){
				$pref=$id_owner."_";
                $db_ak = DBConnect($tmpl, $this->config, $pref);

                if($password){   // аутентификация по паролю
                    list($id_person, $pass)=DBQueryNew($db_ak, "SELECT p.id, p.password FROM ".$pref."persons as p WHERE p.name='$login' AND p.id_group=1");
                    $result = password_verify($password, $pass);
                    if($result)
				        $qa = DBQueryNew($db_ak, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.timezone FROM ".$pref."persons as p WHERE p.id=$id_person");
                }elseif($cookie)// аутентификация по кукисам
                    $qa = DBQueryNew($db_ak, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.timezone FROM ".$pref."persons as p WHERE p.name='$login' AND p.cookie='".addslashes($cookie)."' AND p.id_group=1");

				if(!$qa[0]) {
                    $this->result=array('auth'=>0,'message'=>401, 'id_owner'=>$id_owner);
				}else{
                    $key = '';
                    $key_r = '';
                    $saltLength = 60; //длина соли
                    $arr_chr=array();
                    for($i=48;$i<=57;$i++)
                        $arr_chr[count($arr_chr)]=chr($i);
                    for($i=65;$i<=90;$i++)
                        $arr_chr[count($arr_chr)]=chr($i);
                    for($i=97;$i<=122;$i++)
                        $arr_chr[count($arr_chr)]=chr($i);
                    for($i=0; $i<$saltLength; $i++) 
                        $key.= $arr_chr[mt_rand(0,61)]; //символ из ASCII-table
                    for($i=0; $i<$saltLength; $i++) 
                        $key_r.= $arr_chr[mt_rand(0,61)]; //символ из ASCII-table
                    
                	DBExecuteNew($db_ak, "UPDATE ".$pref."persons SET remember_token='".addslashes($key)."', session_timeout=$session_timeout WHERE ".$pref."persons.name='$login'");

                    if($password)
                        DBExecuteNew($db_ak, "UPDATE ".$pref."persons SET refresh_token='".addslashes($key_r)."', refresh_timeout=$refresh_timeout WHERE ".$pref."persons.name='$login'");

                    if($remember==1){
                        //Сформируем случайную строку для куки (используем функцию generateSalt):
                        $key2 = '';
                        $saltLength = 8; //длина соли
                        for($i=0; $i<$saltLength; $i++) {
                            $key2 .= chr(mt_rand(33,126)); //символ из ASCII-table
                        }
                        DBExecuteNew($db_ak, "UPDATE ".$pref."persons SET cookie='".addslashes($key2)."' WHERE ".$pref."persons.name='$login'");                
                    }

                    if($remote_addr)
                        DBExecuteNew($db_ak, "UPDATE ".$pref."persons SET ip='".$remote_addr."' WHERE ".$pref."persons.name='$login'");                

                    $arr['id']       = $qa[0];
                    $arr['login']    = $login;
                    $arr['idUnit']  = $qa[1];
                    $arr['realName']  = $qa[2];
                    if(strlen($qa[3])>0)
                        $arr['scriptName']	= $qa[3];
                    else
                        list($arr['scriptName'])=DBQueryNew($db_ak, "SELECT script_name FROM ".$pref."unit_name WHERE id='".$qa[1]."'");
                    
                    $arr['idOwner']  = $id_owner;
                    $arr['timezone']  = $qa[4];
                    $arr['token']  = $key;
                    $arr['tokenRefresh']  = $key_r;
                    $arr['cookie']  = $key2;
                    $arr['sessionTimeout'] = $session_timeout;
                    $arr['refreshTimeout'] = $refresh_timeout;

            //        list($arr['edit'])=DBQueryNew($this->conn, "SELECT action FROM ".$pref."permis_page WHERE id_unit=".$qa[1]." and name='button_save' UNION SELECT 1 LIMIT 1");

                    if($arr['id']==1) $arr['edit']=1;

                    $this->result=array('auth'=>1)+$arr;
                }
			}else{
                $this->result=array('auth'=>0,'message'=>401,'id_owner'=>$id_owner);
	    	}
		}else{
            //формируем токены:
		    $key = '';
            $key_r = '';
		    $saltLength = 60; //длина соли
            $arr_chr=array();
            for($i=48;$i<=57;$i++)
                $arr_chr[count($arr_chr)]=chr($i);
            for($i=65;$i<=90;$i++)
                $arr_chr[count($arr_chr)]=chr($i);
            for($i=97;$i<=122;$i++)
                $arr_chr[count($arr_chr)]=chr($i);
            for($i=0; $i<$saltLength; $i++) 
                $key.= $arr_chr[mt_rand(0,61)]; //символ из ASCII-table
            for($i=0; $i<$saltLength; $i++) 
                $key_r.= $arr_chr[mt_rand(0,61)]; //символ из ASCII-table

                  
            DBExecuteNew($this->conn, "UPDATE persons SET remember_token='".addslashes($key)."', session_timeout=$session_timeout WHERE persons.name='$login'");

            if($password)
                DBExecuteNew($this->conn, "UPDATE persons SET refresh_token='".addslashes($key_r)."', refresh_timeout=$refresh_timeout WHERE persons.name='$login'");

            if($remember==1){
                //Сформируем случайную строку для куки (используем функцию generateSalt):
                $key2 = '';
                $saltLength = 8; //длина соли
                for($i=0; $i<$saltLength; $i++) {
                    $key2 .= chr(mt_rand(33,126)); //символ из ASCII-table
                }
                DBExecuteNew($this->conn, "UPDATE persons SET cookie='".addslashes($key2)."' WHERE persons.name='$login'");                
            }

            if($remote_addr)
                DBExecuteNew($this->conn, "UPDATE persons SET ip='".$remote_addr."' WHERE persons.name='$login'");   

            $arr['id']       = $qa[0];
            $arr['login']    = $login;
            $arr['idUnit']  = $qa[1];
            $arr['realName']  = $qa[2];
            if(strlen($qa[3])>0)
                $arr['scriptName']	= $qa[3];
		    else{
			    list($arr['scriptName'])=DBQueryNew($this->conn, "SELECT script_name FROM unit_name WHERE id='".$qa[1]."'");
		    }
            $arr['idOwner']  = $id_owner;
            $arr['timezone']  = $qa[4];
            $arr['token']  = $key;
            $arr['tokenRefresh']  = $key_r;
            $arr['cookie']  = $key2;
            $arr['sessionTimeout'] = $session_timeout;
            $arr['refreshTimeout'] = $refresh_timeout;
            
            list($arr['edit'])=DBQueryNew($this->conn, "SELECT action FROM permis_page WHERE id_unit=".$qa[1]." and name='button_save' UNION SELECT 1 LIMIT 1");

            if($arr['id']==1) $arr['edit']=1;

            $this->result=array('auth'=>1)+$arr;
        }

        
/*
        #------------составляем сетку прав доступа--------------------
        if($arr['id']){
           $res_permission=DBFetchNew($this->conn, "SELECT id,permission,script FROM ".$pref."permis_list ORDER BY id");
            for($j=0;$j<count($res_permission);$j++){
                list($id_permis,$permis_default,$script)=$res_permission[$j];
                list($permis_person)=DBQueryNew($this->conn, "SELECT id_person FROM ".$pref."permission WHERE id_permis='$id_permis' and id_person='".$arr['id']."'");
                list($permis_unit)=DBQueryNew($this->conn, "SELECT id_unit FROM ".$pref."permission WHERE id_permis='$id_permis' and id_unit='".$arr['id_unit']."'");
                list($permis_group)=DBQueryNew($this->conn, "SELECT ".$pref."permission.id_group FROM ".$pref."permission WHERE id_permis='$id_permis' and ".$pref."permission.id_group='".$arr['id_group']."'");
                if($permis_default==1) $rezult=1;	#по умолчанию разрешено 
                else $rezult=-1;			#по умолчанию запрещено
                if($permis_person or $permis_unit or $permis_group) $rezult=-$rezult;
                if ($rezult==1)
                    $arr['permission'][$script]=1;
                else
                    $arr['permission'][$script]=0;
            }

            $this->result=$this->result+array('permission'=>$arr['permission']);
            
        }
*/
	}
?>