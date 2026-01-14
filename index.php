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
<html data-theme="dark">
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
    /* CSS 变量 - 亮色模式 */
    :root {
      --bg-color: #ffffff;
      --bg-secondary: #f7f7f7;
      --text-color: #333333;
      --text-secondary: #666666;
      --border-color: #cccccc;
      --input-bg: #ffffff;
      --active-bg: #2e7d32;
      --active-text: #ffffff;
      --result-bg: #e8f5e9;
      --result-text: #2e7d32;
      --button-primary-bg: #2e7d32;
      --button-primary-text: #ffffff;
      --table-odd-bg: #f2f2f2;
      --error-color: #f00;
      --lang-bg: #fff;
      --link-color: #2e7d32;
    }

    /* 暗黑模式 */
    [data-theme="dark"] {
      --bg-color: #0d1a14;
      --bg-secondary: #142820;
      --text-color: #eaeaea;
      --text-secondary: #a8c4b0;
      --border-color: #2d4a3a;
      --input-bg: #1a3528;
      --active-bg: #5dd863;
      --active-text: #0d1a14;
      --result-bg: #142820;
      --result-text: #5dd863;
      --button-primary-bg: #5dd863;
      --button-primary-text: #0d1a14;
      --table-odd-bg: #162920;
      --error-color: #ff6b6b;
      --lang-bg: #1a3528;
      --link-color: #5dd863;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    h1, h2 {
      font-family: "Nexa", "微软雅黑", "Open Sans", "Noto Sans S Chinese", sans-serif;
      font-size: 20px;
      color: var(--text-color);
    }

    .error {
      color: var(--error-color);
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
      background-color: var(--active-bg) !important;
      color: var(--active-text) !important;
      border-color: var(--active-bg) !important;
      box-shadow: 0 0 8px rgba(46, 125, 50, 0.4);
    }

    [data-theme="dark"] .who-active {
      box-shadow: 0 0 12px rgba(0, 255, 136, 0.5);
    }

    input.pace, input.kmh {
      width: 180px;
      height: 56px;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
      border-radius: 8px;
      border: 2px solid var(--border-color);
      background-color: var(--input-bg);
      color: var(--text-color);
    }

    .pure-button {
      width: 180px;
      height: 56px;
      font-size: 20px;
      font-weight: bold;
      border-radius: 8px;
      margin: 8px 0;
    }

    .pure-button-primary {
      background-color: var(--button-primary-bg);
      color: var(--button-primary-text);
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
      border: 1px solid var(--border-color);
      border-radius: 4px;
      cursor: pointer;
      background-color: var(--lang-bg);
      color: var(--text-color);
      font-size: 14px;
    }

    @media (max-width: 767px) {
      .lang-selector {
        position: static;
        text-align: center;
        margin: 10px 0;
      }
    }

    /* 结果展示区域 */
    .result-display {
      margin: 24px auto;
      padding: 20px;
      max-width: 400px;
    }

    .result-row {
      display: flex;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .result-item {
      flex: 1;
      min-width: 140px;
      max-width: 180px;
      padding: 16px;
      background-color: var(--result-bg);
      border-radius: 12px;
      text-align: center;
    }

    .result-item .result-label {
      font-size: 14px;
      color: var(--text-secondary);
      margin-bottom: 8px;
    }

    .result-item .result-value {
      font-size: 48px;
      font-weight: bold;
      color: var(--result-text);
      line-height: 1.2;
    }

    .result-item .result-unit {
      font-size: 14px;
      color: var(--text-secondary);
      margin-top: 4px;
    }

    /* 主题切换按钮 */
    .theme-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background-color: var(--button-primary-bg);
      color: var(--button-primary-text);
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      z-index: 1000;
      transition: transform 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .theme-toggle:hover {
      transform: scale(1.1);
    }

    .theme-toggle svg {
      width: 24px;
      height: 24px;
    }

    /* 亮色模式：显示月亮图标（点击切换到暗色） */
    .icon-moon { display: block; }
    .icon-sun { display: none; }

    /* 暗色模式：显示太阳图标（点击切换到亮色） */
    [data-theme="dark"] .icon-moon { display: none; }
    [data-theme="dark"] .icon-sun { display: block; }

    /* 表格暗黑模式适配 */
    .pure-table {
      background-color: var(--bg-color);
      color: var(--text-color);
      border-color: var(--border-color);
    }

    .pure-table thead {
      background-color: var(--bg-secondary);
      color: var(--text-color);
    }

    .pure-table-odd {
      background-color: var(--table-odd-bg) !important;
    }

    .pure-table-odd td {
      background-color: var(--table-odd-bg) !important;
    }

    .pure-table tr td {
      background-color: var(--bg-color);
    }

    /* 链接颜色 */
    a {
      color: var(--link-color);
    }

    a:hover {
      color: var(--link-color);
      opacity: 0.8;
    }

  </style>
  <script src="jquery-3.4.1.min.js"></script>
  <script>
      // 防抖函数
      function debounce(func, wait) {
          var timeout;
          return function() {
              var context = this, args = arguments;
              clearTimeout(timeout);
              timeout = setTimeout(function() {
                  func.apply(context, args);
              }, wait);
          };
      }

      // 格式化配速显示 (如 "630" -> "6'30"")
      function formatPace(pace) {
          var str = pace.toString();
          if (str.length === 3) {
              return str.charAt(0) + "'" + str.substring(1) + '"';
          } else if (str.length === 4) {
              return str.substring(0, 2) + "'" + str.substring(2) + '"';
          }
          return pace;
      }

      // 实时计算函数
      function calculateResult() {
          var who = $('input[name="who"]').val();
          var paceVal = $('input[name="pace"]').val();
          var kmhVal = $('input[name="kmh"]').val();

          // 清空错误
          $('.error').hide();

          if (who === 'pace' && paceVal && paceVal.length >= 3) {
              // 配速 -> km/h
              var pace = parseInt(paceVal);
              if (isNaN(pace) || pace < 200 || pace > 999) {
                  return;
              }
              var sec = pace % 100;
              var min = Math.floor(pace / 100);
              if (sec >= 60) {
                  return;
              }
              var totalSeconds = min * 60 + sec;
              var kmh = (3600 / totalSeconds).toFixed(1);

              // 更新结果
              $('.result-display').show();
              $('.result-pace .result-value').text(formatPace(paceVal));
              $('.result-kmh .result-value').text(kmh);
              $('input[name="kmh"]').val(kmh);

          } else if (who === 'kmh' && kmhVal) {
              // km/h -> 配速
              var kmh = parseFloat(kmhVal);
              if (isNaN(kmh) || kmh <= 6 || kmh > 25) {
                  return;
              }
              var paceMinutes = 60 / kmh;
              var paceMin = Math.floor(paceMinutes);
              var paceSec = Math.round((paceMinutes - paceMin) * 60);
              if (paceSec >= 60) {
                  paceSec = 0;
                  paceMin += 1;
              }
              var paceStr = paceMin.toString() + (paceSec < 10 ? '0' : '') + paceSec.toString();

              // 更新结果
              $('.result-display').show();
              $('.result-pace .result-value').text(formatPace(paceStr));
              $('.result-kmh .result-value').text(kmhVal);
              $('input[name="pace"]').val(paceStr);
          }
      }

      // 防抖版本，延迟 600ms
      var debouncedCalculate = debounce(calculateResult, 600);

      $(document).ready(function () {
          // kmh 输入事件
          $('input[name="kmh"]').on('input', function () {
              $('input[name="who"]').val('kmh');
              $('input[name="pace"]').removeClass('who-active');
              $(this).addClass('who-active');
              debouncedCalculate();
          });

          // pace 输入事件
          $('input[name="pace"]').on('input', function () {
              $('input[name="who"]').val('pace');
              $('input[name="kmh"]').removeClass('who-active');
              $(this).addClass('who-active');
              debouncedCalculate();
          });

          // 点击事件（切换高亮）
          $('input[name="kmh"]').click(function () {
              $('input[name="who"]').val('kmh');
              $('input[name="pace"]').removeClass('who-active');
              $(this).addClass('who-active');
          });

          $('input[name="pace"]').click(function () {
              $('input[name="who"]').val('pace');
              $('input[name="kmh"]').removeClass('who-active');
              $(this).addClass('who-active');
          });

          // 重置按钮
          $('input[type="reset"]').click(function (event) {
              $('input[name="kmh"]').val('').removeClass('who-active');
              $('input[name="pace"]').val('').removeClass('who-active');
              $('input[name="who"]').val('');
              $('.result-display').hide();
              $('.error').hide();
          });
      });
  </script>
</head>
<body>
<div class="content">
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
    <!-- 实时结果展示区域 -->
    <div class="result-display" style="display: none;">
      <div class="result-row">
        <div class="result-item result-pace">
          <div class="result-label"><?php echo t('label_pace'); ?></div>
          <div class="result-value">--</div>
          <div class="result-unit">min/km</div>
        </div>
        <div class="result-item result-kmh">
          <div class="result-label"><?php echo t('label_kmh'); ?></div>
          <div class="result-value">--</div>
          <div class="result-unit">km/h</div>
        </div>
      </div>
    </div>
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
  <div class="lang-selector">
    <select id="langSelect" onchange="location.href='?lang='+this.value;">
      <option value="zh" <?php echo $currentLang == 'zh' ? 'selected' : ''; ?>>简体中文</option>
      <option value="zh-TW" <?php echo $currentLang == 'zh-TW' ? 'selected' : ''; ?>>繁體中文</option>
      <option value="en" <?php echo $currentLang == 'en' ? 'selected' : ''; ?>>English</option>
      <option value="de" <?php echo $currentLang == 'de' ? 'selected' : ''; ?>>Deutsch</option>
    </select>
  </div>
</div>
<!-- 主题切换按钮 -->
<button class="theme-toggle" onclick="toggleTheme()" aria-label="切换主题">
  <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>
  <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
</button>
<script>
    function toggleTheme() {
        var html = document.documentElement;
        var currentTheme = html.getAttribute('data-theme');
        var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    // 页面加载时恢复主题
    (function() {
        var savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();
</script>
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
