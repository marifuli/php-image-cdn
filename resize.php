<?php 

function dynamic_image($url, $width = false, $height = -1, $quality = 80, $compression = 75)
{
    $parsed = parse_url($url);
    $path = $parsed['path'];
    $file_name = basename($path);
    $only_path = str_replace($file_name, '', $path);
    $public_path = 'cache/' . str_replace('/', '_', $path);
    try {
        // if(!file_exists($public_path) || isset($_GET['refresh']))
        // {
            file_put_contents($public_path, file_get_contents($url));
        // }
        // var_dump(file_exists($public_path));die;

        if(file_exists($public_path))
        {
            $content_type = explode('/', mime_content_type($public_path));
            $type = $content_type[1];
            
            if(
                $content_type[0] == 'image' && 
                in_array($type, ['png', 'jpeg','jpg', 'gif', 'webp'])
            )
            {
                $cache_image_name = 'resized.cached_' ;
                if($width)
                {
                    $cache_image_name .= '_w_'. $width. '_'; 
                }
                if($height !== false)
                {
                    $cache_image_name .= '_h_'. $height. '_'; 
                }
                $cache_image_name .= '_quality_' . $quality . '_compression_' . $compression . $file_name;

                // $cache_image_path = $only_path . $cache_image_name;
                $cache_image_path = 'cache/' . $cache_image_name;
                // var_dump($only_path);
                if(!file_exists($cache_image_path))
                {
                    $image = false;
                    //- create and save the image
                    if($type == 'png')
                    {
                        $image = imagecreatefrompng($public_path);
                        imagepng($image, $cache_image_path);
                    }
                    if($type == 'jpg' || $type == 'jpeg')
                    {
                        $image = imagecreatefromjpeg($public_path);
                        imagejpeg($image, $cache_image_path, $quality);
                    }
                    if($type == 'gif')
                    {
                        $image = imagecreatefromgif($public_path);
                        imagegif($image, $cache_image_path);
                    }
                    if($type == 'webp')
                    {
                        $image = imagecreatefromwebp($public_path);
                        imagewebp($image, $cache_image_path);
                    }
                    // dd(imagesx($image) > $width);
                    if($image && $width !== false && imagesx($image) > $width)
                    {
                        $image = imagescale($image, $width, $height);
                        
                        //- save the image again
                        if($type == 'png')
                        {
                            imagepng($image, $cache_image_path);
                        }
                        if($type == 'jpg' || $type == 'jpeg')
                        {
                            imagejpeg($image, $cache_image_path, $quality);
                        }
                        if($type == 'gif')
                        {
                            imagegif($image, $cache_image_path);
                        }
                        if($type == 'webp')
                        {
                            imagewebp($image, $cache_image_path);
                        }
                        if(
                            in_array($type, ['png', 'jpg', 'jpeg', 'gif', 'webp'])
                        )
                        {
                            header('Content-type: image/' . $type);
                        }
                    }
                    
                    return file_get_contents($cache_image_path);
                }
                // var_dump(
                //     $cache_image_name
                //     // $file_name, $cache_image_name, $url)
                // );die;
                return header( 'Location: cache/' . $cache_image_name );
            }
        }
    } 
    catch (\Throwable $th) 
    {
        //throw $th;
    }
    
    return header( 'Location: ' . $url ) ;
    //return 'file not found';
}