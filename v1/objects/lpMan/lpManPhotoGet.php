<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех физических лицах         //
//                                                             //
//-------------------------------------------------------------//


if(isset($this->postParam)){
    $data=$this->postParam;
    $session_id=$data['session_id'];
    if(isset($data['pref']))
        $pref=$data['pref'];
}
list($name,$type)=explode('.',$data['input']['search']['fileName']);
$type=strtolower($type);

// файл, который мы проверяем
$urlf = "https://".$this->domain."/content/photos/".$data['input']['search']['id']."/".$data['input']['search']['fileName'];
$Headers = @get_headers($urlf);
// проверяем ли ответ от сервера с кодом 200 - ОК

if(strpos($Headers[0],'200')) {
    if($type=='jpeg' or $type=='jpg'){
        $im=imagecreatefromjpeg($urlf);
        $exist=1;
    }elseif($type=='gif'){
        $im=imagecreatefromgif($urlf);
        $exist=1;
    }elseif($type=='png'){
        $im=imagecreatefrompng($urlf);
        $exist=1;
    }elseif($type=='bmp'){
        $im=imagecreatefrombmp($urlf);
        $exist=1;
    }
} else {
    $im=imagecreatefromjpeg('404.jpg');
    $type='jpg';
}

$this->result=array('image'=>$im,'type'=>$type,'headers'=>$Headers[0]);
#$this->result=array('result'=>"https://".$this->domain."/content/photos/".$data['input']['search']['id']."/".$data['input']['search']['fileName']);
?>