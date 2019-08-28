<?php
$pace_str = '';
$kmh_str = '';
$error = FALSE;
$msg = '';
$who = '';
$table_counter = 0;

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  $pace = htmlspecialchars($_POST["pace"]);
  $kmh = htmlspecialchars($_POST["kmh"]);
  $who = htmlspecialchars($_POST["who"]);
  $pace_str = strval($pace);
  $kmh_str = strval($kmh);
  if ($who == 'kmh') {
    if (is_numeric($kmh)) {
      if (intval($kmh) <= 6) {
        $error = TRUE;
        $msg .= "爬的太慢，不管算<br>\n";
      } elseif (intval($kmh) > 25) {
        $error = TRUE;
        $msg .= "飞得太快，算不过来<br>\n";
      }
    } else {
      $error = TRUE;
      $msg .= "km/h要填数字，要不没法算<br>\n";
    }
  } elseif ($who == 'pace') { //
    if (is_numeric($pace)) {
      if (intval($pace) <= 200 || intval($pace) >= 999) {
        $error = TRUE;
        $msg .= "别逗我，配速不合常识~<br>\n";
      }

    } else {
      $error = TRUE;
      $msg .= "配速你填" . $pace . "，打算让我怎么算？<br>\n";
    }
  }
  work();
}

function convertToHoursMins($time, $format = '%02d:%02d:%02d')
{
  if ($time < 1) {
    return;
  }
  $hours = floor($time / 3600);
  $minutes = floor(($time - $hours * 3600) / 60);
  $seconds = (($time - $hours * 3600 - $minutes * 60) % 3600);
  return sprintf($format, $hours, $minutes, $seconds);
}

function work()
{
  global $pace, $kmh, $pace_str, $kmh_str, $who, $error;
  if ($error == TRUE) {
    return;
  }

  if ($who == 'pace') {
    $sec = (int)substr($pace, -2);
    $min = (int)substr($pace, 0, strlen($pace) - 2);
    $kmh_str = strval(round(1 / ($min * 60 + $sec) * 3600, 2));


  } elseif ($who == 'kmh') {

    $pace = 60 / $kmh;
    $pace_int = intval($pace);
    $pace_decimals = round(($pace - $pace_int) * 60, 0);
    $pace_str = strval($pace_int);


    if ($pace_decimals == 0) {
      $pace_str .= '00';
    } elseif ($pace_decimals < 10) { //小于10，补0。例如 602
      $pace_str .= '0';
      $pace_str .= strval($pace_decimals);
    } else {
      $pace_str .= strval($pace_decimals);
    }


  }
}

?>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta charset="utf-8">
  <title>速度转换工具 | 跑步配速与跑步机速度互换</title>
  <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css"
        integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous">
  <!--[if lte IE 8]>
  <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/grids-responsive-old-ie-min.css">
  <![endif]-->
  <!--[if gt IE 8]><!-->
  <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/grids-responsive-min.css">
  <!--<![endif]-->
  <style type="text/css">
    h1, h2 {
      font-family: "Nexa", "微软雅黑", "Open Sans", "Noto Sans S Chinese", sans-serif;
      font-size: 20px;
    }

    .error {
      color: #f00;
    }

    .content {
      text-align: center;
      vertical-align: middle;
      margin: 0 10px;
    }

    .content-inner {
      margin: 0 auto;
    }

    .table {
      text-align: center;
      vertical-align: middle;
    }

    .table table {
      min-width: 300px;

      margin: 0 auto;
    }

    .who-active {
      /*border-color: #0078e7;*/
      background-color: #d8c74a;
      color: #fff;
      border-color: #0e0e0e;
    }

    input.pace, input.kmh, .pure-button {
      width: 150px;
    }

    table.pure-table tr > th {
      width: 50%;
    }


    @media (max-width: 767px) {
      .pure-form input[type=number] {
        display: inline-block;
      }

      .pure-form-aligned .pure-control-group label {
        text-align: center;
        display: inline-block;
      }
    }

  </style>
  <script src="jquery-3.4.1.min.js"></script>
  <script>
      $(document).ready(function () {
          $('input[name="kmh"]').change(function () {
              $('input[name="who"]').val('kmh');
              $('input[name="pace"]').removeClass('who-active');
              $('input[name="kmh"]').addClass('who-active');
          });
          $('input[name="pace"]').change(function () {
              $('input[name="who"]').val('pace');
              $('input[name="kmh"]').removeClass('who-active');
              $('input[name="pace"]').addClass('who-active');
              //$('div.result').append('asdfasdf');
          });

          $('input[name="kmh"]').click(function () {
              $('input[name="who"]').val('kmh');
              $('input[name="pace"]').removeClass('who-active');
              $('input[name="kmh"]').addClass('who-active');
          });
          $('input[name="pace"]').click(function () {
              $('input[name="who"]').val('pace');
              $('input[name="kmh"]').removeClass('who-active');
              $('input[name="pace"]').addClass('who-active');
              //$('div.result').append('asdfasdf');
          });


          $('input[type="reset"]').click(function (event) {
              //$(".result").hide();
              $('input[name="kmh"]').attr('value', "");
              $('input[name="pace"]').attr('value', "");
              $('input[name="who"]').attr('value', "");

          });

          /*          if($('input[name="who"]').val()=='kmh'){
                        $('input[name="pace"]').removeClass('who-active');
                        $('input[name="kmh"]').addClass('who-active');
                    }else if($('input[name="who"]').val()=='pace'){
                        $('input[name="kmh"]').removeClass('who-active');
                        $('input[name="pace"]').addClass('who-active');
                    }*/

      });
      //console.log($('input[type=="reset"]'));
  </script>
</head>
<body>
<div class="content">
  <div class="content-inner">
    <h1>跑步配速 - 跑步机速度 换算</h1>
    <?php
    if ($msg <> '') {
      echo '<div class="error">';
      echo $msg;
      echo '</div>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
          class="pure-form pure-form-aligned">
      <fieldset>
        <div class="pure-control-group">
          <label>配速</label>
          <input type="number" size="8" class="pace" name="pace" placeholder="Pace"
                 value="<?php echo $pace_str; ?>">
          <span class="pure-form-message-inline">格式: 630, 500 ...</span></div>
        <div class="pure-control-group">
          <label>km/h</label>
          <input type="number" size="8" class="kmh" name="kmh" placeholder="km/h"
                 value="<?php echo $kmh_str; ?>">
          <span class="pure-form-message-inline">格式: 12.3, 13 ...</span></div>
        <div class="pure-control-group">
          <input type="hidden" name="who">
          <input type="submit" class="pure-button pure-button-primary">
        </div>
        <div class="pure-control-group">
          <input type="reset" class="pure-button"></div>
      </fieldset>
    </form>
    <?php
    if (!$error && $_SERVER["REQUEST_METHOD"] == 'POST') {

      $distance = array(
        '0.1' => '100m',
        '0.2' => '200m',
        '0.4' => '400m',
        '0.8' => '800m',
        '1' => '1KM',
        '5' => '5KM',
        '10' => '10KM',
        '21.0975' => '半马',
        '42.195' => '全马',
      );
      $time = array(
        1,
        2,
        5,
        10,
        15,
        20,
        30,
        60,
        120,
      );

      echo '<h2>在此速度下</h2>';
      echo "<div class=\"pure-g\"><div class='table pure-u-1'>";
      echo "<table class=\"pure-table\">
    <thead>
        <tr>
            <th>距离</th>
            <th>时:分:秒</th>
        </tr>
    </thead><tbody>
      ";


      foreach ($distance as $key => $value) {
        $table_counter += 1;
        if ($table_counter % 2) {
          //odd
          echo "<tr class=\"pure-table-odd\">";
        } else {
          //even
          echo "<tr>";
        }
        echo '<td>' . $value . '</td>';
        $time_ori = (float)(1 / floatval($kmh_str) * (floatval($key)) * 3600);
        echo '<td>' . convertToHoursMins($time_ori) . '</td></tr>';
      }
      echo "</tbody></table>";


      echo "</div>";


      echo "<div class='table pure-u-1'>";
      echo "<table class=\"pure-table\">
    <thead>
        <tr>
            <th>时间</th>
            <th>距离</th>
        </tr>
    </thead><tbody>
      ";
      $table_counter = 0;
      foreach ($time as $key => $value) {
        $table_counter += 1;
        if ($table_counter % 2) {
          //odd
          echo "<tr class=\"pure-table-odd\">";
        } else {
          //even
          echo "<tr>";
        }
        echo '<td>' . $value . '分</td>';
        $time_ori = round($value / 60 * floatval($kmh_str) * 1000, 0);
        echo '<td>' . $time_ori . '米</td></tr>';
      }
      echo "</tbody></table>";


      echo "</div>";

      echo "<div class='table pure-u-1-2'>";

      echo "</div></div>";


      echo "</div>";

    } else {

    }
    echo '网上搜索到的配速转换器都觉得不好用。<br>
      自己写了一个。欢迎跑友测试。<br><br>
      <a href="https://github.com/esojourn/calc/issues">bug反馈</a> - 
      <a href="https://github.com/esojourn/calc">源代码</a> - 
      <a href="https://dingxuan.info/calc">跑步配速转换</a><br>
      <a href="https://dingxuan.info">dingxuan.info</a></div>';
    ?>
    <div class="table">


      <?php
      //echo $msg; //21.0975, 42.195
      /*      echo 'Pace value: ' . $pace_str . '<br>';
            echo 'KMH value: ' . $kmh_str . '<br>';
            echo 'WHO: ' . $who . '<br>';*/

      ?>
    </div>
  </div>
</div>
</body>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-248898-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-248898-1');
</script>
</html>
