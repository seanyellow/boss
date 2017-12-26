<?php
/*
  執行此php即會執行'db.sql'腳本中所有指令，
  等於把db.sql全選貼入phpmyadmin的console中。
*/
$page_name = '執行SQL腳本'; // 本頁面名稱
require_once 'config.php';
require_once 'connection.php';

if($_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']== '127.0.0.1'){
  // 若當前主機為localhost
  $file = file_get_contents('db.sql'); // 取得sql腳本檔
  $conn = new mysqli(db_host, db_username, db_password); // 建立mysql連接
}else{
  $file = file_get_contents('db_server.sql');
  $conn = new mysqli(db_host, db_username, db_password, db_name);
}

// 將讀入的腳本檔字串打散為Array，以';'分割，所以連註解的尾也要打';'
$arr = explode(';', $file);

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <?php include('style.php') ?>
  <title><?php echo  $page_name. ' - ' .title_name ?></title>
</head>

<body>
  <?php include('nav.php') ?>
  <div class="container mt-3">
    <div class="alert alert-info text-center"><i class="material-icons">storage</i> 執行了以下查詢 <a data-toggle="collapse" href="#zhiling" class="alert-link">顯示</a></div>
    <div class="" id="zhiling">
    <?php
      // 逐一執行mysql查詢
      $errorNum=0;
      foreach ($arr as $line) {
        ob_flush();
        flush();
        if ($conn->query($line.';') == TRUE) {
          // 若正確
          if (!($rst = strpos($line, '--'))) { // 只印出非註解指令
            echo '<div class="alert alert-secondary"><pre><code>'. $line . ';</code></pre></div>';
          }
        } else {
          // 若錯誤 印出錯誤資訊
          $errorNum++;
          echo'<div class="alert alert-danger" id="error-query"><i class="material-icons">warning</i> 錯誤 :<br><pre><code>'
              . $line . ';<br><strong></code></pre>'. $conn->error .'</strong></div>';

        }
      }
    ?>
    </div>
    <?=($errorNum>0)
     ?'<div class="alert alert-danger text-center" id="final-msg"><i class="material-icons">report_problem</i> 查詢時發生了共 '. $errorNum .' 筆錯誤！</div>'
     :'<div class="alert alert-success text-center" id="final-msg"><i class="material-icons">favorite</i> 已成功查詢所有指令。</div>';
     ?>
  </div>
  <?php include('footer.php') ?>
</body>
<?php include('js.php') ?>
<script type="text/javascript">

  // 頁面載入完成後，自動滾到最底部
  $(document).ready(function(){
    var h = $(document).height()-$(window).height();
    $(document).scrollTop(h);

    // 若查詢成功則將指令折疊。
    if($('#final-msg').hasClass('alert-success')){
      $('#zhiling').addClass('collapse');
    }
  });


</script>

</html>
