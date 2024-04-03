<?php
//-------------------------------------------------------------//
//                                                             //
//        загрузка фото физ.лица                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();
if(isset($this->formdata['file']) and isset($this->formdata['idMan'])){
    $uploaddir="ftp://".$this->config[$pref."login"].":".$this->config[$pref."password"]."@".$this->domain."/content";
    mkdir($uploaddir, 0777);
    mkdir($uploaddir."/photos/", 0777);
    mkdir($uploaddir."/photos/".$this->formdata['idMan'], 0777);


    $uploaddir=$uploaddir."/photos/".$this->formdata['idMan']."/";
  
    $x1=explode('base64,',$this->formdata['file']);
    $fp = base64_decode($x1[1]);

    if (file_exists($uploaddir.$this->formdata['name'].".".$this->formdata['type']))
        unlink($uploaddir.$this->formdata['name'].".".$this->formdata['type']);
    $f = fopen($uploaddir.$this->formdata['name'].".".$this->formdata['type'],'w');
    fwrite($f,$fp);
    fclose($f);

    if(strtolower($this->formdata['type'])=='jpg' or strtolower($this->formdata['type'])=='jpeg')
        $im1=imagecreatefromjpeg($uploaddir.$this->formdata['name'].".".$this->formdata['type']);
    elseif(strtolower($this->formdata['type'])=='png')
        $im1=imagecreatefrompng($uploaddir.$this->formdata['name'].".".$this->formdata['type']);
    elseif(strtolower($this->formdata['type'])=='bmp')
        $im1=imagecreatefrombmp($uploaddir.$this->formdata['name'].".".$this->formdata['type']);
    elseif(strtolower($this->formdata['type'])=='gif')
        $im1=imagecreatefromgif($uploaddir.$this->formdata['name'].".".$this->formdata['type']);

    if($im1 === false){
        $this->result=array('error'=>error_get_last());
    }else{
        $k1=512/imagesx($im1);
        $k2=512/imagesy($im1);
        $k=$k1>$k2?$k1:$k2;
        $w=intval(imagesx($im1)*$k);
        $h=intval(imagesy($im1)*$k);
        
        $im=imagecreatetruecolor($w,$h);
        imagecopyresized($im,$im1,0,0,0,0,$w,$h,imagesx($im1),imagesy($im1));
        
        $im2=imagecreatetruecolor(512,512);
        if(imagesx($im1)>imagesy($im1))
            imagecopyresized($im2,$im,0,0,imagesx($im)/2-256,0,512,512,512,512);
        else
            imagecopyresized($im2,$im,0,0,0,imagesy($im)/2-256,512,512,512,512);
        
        if(strtolower($this->formdata['type'])=='jpg' or strtolower($this->formdata['type'])=='jpeg')
            imagejpeg($im2,$uploaddir."512_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='png')
            imagepng($im2,$uploaddir."512_".$this->formdata['name'].".".$this->formdata['type']);
        elseif(strtolower($this->formdata['type'])=='bmp')
            imagebmp($im2,$uploaddir."512_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='gif')
            imagegif($im2,$uploaddir."512_".$this->formdata['name'].".".$this->formdata['type']);

        $k1=128/imagesx($im1);
        $k2=128/imagesy($im1);
        $k=$k1>$k2?$k1:$k2;
        $w=intval(imagesx($im1)*$k);
        $h=intval(imagesy($im1)*$k);
        
        $im3=imagecreatetruecolor($w,$h);
        imagecopyresized($im3,$im1,0,0,0,0,$w,$h,imagesx($im1),imagesy($im1));
        
        $im4=imagecreatetruecolor(128,128);
        if(imagesx($im1)>imagesy($im1))
            imagecopyresized($im4,$im3,0,0,imagesx($im3)/2-64,0,128,128,128,128);
        else
            imagecopyresized($im4,$im3,0,0,0,imagesy($im3)/2-64,128,128,128,128);
        
        if(strtolower($this->formdata['type'])=='jpg' or strtolower($this->formdata['type'])=='jpeg')
            imagejpeg($im4,$uploaddir."128_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='png')
            imagepng($im4,$uploaddir."128_".$this->formdata['name'].".".$this->formdata['type']);
        elseif(strtolower($this->formdata['type'])=='bmp')
            imagebmp($im4,$uploaddir."128_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='gif')
            imagegif($im4,$uploaddir."128_".$this->formdata['name'].".".$this->formdata['type']);

        $k1=32/imagesx($im1);
        $k2=32/imagesy($im1);
        $k=$k1>$k2?$k1:$k2;
        $w=intval(imagesx($im1)*$k);
        $h=intval(imagesy($im1)*$k);
        
        $im5=imagecreatetruecolor($w,$h);
        imagecopyresized($im5,$im1,0,0,0,0,$w,$h,imagesx($im1),imagesy($im1));
        
        $im6=imagecreatetruecolor(32,32);
        if(imagesx($im1)>imagesy($im1))
            imagecopyresized($im6,$im5,0,0,imagesx($im5)/2-16,0,32,32,32,32);
        else
            imagecopyresized($im6,$im5,0,0,0,imagesy($im5)/2-16,32,32,32,32);
        
        if(strtolower($this->formdata['type'])=='jpg' or strtolower($this->formdata['type'])=='jpeg')
            imagejpeg($im6,$uploaddir."32_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='png')
            imagepng($im6,$uploaddir."32_".$this->formdata['name'].".".$this->formdata['type']);
        elseif(strtolower($this->formdata['type'])=='bmp')
            imagebmp($im6,$uploaddir."32_".$this->formdata['name'].".".$this->formdata['type'],80);
        elseif(strtolower($this->formdata['type'])=='gif')
            imagegif($im6,$uploaddir."32_".$this->formdata['name'].".".$this->formdata['type']);
        
        imagedestroy($im);
        imagedestroy($im1);
        imagedestroy($im2);
        imagedestroy($im3);
        imagedestroy($im4);
        imagedestroy($im5);
        imagedestroy($im6);

        $url="https://".$this->formdata['domain']."/content/photos/".$this->formdata['idMan']."/".$this->formdata['name'].".".$this->formdata['type'];

        $query="UPDATE ".$pref.$this->table_name." SET photo='".$this->formdata['name'].".".$this->formdata['type']."' WHERE id=".$this->formdata['idMan'];
        $result=DBExecuteNew($this->conn, $query);

        $this->result=array('url'=>$this->formdata);
    }
}
?>