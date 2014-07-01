<?php $config = include '../app/config.php'; ?>
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
      var ioServer = 'http://guarderia.os-cloud.net:3000';
      var notifications = [];
    </script>
    <link rel="stylesheet/less" type="text/css" href="/css/app.less?<?php echo $commit?>" />
    <link rel="stylesheet" type="text/css" href="/css/custom-theme/jquery-ui-1.9.2.custom.css?<?php echo $commit?>" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-notify.css" />
    <link rel="stylesheet" type="text/css" href="/css/alert-bangtidy.css" />
    <script type="text/javascript" src="/js/less-1.7.0.min.js"></script>
    <script type="text/javascript" src="http://guarderia.os-cloud.net:3000/socket.io/socket.io.js"></script>
    <script data-main="/js/main.js?<?php echo $commit?>" src="/js/require.js"></script>
  
  </head>
  <body>
    <ng-view></ng-view>
    <div class='notifications bottom-right'></div>
  </body>
</html>