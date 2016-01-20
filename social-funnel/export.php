<?php ob_start();
if(empty($_POST['filename']) || empty($_POST['content'])){
    exit;
}
$filename = stripslashes($_POST['filename']);
$json = json_decode($_POST['content'], true);
$popup_details = json_decode($json["popup_details"], true);
$social_info = array();
$social_info['content'] = $_POST['content'];

  if(isset($popup_details["p1s1image"]) AND $popup_details["p1s1image"] != "")
    {
         $img = $popup_details["p1s1image"];
         $img_data = file_get_contents($img);
         $base64 = base64_encode($img_data);
         $ash = explode('.', $img);
         $ext = end($ash);
         $social_info['images']['data'] = array('ext' => $ext, 'data' => $base64);
         $social_info["images"]['old_img'] = $img;

    }
    elseif(isset($popup_details["p4s1image"]) AND $popup_details["p1s1image"] != "")
    {
         $img = $popup_details["p4s1image"];
         $img_data = file_get_contents($img);
         $base64 = base64_encode($img_data);
         $ash = explode('.', $img);
         $ext = end($ash);
         $social_info['images']['data'] = array('ext' => $ext, 'data' => $base64);
         $social_info["images"]['old_img'] = $img;
    }

    $gift_url = $json["gift_url"];
     if(!empty($gift_url))
     {   
         //$gift_url = stripslashes($gift_url);   
         $gift_data = file_get_contents($gift_url);
         $gift_base64 = base64_encode($gift_data);
         $gft = explode('.', $gift_url);
         $gft_ext = end($gft);
         $social_info['gift_images']["data"] = array('ext' => $gft_ext, 'data' => $gift_base64);
         $social_info['gift_images']['old_img'] = $gift_url;
     }

     $featur_img = $json["feature_image"];
     $featur_img = str_replace('[', '', str_replace(']', "", $featur_img));
     $featur_img = trim($featur_img,'"');
     if(!empty($featur_img))
     {  
        $fet  = explode('","', $featur_img);
        foreach($fet as $fetch)
        {
            $fetch = stripslashes($fetch);
            $f_data = file_get_contents($fetch);
            $f_base64 = base64_encode($f_data);
            $f = explode('.', $fetch);
            $f_ext = end($f);
            $social_info['feature_images']["data"][] = array('ext' => $f_ext, 'data' => $f_base64);
            $social_info['feature_images']['old_img'][] = $fetch;

        }
     }

header("Cache-Control: ");
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="'.$filename.'"');
 
echo json_encode($social_info);
exit();

  
?>