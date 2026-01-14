<?php
require_once 'lang/i18n.php';

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
        $msg .= t('error_too_slow') . "<br>\n";
      } elseif (intval($kmh) > 25) {
        $error = TRUE;
        $msg .= t('error_too_fast') . "<br>\n";
      }
    } else {
      $error = TRUE;
      $msg .= t('error_kmh_not_number') . "<br>\n";
    }
  } elseif ($who == 'pace') { //
    if (is_numeric($pace)) {
      if (intval($pace) <= 200 || intval($pace) >= 999) {
        $error = TRUE;
        $msg .= t('error_pace_invalid') . "<br>\n";
      }

    } else {
      $error = TRUE;
      $msg .= sprintf(t('error_pace_not_number'), $pace) . "<br>\n";
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
    $kmh_str = strval(round(1 / ($min * 60 + $sec) * 3600, 1));


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
  <title><?php echo t('page_title'); ?></title>
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

    .lang-selector {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1000;
    }

    .lang-selector select {
      padding: 5px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      cursor: pointer;
      background-color: #fff;
      font-size: 14px;
    }

    @media (max-width: 767px) {
      .lang-selector {
        position: static;
        text-align: center;
        margin: 10px 0;
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
  <div class="lang-selector">
    <select id="langSelect" onchange="location.href='?lang='+this.value;">
      <option value="zh" <?php echo $currentLang == 'zh' ? 'selected' : ''; ?>>简体中文</option>
      <option value="zh-TW" <?php echo $currentLang == 'zh-TW' ? 'selected' : ''; ?>>繁體中文</option>
      <option value="en" <?php echo $currentLang == 'en' ? 'selected' : ''; ?>>English</option>
      <option value="de" <?php echo $currentLang == 'de' ? 'selected' : ''; ?>>Deutsch</option>
    </select>
  </div>
  <div class="content-inner">
    <h1><?php echo t('main_heading'); ?></h1>
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
          <label><?php echo t('label_pace'); ?></label>
          <input type="number" size="8" class="pace" name="pace" placeholder="<?php echo t('placeholder_pace'); ?>"
                 value="<?php echo $pace_str; ?>">
          <span class="pure-form-message-inline"><?php echo t('hint_pace'); ?></span></div>
        <div class="pure-control-group">
          <label><?php echo t('label_kmh'); ?></label>
          <input type="number" size="8" class="kmh" name="kmh" placeholder="<?php echo t('placeholder_kmh'); ?>"
                 value="<?php echo $kmh_str; ?>" step=".1">
          <span class="pure-form-message-inline"><?php echo t('hint_kmh'); ?></span></div>
        <div class="pure-control-group">
          <input type="hidden" name="who">
          <input type="submit" class="pure-button pure-button-primary" value="<?php echo t('button_submit'); ?>">
        </div>
        <div class="pure-control-group">
          <input type="reset" class="pure-button" value="<?php echo t('button_reset'); ?>"></div>
      </fieldset>
    </form>
    <?php
    if (!$error && $_SERVER["REQUEST_METHOD"] == 'POST') {

      $distance = array(
        '0.1' => t('distance_100m'),
        '0.2' => t('distance_200m'),
        '0.4' => t('distance_400m'),
        '0.8' => t('distance_800m'),
        '1' => t('distance_1km'),
        '5' => t('distance_5km'),
        '10' => t('distance_10km'),
        '21.0975' => t('distance_half_marathon'),
        '42.195' => t('distance_full_marathon'),
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

      echo '<h2>' . t('table_heading') . '</h2>';
      echo "<div class=\"pure-g\"><div class='table pure-u-1'>";
      echo "<table class=\"pure-table\">
    <thead>
        <tr>
            <th>" . t('table_header_distance') . "</th>
            <th>" . t('table_header_time') . "</th>
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
            <th>" . t('table_header_duration') . "</th>
            <th>" . t('table_header_distance') . "</th>
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
        echo '<td>' . sprintf(t('time_minutes'), $value) . '</td>';
        $time_ori = round($value / 60 * floatval($kmh_str) * 1000, 0);
        echo '<td>' . sprintf(t('distance_meters'), $time_ori) . '</td></tr>';
      }
      echo "</tbody></table>";

      echo "</div></div>";


      // echo "</div>";

    } else {

    }
    echo t('footer_text') . '<br><br>
      <a href="https://github.com/esojourn/calc/issues">' . t('footer_bug_report') . '</a> -
      <a href="https://github.com/esojourn/calc">' . t('footer_source_code') . '</a> -
      <a href="https://dingxuan.info/calc">' . t('footer_tool_link') . '</a><br>
      <a href="https://dingxuan.info">dingxuan.info</a></div>';
    //echo $msg; //21.0975, 42.195
    /*      echo 'Pace value: ' . $pace_str . '<br>';
          echo 'KMH value: ' . $kmh_str . '<br>';
          echo 'WHO: ' . $who . '<br>';*/

    ?>
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
