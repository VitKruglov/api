<?php
//-------------------------------------------------------------//
//                                                             //
//         refresh по tokenRefresh                            //
//                                                             //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam))
    $data=$this->urlParam;
if(isset($this->postParam))
    $postParam=$this->postParam;

    if(isset($data['pref']))
        $pref=$data['pref'];

    if(isset($data['tokenRefresh']))
        $tokenRefresh=$data['tokenRefresh'];        
    elseif(isset($postParam['tokenRefresh']))
        $tokenRefresh=$postParam['tokenRefresh'];

    $session_timeout=time()+24*60*60;
    $refresh_timeout=time()+30*24*60*60;

	// check login/password
	if (!$tokenRefresh or $tokenRefresh=='' or $tokenRefresh==NULL) {
        $this->result=array('auth'=>0,'message'=>401,'urlParam'=>$this->urlParam,'postParam'=>$this->postParam,'server_name'=>$_SERVER['SERVER_NAME'],'post'=>$_POST, 'urlData'=>$this->$urlData,'formdata'=>$this->$formData);
	} else  {

        $qa=DBQueryNew($this->conn, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.id_owner, p.timezone, p.name FROM persons as p WHERE p.refresh_token='".addslashes($tokenRefresh)."' AND p.id_group=1 and p.refresh_timeout>UNIX_TIMESTAMP()");        
        
		if(!$qa[0]) {
			//ищем пользователя по аутсорсерам
			$servername = strtolower($_SERVER['SERVER_NAME']);
			list($id_owner, $ownerrealname)=DBQueryNew($this->conn, "SELECT id, realname FROM owner WHERE domain='$servername'");
			if($id_owner>0){
                $pref=$id_owner."_";
                $qa = DBQueryNew($this->conn, "SELECT p.id, p.id_unit,p.realname, p.script_name, p.id_owner, p.timezone, p.name FROM ".$pref."persons as p WHERE refresh_token='".addslashes($tokenRefresh)."' AND p.id_group=1 and p.id_owner=$id_owner and p.refresh_timeout>UNIX_TIMESTAMP()");

				if(!$qa[0]) {
                    $this->result=array('auth'=>0,'message'=>401);
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
                    
                	DBExecuteNew($this->conn, "UPDATE ".$pref."persons SET remember_token='".addslashes($key)."', session_timeout=$session_timeout, refresh_token='".addslashes($key_r)."', refresh_timeout=$refresh_timeout WHERE ".$pref."persons.name='".$qa[6]."'");

                    $arr['id']       = $qa[0];
                    $arr['login']    = $qa[6];
                    $arr['idUnit']  = $qa[1];
                    $arr['realName']  = $qa[2];
                    if(strlen($qa[3])>0)
                        $arr['scriptName']	= $qa[3];
                    else
                        list($arr['scriptName'])=DBQueryNew($this->conn, "SELECT script_name FROM ".$pref."unit_name WHERE id='".$qa[1]."'");
                    
                    $arr['idOwner']  = $qa[4];
                    $arr['timezone']  = $qa[5];
                    $arr['token']  = $key;
                    $arr['tokenRefresh']  = $key_r;
                    $arr['cookie']  = $key2;
                    $arr['sessionTimeout'] = $session_timeout;
                    $arr['refreshTimeout'] = $refresh_timeout;

                    if($arr['id']==1) $arr['edit']=1;

                    $this->result=array('auth'=>1)+$arr;
                }
			}else{
                $this->result=array('auth'=>0,'message'=>401);
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

                  
            DBExecuteNew($this->conn, "UPDATE persons SET remember_token='".addslashes($key)."', session_timeout=$session_timeout, refresh_token='".addslashes($key_r)."', refresh_timeout=$refresh_timeout WHERE persons.name='".$qa[6]."'");

            $arr['id']       = $qa[0];
            $arr['login']    = $qa[6];
            $arr['idUnit']  = $qa[1];
            $arr['realName']  = $qa[2];
            if(strlen($qa[3])>0)
                $arr['scriptName']	= $qa[3];
		    else{
			    list($arr['scriptName'])=DBQueryNew($this->conn, "SELECT script_name FROM unit_name WHERE id='".$qa[1]."'");
		    }
            $arr['idOwner']  = $qa[4];
            $arr['timezone']  = $qa[5];
            $arr['token']  = $key;
            $arr['tokenRefresh']  = $key_r;
            $arr['cookie']  = $key2;
            $arr['sessionTimeout'] = $session_timeout;
            $arr['refreshTimeout'] = $refresh_timeout;
            
            list($arr['edit'])=DBQueryNew($this->conn, "SELECT action FROM permis_page WHERE id_unit=".$qa[1]." and name='button_save' UNION SELECT 1 LIMIT 1");

            if($arr['id']==1) $arr['edit']=1;

            $this->result=array('auth'=>1)+$arr;
            
        }

	}
    
?>