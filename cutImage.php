<?php
header("Content-type: text/html; charset=utf-8");
require_once('PIPHP_ImageCrop.php');

$filename = $_POST['name'];

$file = rtrim(dirname(__FILE__),'/').'/upload/'.$filename;
$cutPicfolder = 'upload/cut/';
$cutPicPath = rtrim(dirname(__FILE__),'/').'/'.$cutPicfolder;

$x1 = $_POST['offsetLeft'];
$y1 = $_POST['offsetTop'];
$width = $_POST['width'];
$height = $_POST['height'];

$type = exif_imagetype($file);

$support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);

if(!in_array($type, $support_type,true)) {
    $data['status'] = 0;
    $data['info'] =  "不支持的格式！";
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;
}else{
    switch($type) {
    case IMAGETYPE_JPEG :
        $image = imagecreatefromjpeg($file);
        break;
    case IMAGETYPE_PNG :
        $image = imagecreatefrompng($file);
        break;
    case IMAGETYPE_GIF :
        $image = imagecreatefromgif($file);
        break;
    default:
        $data['status'] = 0;
        $data['info'] =  "不支持的格式！";

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    }

    $copy = PIPHP_ImageCrop($image, $x1, $y1, $width, $height);

    $newName = 'cut_'.$filename;
    $targetPic = $cutPicPath.$newName;

    //TODO 目录与写文件检测
    if(false === imagejpeg($copy, $targetPic) ){
        $data['status'] = 0;
        $data['info'] =  "生成裁剪图片失败！请确认保存路径存在且可写！";
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
        exit;
    } 

    @unlink($file);

    $data['status'] = 1;
    $data['path'] =  $cutPicfolder.$newName;
    $data['name'] =  $newName;
    $data['url'] =  'http://'.rtrim($_SERVER['HTTP_HOST'],'/').'/'.$data['path'];

    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    exit;

}
