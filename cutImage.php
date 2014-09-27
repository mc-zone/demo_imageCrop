<?php
header("Content-type: text/html; charset=utf-8");
require_once('PIPHP_ImageCrop.php');

$filename = $_POST['name'];

$file = rtrim(dirname(__FILE__),'/').'/upload/'.$filename;
$cutPicfolder = 'upload/cut/';
$cutPicPath = rtrim(dirname(__FILE__),'/').'/'.$cutPicfolder;

$urlPath = get_current_url();
$urlPath = rtrim($urlPath,'/').'/';

$x1 = $_POST['offsetLeft'];
$y1 = $_POST['offsetTop'];
$width = $_POST['width'];
$height = $_POST['height'];

$type = exif_imagetype($file);

$support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);

if(!in_array($type, $support_type,true)) {
    $data['status'] = 0;
    $data['info'] =  "不支持的格式！";
    echo json_encode($data);
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

        echo json_encode($data);
        exit;
    }

    $copy = PIPHP_ImageCrop($image, $x1, $y1, $width, $height);

    $newName = 'cut_'.$filename;
    $targetPic = $cutPicPath.$newName;

    //TODO 目录与写文件检测
    if(false === imagejpeg($copy, $targetPic) ){
        $data['status'] = 0;
        $data['info'] =  "生成裁剪图片失败！请确认保存路径存在且可写！";
        echo json_encode($data);
        exit;
    } 

    @unlink($file);

    $data['status'] = 1;
    $data['path'] = $cutPicfolder.$newName;
    $data['name'] = $newName;
    $data['url']  = $urlPath.$data['path'];

    echo json_encode($data);
    exit;

}

function get_current_url($strip = true){
    // filter function
    $filter = function($input, $strip) {
        $input = urldecode($input);
        $input = str_ireplace(array("\0", '%00', "\x0a", '%0a', "\x1a", '%1a'), '', $input);
        if ($strip) {
            $input = strip_tags($input);
        }
        $input = htmlentities($input, ENT_QUOTES, 'UTF-8'); // or whatever encoding you use...
        return trim($input);
    };

    $url = array();
    // set protocol
    $url['protocol'] = 'http://';
    if (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] == 1)) {
        $url['protocol'] = 'https://';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        $url['protocol'] = 'https://';
    }
    // set host
    $url['host'] = $_SERVER['HTTP_HOST'];
    // set request uri in a secure way
    $url['request_uri'] = $filter( dirname($_SERVER['REQUEST_URI']), $strip);
    return join('', $url);
}
