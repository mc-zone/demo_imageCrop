<?php // Plug-in 15: Image Crop

// This is an executable example with additional code supplied
// To obtain just the plug-ins please click on the Download link
// You'll need a jpeg image file called photo.jpg in this folder

/*$image = imagecreatefromjpeg("photo.jpg");
$copy =  PIPHP_ImageCrop($image, 100, 0, 110, 1400);

if (!$copy) echo "Crop failed: Argument(s) out of bounds";
else
{
   imagejpeg($copy, "photo3.jpg");
   echo "<img src='photo.jpg' align=left> ";
   echo "Cropped at 100,0<br />with width / height";
   echo "<br />of 110/140 pixels<br /><br />";
   echo "<img src='photo3.jpg'>";
}
*/
function PIPHP_ImageCrop($image, $x, $y, $w, $h)
{
   // Plug-in 15: Image Crop
   //
   // This plug-in takes a GD image and returns a cropped
   // version of it. If any arguments are out of the
   // image bounds then FALSE is returned. The arguments
   // required are:
   //
   //    $image:   The image source
   //    $x & $y:  The top-left corner
   //    $w & $h : The width and height

   $tw = imagesx($image);
   $th = imagesy($image);

   if ($x > $tw || $y > $th || $w > $tw || $h > $th)
      return FALSE;

   $temp = imagecreatetruecolor($w, $h);
   imagecopyresampled($temp, $image, 0, 0, $x, $y,
      $w, $h, $w, $h);
   return $temp;
}

?>

