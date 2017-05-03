<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>网易云音乐外链获取工具</title>
</head>
<body>
<?php
function netease_http($url)
{
    $refer = "http://music.163.com/";
    $header[] = "Cookie: " . "appver=1.5.0.75771;";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    $cexecute = curl_exec($ch);
    curl_close($ch);

    if ($cexecute) {
        $result = json_decode($cexecute, true);
        return $result;
    }else{
        return false;
    }
}

function netease_song($music_id)
{

    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
    $response = netease_http($url);

    if( $response["code"]==200 && $response["songs"] ){
        //print_r($response["songs"]);
        //处理音乐信息
        $mp3_url = $response["songs"][0]["mp3Url"];
        $mp3_url = str_replace("http://m", "http://p", $mp3_url);
        $music_name = $response["songs"][0]["name"];
        $mp3_cover = $response["songs"][0]["album"]["picUrl"];
        $song_duration = $response["songs"][0]["duration"];
        $artists = array();

        foreach ($response["songs"][0]["artists"] as $artist) {
            $artists[] = $artist["name"];
        }

        $artists = implode(",", $artists);

        $result = array(
            "song_id" => $music_id,
            "song_title" => $music_name,
            "song_author" => $artists,
            "song_src" => $mp3_url,
            "song_cover" => $mp3_cover,
            "song_duration" => $song_duration
        );

        return $result;
    }

    return false;
}
?>
<?php
$id = $_REQUEST['id'];
$rs = netease_song($id);
if(!$rs)
{
    echo 'NO FOUND';
    return;
}
while(list($key,$value)=each($rs)){
    echo "$key:$value<br />";
}
?>
</body>
</html>