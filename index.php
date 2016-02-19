<?php

// Enter your API Key
define("api_key", "********************");

$channel = $_GET["ch"];

// to set error flag
$error = "";

if($channel != ""){

  $char_cnt = mb_strlen($channel, "UTF-8");

  if(substr($channel, 0 ,2) == "UC" and $char_cnt == 24){

    // search by channel ID
    $apiurl = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,statistics,snippet&key=" . api_key . "&id=" . $channel;

  }else{

    // search by user name
    $apiurl = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings,statistics,snippet&key=" . api_key . "&forUsername=" . $channel;

  }

  $video = file_get_contents($apiurl);

  $video = json_decode($video, true); 

  // 件数
  $results = $video['pageInfo']['totalResults'];

  if($results > 0){

    foreach ($video['items'] as $data ){
      $id = $data['id'];
      $title = $data['snippet']['title'];
      $image = $data['snippet']['thumbnails']['default']['url'];
      $subscriber = $data['statistics']['subscriberCount'];
      $subscriber2 = number_format($subscriber);
      $viewer = $data['statistics']['viewCount'];
      $viewer = number_format($viewer);
      $upload = $data['statistics']['videoCount'];
      $upload = number_format($upload);
    }

    // Get RSS feed
    $feed = curl_get_contents("https://www.youtube.com/feeds/videos.xml?channel_id=" . $id);

    $xml = new SimpleXmlElement($feed);

    $count = count($xml->entry);
    for ($i=0; $i < 10; $i++) { 
      $url = $xml->entry[$i]->link->attributes();
      $videourl = explode("&", $url['href']);
      $videoid = str_replace("http://www.youtube.com/watch?v=", "", $videourl[0]);
      $videotitle = $xml->entry[$i]->title;
      $carousel .= '<div><a href="' . $url['href'] . '" data-toggle="tooltip" data-original-title="' . $videotitle . '" data-container="body"><img src="http://img.youtube.com/vi/' . $videoid . '/mqdefault.jpg"></a></div>';
    }

  // in case of error
  }else{

    $error = <<< EOM
       <div class="bs-component">
          <div class="alert alert-dismissible alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
          </div>
        </div>
EOM;

  }

}

function curl_get_contents($url) {
  // Initiate the curl session
  $ch = curl_init();
  // Set the URL
  curl_setopt($ch, CURLOPT_URL, $url);
  // Removes the headers from the output
  curl_setopt($ch, CURLOPT_HEADER, 0);
  // Return the output instead of displaying it directly
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //set timeout
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  // Execute the curl session
  $output = curl_exec($ch);
  // Close the curl session
  curl_close($ch);
  // Return the output as a variable
  return $output;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Tool to count YouTube subscribers.">
  <meta name="keywords" content="youtube,tool,subscriber">
  <title>YouTubeのチャンネル登録者数を調べます - YouTubeツール</title>
  <link rel="shortcut icon" href="./favicon.ico">
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/odometer/0.4.7/themes/odometer-theme-minimal.css">
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.css"/>
  <style type="text/css">
  body { padding-top: 80px; }
  @media ( min-width: 768px ) {
    #banner {
      min-height: 300px;
      border-bottom: none;
    }
    .bs-docs-section {
      margin-top: 8em;
    }
    .bs-component {
      position: relative;
    }
    .bs-component .modal {
      position: relative;
      top: auto;
      right: auto;
      left: auto;
      bottom: auto;
      z-index: 1;
      display: block;
    }
    .bs-component .modal-dialog {
      width: 90%;
    }
    .bs-component .popover {
      position: relative;
      display: inline-block;
      width: 220px;
      margin: 20px;
    }
    .nav-tabs {
      margin-bottom: 15px;
    }
    .stylish-input-group .input-group-addon{
      background: white !important; 
    }
    .stylish-input-group .form-control{
      border-right:0; 
      box-shadow:0 0 0; 
      border-color:#ccc;
    }
    .stylish-input-group button{
      border:0;
      background:transparent;
    }
    .img-responsive-overwrite{
      margin: 50px auto;
    }
    .odometer {
      font-size: 100px;
    }
  }
  </style>

  <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body>

<header>
  <div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="./" class="navbar-brand">YouTubeツール <span style="color:#f48260;"><i class="glyphicon glyphicon-heart"></i></span></a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
    </div>
  </div>
</header>

<div class="container">

  <div class="page-header" id="banner">

    <div class="row">

      <div class="col-sm-8 col-sm-offset-2 text-center">

        <form action="./" method="get" class="form-horizontal" id="myForm">

        <div class="input-group stylish-input-group">
          <input type="text" class="form-control input-lg"  placeholder="チャンネル名(またはID)を入力してください。" name="ch" value="<?php echo $channel; ?>">
          <span class="input-group-addon">
            <button type="submit">
              <span class="glyphicon glyphicon-search"></span>
            </button>
          </span>
        </div>
        <span class="help-block pull-left">(例) <a href="./?ch=HikakinTV">HikakinTV</a>、<a href="./?ch=TheMaxMurai">TheMaxMurai</a>、<a href="./?ch=kazuyahkd">kazuyahkd</a></span>
        </form>

<?php
if($channel != "" and $error == ""){

echo <<< EOM
        <img src="{$image}" class="img-responsive img-responsive-overwrite img-circle">
        <h1>{$title}</h1>
        <div id="odometer" class="odometer">0</div>

        <hr>

      </div>

    </div>

    <div class="row">

      <div class="col-sm-8 col-sm-offset-2 text-center">

        <div class="col-sm-4 text-center">
        <h2><i class="fa fa-user" data-toggle="tooltip" data-original-title="登録者数"></i><br>
        {$subscriber2}</h2>
        </div>

        <div class="col-sm-4 text-center">
        <h2><i class="fa fa-eye" data-toggle="tooltip" data-original-title="再生回数"></i><br>
        {$viewer}</h2>
        </div>

        <div class="col-sm-4 text-center">
        <h2><i class="fa fa-video-camera" data-toggle="tooltip" data-original-title="動画数"></i><br>
        {$upload}</h2>
        </div>

        <hr>

      </div>

    </div>

    <div class="row">


        <div class="your-class">
          {$carousel}
        </div>

EOM;

}elseif($error != ""){

echo $error;

echo <<< EOM

      </div>

EOM;

}else{

echo <<< EOM

      </div>

EOM;

}
?>

    </div>

  </div>

  <div class="row">

    <div class="col-sm-8 col-sm-offset-2 text-center">

    <hr>

    </div>

  </div>



  <footer class="footer text-center">

    <p>
    Copyright (C) 2016 <a href="http://tsukuba42195.top/">Akira Mukai</a><br>
    </p>

  </footer>

</div>


<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/odometer/0.4.7/odometer.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery.slick/1.5.9/slick.min.js"></script>

<script type="text/javascript">
  $('[data-toggle="tooltip"]').tooltip();

  $('.your-class').slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 3,
    slidesToScroll: 3,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

<?php
if($channel != "" and $error == ""){
echo <<< EOM
  setTimeout(function(){
      odometer.innerHTML = {$subscriber};
  }, 1000);
EOM;
}
?>

</script>

</body>
</html>
