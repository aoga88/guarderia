<?php $config = include '../app/config.php'; ?>
<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $config['appName']?></title>

    <?php
      $commit = exec('git log --format="%H" -n 1');
    ?>

    <script type="text/javascript">
      //var ioServer = 'http://guarderia.os-cloud.net:3000';
      //var ioServer = 'http://guarderia.local:3000';
      var notifications = [];
    </script>
    <link rel="stylesheet" type="text/css" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/styles-less.css">
    <link rel="stylesheet" type="text/css" href="/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/css/animate.css">
    <link rel="stylesheet" type="text/css" href="/assets/effects/menu-effects.css">
    <link rel="stylesheet" type="text/css" href="/_demo/demo.css">

    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Lato:400,300,700,700italic,900,100' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="/css/custom-theme/jquery-ui-1.9.2.custom.css?<?php echo $commit?>" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-notify.css" />
    <link rel="stylesheet" type="text/css" href="/css/alert-bangtidy.css" />
    <script type="text/javascript" src="/js/less-1.7.0.min.js"></script>

    <!-- Javascript -->
    <script src="/assets/jquery/jquery.min.js"></script>
    <script src="/assets/sidebar/min-height.js"></script>

    <!-- Custom Scroll Bar -->
    <script src="/assets/nicescroll/jquery.nicescroll.min.js"></script>

    <!-- Bootstrap -->
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>

    <!-- Demo -->
    <script src="/_demo/all-pages.js"></script>

    <!-- <script type="text/javascript" src="http://guarderia.os-cloud.net:3000/socket.io/socket.io.js"></script> -->
    <script data-main="/js/main.js?<?php echo $commit?>" src="/js/require.js"></script>
  
  </head>
  <body
    <?php if(empty($_SESSION)): ?>
      class="login-page"
    <?php endif?>>

    <section class="content">
      <?php if(!empty($_SESSION)): ?>
        <ng-include src="'/views/common/sidebar.html'"></ng-include>
        <div class="content-liquid-full">
          <div class="container">
            <ng-include src="'/views/common/header.html'"></ng-include>
        
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php endif?>
                <ng-view></ng-view>
            <?php if(!empty($_SESSION)): ?>
              </div>
            </div>
        
          </div>
        </div>
      <?php 
        endif;
      ?>
    </section>

    <div class='notifications bottom-right'></div>
  </body>
</html>