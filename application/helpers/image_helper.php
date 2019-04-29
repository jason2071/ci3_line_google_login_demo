<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function resize_cache($picture, $resize, $justurl=false, $default='')
{
	$media_path = config_item('media_path');
	$media_url = config_item('media_url');

	$picture = ltrim($picture, '/ud/');
	$picture = ltrim($picture, '/');

	if (empty($default)) {
		$default = config_item('default_picture');
	}

	$SIZESUBFIX = '-';  // เปลี่ยนจาก _ เป็น - เมื่อ 21 Jan 2015

	if($picture!=""){
		if (is_file($media_path.$picture))
		{
			$size = @getimagesize($media_path.$picture);
			if (!$size)
			{
				$picture = $default;
				$size = @getimagesize($media_path.$picture);
			}
		}
		else
		{
			$picture = $default;
			$size = @getimagesize($media_path.$picture);
		}
	}
	else {
		return ''; // เช็คค่า picture หากไม่มีรูป ให้ส่งค่า ว่าง 
		//die();
	}
	switch ($size[2])
	{
		case 1 : $extension = 'gif'; break;
		case 2 : $extension = 'jpg'; break;
		case 3 : $extension = 'png'; break;
		default :
			$picture = 'default.jpg';
			$extension = 'jpg';
	}

	$filename = $picture;
	if (strpos($filename, '.') !== false && strpos($filename, '.') > strlen($filename) - 5)
	{
		$filename_noext = substr($filename, 0, strrpos($filename, '.'));
	}
	else
	{
		$filename_noext = $filename;
	}
	$chk_resize = true;
	$more_command = '';
	if (strpos($resize, 'x') === false)
	{
		$width = intval($resize);
		if ($size[0] >= $size[1] && $size[0] >= $width) // landscape
		{
			$height = ceil(($size[1]/$size[0])*$width);
		}
		else if ($size[1] >= $size[0] && $size[1] >= $width) // portrait
		{
			$height = $width;
			$width = ceil(($size[0]/$size[1])*$width);
		}
		else // too small
		{
			$width = $size[0];
			$height = $size[1];
			$chk_resize = false;
		}
		$resize_command = $width.'x'.$height.'^';
		$resize_filename = $resize;
	}
	else
	{
		$more_command = ' -gravity center -crop '.$resize.'-0-0';
		$resize_filename = $resize;

		list($width, $height) = explode('x', $resize);

		if ($size[0] >= $size[1]) // landscape
		{
			$chk_width = ceil(($size[0]/$size[1])*$height);
			if ($chk_width < $width) {
				$height = ceil(($size[1]/$size[0])*$width);
			}
			else {
				$width = $chk_width;
			}
		}
		else if ($size[1] >= $size[0]) // portrait
		{
			$chk_height = ceil(($size[1]/$size[0])*$width);
			if ($chk_height < $height) {
				$width = ceil(($size[0]/$size[1])*$height);
			}
			else {
				$height = $chk_height;
			}
		}

		$resize_command = $width.'x'.$height;
	}

	if ($chk_resize) {
		$filename = $resize_filename.'/'.$filename_noext.'.'.$extension;

		if (!is_file($media_path.'resize-cache/'.$filename))
		{
			$dir_filename = substr($filename, 0, strrpos($filename, '/'));
			if (!is_dir($media_path.'resize-cache/'.$dir_filename)) {
				createmediapath('resize-cache/'.$dir_filename);
			}
			$cmdResize = IMAGEMAGICK_CONVERT.' "'.$media_path.$picture.'" -resize '.$resize_command.' -auto-orient'.$more_command.' "'.$media_path.'resize-cache/'.$filename.'"';
			exec($cmdResize);
		}

		$url_filename = $media_url.'resize-cache/'.$filename;
	}
	else {
		$url_filename = $media_url.$filename;
	}

	if ($justurl)
	{
		return $url_filename;
	}
	else
	{
		return '<img src="'.$url_filename.'" />';
	}
}

function resize_replace($picture, $resize)
{
	$media_path = config_item('media_path');

	if ($picture!=""){
		if (is_file($media_path.$picture)) {
			$size = @getimagesize($media_path.$picture);
			if (!$size) {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

	if (strpos($resize, 'x') === false) {
		$more_attr = '';
		$resize = $resize.'x'.$resize.'>';
	}
	else {
		$more_attr = ' -crop '.$resize.'-0-0';
		$resize .= '^';
	}

	$cmdResize = IMAGEMAGICK_CONVERT.' "'.$media_path.$picture.'" -resize '.$resize.' -auto-orient -gravity center'.$more_attr.' "'.$media_path.$picture.'"';
	exec($cmdResize);
}

function resize_crop($picture, $resize)
{
	$media_path = config_item('media_path');

	if ($picture!=""){
		if (is_file($media_path.$picture)) {
			$size = @getimagesize($media_path.$picture);
			if (!$size) {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

	if (strpos($resize, 'x') === false) {
		$width = $height = $resize;
		$resize = $width.'x'.$height;
	}
	else {
		list($width, $height) = explode('x', $resize);
	}

	if ($size[0] >= $size[1] && $size[0] >= $width) // landscape
	{
		$width = ceil(($size[0]/$size[1])*$height);
	}
	else if ($size[1] >= $size[0] && $size[1] >= $height) // portrait
	{
		$height = ceil(($size[1]/$size[0])*$width);
	}

	$cmdResize = IMAGEMAGICK_CONVERT.' "'.$media_path.$picture.'" -resize '.$width.'x'.$height.' -auto-orient -gravity center -crop '.$resize.'-0-0 "'.$media_path.$picture.'"';
	exec($cmdResize);
}

function image_exists($url){
	$file_headers = @get_headers($url,1);
	if(!$file_headers) return false;
	if(preg_match("|404|", $file_headers[0]) || preg_match("|400|", $file_headers[0])) {
		return false;
	}
	else if (preg_match("|200|", $file_headers[0])) {
		if (strpos($file_headers['Content-Type'], "image") !== false) {
			return true;
		}
		else {
			return false;
		}
	}
	else if (preg_match("|302|", $file_headers[0]))
	{
		if (is_array($file_headers['Content-Type']))
		{
			$chk_image = false;
			foreach ($file_headers['Content-Type'] as $value)
			{
				if (strpos($value, "image") !== false)
				{
					return true;
				}
			}
			return false;
		}
		else
		{
			if (strpos($file_headers['Content-Type'], "image") !== false) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	else {
		return false;
	}
}

function get_picture($url, $filename)  {
	$dst_img = @imagecreatefromjpeg($url);
	if ($dst_img) {
		imagejpeg($dst_img, $filename, 100);
		return true;
	}
	else return false;
}

function get_picturepng($url, $filename)  {
	$dst_img = @imagecreatefrompng($url);
	if ($dst_img) {
		imagepng($dst_img, $filename, 100);
		return true;
	}
	else return false;
}

function get_picture2($url, $filename)
{
	$ch = curl_init($url);
	$fp = fopen($filename, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	// fix bug facebook -- 28 Mar 2018
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla');
	// end
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	if ($result === false) {
		return false;
	}
	else {
		return true;
	}
}

function genImageFacescan($data, $output, $score)
{
 	$images = $data;
    // Allocate new image
    $img = imagecreatetruecolor(600, 315);
    // Make alpha channels work
    imagealphablending($img, true);
    imagesavealpha($img, true);

	foreach ( $images as $i=>$fn ) 
	{
        // Load image
        if ( $i == 0 ) {

			// Resize place image 600x315
            $cur = imagecreatefromstring(file_get_contents($fn));
            imagealphablending($cur, true);
            imagesavealpha($cur, true);
			imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
			
		} else if ( $i == 1 ) {

			// Add blue background overlay place image
			$cur = imagecreatefromstring(file_get_contents($fn));
			imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
			
		} else if ( $i == 2 ) {
/*
			// Add player profile circle
			$image_s = imagecreatefromstring(file_get_contents($fn));
			$width = imagesx($image_s); 
			$height = imagesx($image_s);

			$newwidth = 180;
			$newheight = 180;
			
			$image = imagecreatetruecolor($newwidth, $newheight);
			imagealphablending($image, true);
			imagecopyresampled($image, $image_s, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

			$mask = imagecreatetruecolor($newwidth, $newheight);

			$transparent = imagecolorexact($mask, 255, 0, 0);
			imagecolortransparent($mask, $transparent);

			imagefilledellipse($mask, $newwidth/2, $newheight/2, $newwidth, $newheight, $transparent);

			$red = imagecolorexact($mask, 0, 0, 0);
			imagecopymerge($image, $mask, 0, 0, 0, 0, $newwidth, $newheight, 100);
			imagecolortransparent($image, $red);
			imagefill($image, 0, 0, $red);
			
			imagecopy($img, $image, 210, 105, 0, 0, $newwidth, $newheight);
*/
			$cur = imagecreatefromstring(file_get_contents($fn));
			imagecopy($img, $cur, 275, 200, 0, 0, 80, 80);

		} else if ( $i == 3 ) {

			$cur = imagecreatefromstring(file_get_contents($fn));
			imagecopy($img, $cur, 400, 150, 0, 0, 30, 30);

		}
	
	}

	$x1 = 380; $y1 = 250; $x2 = 580; $y2 = 250;
	$color = imagecolorallocate($img, 0, 0, 0);
	imagelinethick($img, $x1, $y1, $x2, $y2, $color, 16);

	if ( $score > 79 ) {
		$color = imagecolorallocate($img, 100, 160, 0);
	} else if ( $score < 31 ) {
		$color = imagecolorallocate($img, 255, 25, 40);
	} else {
		$color = imagecolorallocate($img, 240, 160, 0);
	}
	$percent = ((($x2-$x1)/100)*$score)+$x1;
	imagelinethick($img, $x1, $y1, $percent, $y2, $color, 12);

	imagepng($img, $output);
}

function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick)
{
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

function genImageTextFacescan($image, $output, $text) 
{
	foreach ( $text as $i=>$tx ) 
	{
        if ( $i == 0 ) {
			
			$imagetx = imagecreatefrompng($image);
			$color = imagecolorallocate($imagetx, 255, 195, 30);
			$font = '/assets/fonts/superspace-bold.ttf';
			imagettftext($imagetx, 50, 0, 280, 70, $color, $font, $tx);
			
		} else if ( $i == 1 ) {

			$color = imagecolorallocate($imagetx, 255, 255, 255);
			$font = '/assets/fonts/superspace-bold.ttf';
			imagettftext($imagetx, 14, 0, 290, 110, $color, $font, $tx);

		} else if ( $i == 2 ) {

			$color = imagecolorallocate($imagetx, 255, 255, 255);
			$font = '/assets/fonts/superspace-bold.ttf';
			imagettftext($imagetx, 14, 0, 310, 130, $color, $font, $tx);

		} else if ( $i == 3 ) {

			$color = imagecolorallocate($imagetx, 255, 255, 255);
			$font = '/assets/fonts/superspace-regular.ttf';
			imagettftext($imagetx, 14, 0, 440, 170, $color, $font, $tx);

		} else if ( $i == 4 ) {

			$color = imagecolorallocate($imagetx, 255, 255, 255);
			$font = '/assets/fonts/superspace-bold.ttf';
			imagettftext($imagetx, 60, 0, 400, 230, $color, $font, $tx.'%');

		} else if ( $i == 5 ) {

			$color = imagecolorallocate($imagetx, 255, 255, 255);
			$font = '/assets/fonts/superspace-regular.ttf';
			imagettftext($imagetx, 13, 0, 370, 280, $color, $font, $tx);

		} 

	}

	imagejpeg($imagetx, $output);
}

function genImageShare($data, $output=null)
{
 $images = $data;
    // Allocate new image
    $img = imagecreatetruecolor(600, 315);
    // Make alpha channels work
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $num=0;
    foreach($images as $fn) 
    {
        // Load image
        if($num==0)
        {
            $cur = imagecreatefromstring(file_get_contents($fn));
            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
        }
        else if($num==1)
        {
             $cur = resize_image($fn,160,160,true);
         //imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
            imagecopy($img, $cur, 50, 50, 0, 0, 160, 160);
        }
        else if($num==2)
        {
            $cur = resize_image($fn,80,80,true);
            imagecopy($img, $cur, 340, 55, 0, 0, 80, 80);
        }
        else if($num==3)
        {
            $cur = resize_image($fn,80,80,true);
            imagecopy($img, $cur, 475, 130, 0, 0, 80, 80);
        }
        else if($num==4)
        {
            $cur = imagecreatefromstring(file_get_contents($fn));
            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
        }
        // Copy over image
        

        // Free memory
        imagedestroy($cur);
        $num++;
    }   

	if (!is_null($output)) {
		imagepng($img, $output);
	}
	else {
		header('Content-Type: image/png');  // Comment out this line to see PHP errors
		imagepng($img);
	}
}

function genImageShareAvatar($data, $output=null, $avatar='')
{
      $images = $data;
    // Allocate new image
    $img = imagecreatetruecolor(600, 315);
    // Make alpha channels work
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $num=0;
    $i = count($images);

    if($i>4 || $i==1)
    {
      die('Error'.$i);
    }
    $w=(600/($i-1));
	$h = 315;
    foreach($images as $fn) 
    {
      
      if($i==$num+1)
      {
		if (!empty($avatar)) {
			// replace avatar
			$size = getimagesize($avatar);
			$avatar_w = 92;
			$avatar_h = 92;

			$src_x = 0;
			$src_y = 0;
			if ($size[1] >= $size[0]) {
				$resize_height = ($size[1]/$size[0])*$avatar_w;
				$resize_width = $avatar_w;
				if ($resize_height < $avatar_h) {
					$resize_width = ($size[0]/$size[1])*$avatar_h;
					$resize_height = $avatar_h;
				}
			}
			else {
				$resize_width = ($size[0]/$size[1])*$avatar_h;
				$resize_height = $avatar_h;
				if ($resize_width < $avatar_w) {
					$resize_height = ($size[1]/$size[0])*$avatar_w;
					$resize_width = $avatar_w;
				}
			}
			if ($resize_width > $avatar_w) {
				$src_x = ($resize_width/2) - ($avatar_w/2);
			}
			else if ($resize_height > $avatar_h) {
				$src_y = ($resize_height/2) - ($avatar_h/2);
			}

			$myImage = imagecreatefromstring(file_get_contents($avatar));
			$tmp = imagecreatetruecolor($resize_width, $resize_height);
			imagecopyresampled($tmp, $myImage, 0, 0, 0, 0, $resize_width, $resize_height, imagesx($myImage), imagesy($myImage));
			$cur = imagecreatetruecolor($avatar_w, $avatar_h);
			imagecopy($cur, $tmp, 0, 0, $src_x, $src_y, $avatar_w, $avatar_h);
			imagedestroy($tmp);

			$newwidth = 92;
			$newheight = 92;
			// crop circle
			$mask = imagecreatetruecolor($newwidth, $newheight);
			$transparent = imagecolorallocate($mask, 255, 255, 255);
			imagecolortransparent($mask,$transparent);
			imagefilledellipse($mask, $newwidth/2, $newheight/2, $newwidth, $newheight, $transparent);
			$white = imagecolorallocate($mask, 0, 0, 0);
			imagecopymerge($cur, $mask, 0, 0, 0, 0, $newwidth, $newheight, 100);
			imagecolortransparent($cur,$white);
			imagefill($cur, 0, 0, $white);
			// crop circle

            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, 47, 190, 0, 0, $avatar_w, $avatar_h);
			// replace avatar
		}

		$cur = imagecreatefromstring(file_get_contents($fn));
		imagealphablending($cur, true);
		imagesavealpha($cur, true);
		imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
      }
      else
      {
			$size = getimagesize($fn);

			$src_x = 0;
			$src_y = 0;
			if ($size[1] >= $size[0]) {
				$resize_height = ($size[1]/$size[0])*$w;
				$resize_width = $w;
				if ($resize_height < $h) {
					$resize_width = ($size[0]/$size[1])*$h;
					$resize_height = $h;
				}
			}
			else {
				$resize_width = ($size[0]/$size[1])*$h;
				$resize_height = $h;
				if ($resize_width < $w) {
					$resize_height = ($size[1]/$size[0])*$w;
					$resize_width = $w;
				}
			}
			if ($resize_width > $w) {
				$src_x = ($resize_width/2) - ($w/2);
			}
			else if ($resize_height > $h) {
				$src_y = ($resize_height/2) - ($h/2);
			}

			$myImage = imagecreatefromstring(file_get_contents($fn));

			$tmp = imagecreatetruecolor($resize_width, $resize_height);
			imagecopyresampled($tmp, $myImage, 0, 0, 0, 0, $resize_width, $resize_height, imagesx($myImage), imagesy($myImage));
			$cur = imagecreatetruecolor($w, $h);
			imagecopy($cur, $tmp, 0, 0, $src_x, $src_y, $w, $h);
			imagedestroy($tmp);

            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, $w*$num, 0, 0, 0, $w, $h);
      }

/*
      // The text to draw
      // Replace path by your own font path
      $font = 'assets/font/TH Baijam.ttf';
      $white = imagecolorallocate($img, 255, 255, 255);
      // Add some shadow to the text
      imagettftext($img, 28, 0, 20, 285, $white, $font, $text);
*/

      // Free memory
      imagedestroy($cur);
      $num++;

    }

      if (!is_null($output)) {
            imagepng($img, $output);
      }
      else {
            header('Content-Type: image/png');  // Comment out this line to see PHP errors
            imagepng($img);
      }
}

function genImageShareNew($data, $output=null)
{
      $images = $data;
    // Allocate new image
    $img = imagecreatetruecolor(600, 315);
    // Make alpha channels work
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $num=0;
    $i = count($images);

    if($i>4 || $i==1)
    {
      die('Error'.$i);
    }
    $w=(600/($i-1));
	$h = 245;
    foreach($images as $fn) 
    {
      
      if($i==$num+1)
      {
            $cur = imagecreatefromstring(file_get_contents($fn));
            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, 0, 0, 0, 0, 600, 315);
      }
      else
      {
			$size = getimagesize($fn);

			$src_x = 0;
			$src_y = 0;
			if ($size[1] >= $size[0]) {
				$resize_height = ($size[1]/$size[0])*$w;
				$resize_width = $w;
				if ($resize_height < $h) {
					$resize_width = ($size[0]/$size[1])*$h;
					$resize_height = $h;
				}
			}
			else {
				$resize_width = ($size[0]/$size[1])*$h;
				$resize_height = $h;
				if ($resize_width < $w) {
					$resize_height = ($size[1]/$size[0])*$w;
					$resize_width = $w;
				}
			}
			if ($resize_width > $w) {
				$src_x = ($resize_width/2) - ($w/2);
			}
			else if ($resize_height > $h) {
				$src_y = ($resize_height/2) - ($h/2);
			}

			$myImage = imagecreatefromstring(file_get_contents($fn));

			$tmp = imagecreatetruecolor($resize_width, $resize_height);
			imagecopyresampled($tmp, $myImage, 0, 0, 0, 0, $resize_width, $resize_height, imagesx($myImage), imagesy($myImage));
			$cur = imagecreatetruecolor($w, $h);
			imagecopy($cur, $tmp, 0, 0, $src_x, $src_y, $w, $h);
			imagedestroy($tmp);


            imagealphablending($cur, true);
            imagesavealpha($cur, true);
            imagecopy($img, $cur, $w*$num, 0, 0, 0, $w, $h);
      }

/*
      // The text to draw
      // Replace path by your own font path
      $font = 'assets/font/TH Baijam.ttf';
      $white = imagecolorallocate($img, 255, 255, 255);
      // Add some shadow to the text
      imagettftext($img, 28, 0, 20, 285, $white, $font, $text);
*/

      // Free memory
      imagedestroy($cur);
      $num++;

    }

      if (!is_null($output)) {
            imagepng($img, $output);
      }
      else {
            header('Content-Type: image/png');  // Comment out this line to see PHP errors
            imagepng($img);
      }
}

function resize_image($file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}
?>