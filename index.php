<?php

function showRedirectPage($fl)
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<script language="JavaScript1.1" type="text/javascript">
<!--
location.replace("<?php echo $fl; ?>");
//-->
</script>
<noscript>
<meta http-equiv="Refresh" content="0; URL=<?php echo $fl; ?>">
</noscript>
</head>
<body>
<a href="<?php echo $fl; ?>">Нажмите</a>
</body>
</html>
<?php
}

function getClientIP() {
    $client_ip = ( !empty($HTTP_SERVER_VARS['REMOTE_ADDR']) ) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ( ( !empty($HTTP_ENV_VARS['REMOTE_ADDR']) ) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv('REMOTE_ADDR') );
    return $client_ip;
}

if (isset($_POST['ppmaInput']) && isset($_POST['ppmaSubmit'])) {
    if (strlen($_POST['ppmaInput']) > 1000) {
        echo "Max allowed input length is 1000.";
    } else {

        $i = file_get_contents('solve/current.txt');
        file_put_contents('solve/current.txt', $i + 1);
        
        $dir = 'solve/' . $i . '/';
        if (!mkdir($dir)) {
            echo "Fatal error happened, try again!";
        } else {
            copy('PPMA_Launcher.class',   $dir . 'PPMA_Launcher.class');
            copy('PPMA_Oleg_Mamin.class', $dir . 'PPMA_Oleg_Mamin.class');
            if (!chdir($dir)) {
                echo "Fatal error happened, try again!";
            } else {
                file_put_contents('ip.txt', getClientIP());
                file_put_contents('PPMA_Input.txt', $_POST['ppmaInput']);
                exec('java PPMA_Launcher');
                showRedirectPage("/?result=" . $i);
            }
        }

    }
} else {
?>
<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>PPMA: генератор таблички для отчёта</title>
</head>
<body>

<h1 align=center>PPMA: генератор таблички для отчёта</h1>

<div id="wrap">

<?php
    if (isset($_GET['result'])) {
?>

<ul id="lineTabs2">
    <li><a href="index.php?result=<?php echo htmlentities($_GET['result']); ?>" class="active">Результат</a></li>
    <li><a href="index.php">Генератор</a></li>
</ul>

<div id="content">
<?php
        if (is_numeric($_GET['result']) && is_dir('solve/' . $_GET['result'])) {
            $reportName = 'solve/' . $_GET['result'] . '/PPMA_Report.txt';
            if (file_exists($reportName)) {
                echo file_get_contents($reportName);
            } else {
                echo "<p>Результаты не готовы. Понажимайте F5, пока что...</p>";
            }
        } else {
            echo "<p>Кул хацкер?</p>";
        }
?>
</div>

<?php
    } else {
?>

<ul id="lineTabs1">
    <li><a href="index.php" class="active">Генератор</a></li>
</ul>

<div id="content">

<p>Возможно, не все смогут разобраться и написать алгоритм генерации таблички для PPMA для третьего ДЗ по Кудряшову.<br />
Даже потратив около пяти часов, кое-кто не смог. <b>Но Олег Мамин смог!</b><br /><br />
Поэтому для больших лентяев есть возможность загнать свою последовательность символов в текстовое поле ниже и получить готовую табличку.</p>

<form method="post" action="index.php">
    <table id="submitTable">
        <tr class="odd">            
            <td>Кстати, а вот и текстовое поле:</td>
            <td><input type="text" size="70" name="ppmaInput"></td>
        </tr>
        <tr class="even">
            <td colspan="2"><center><input type="submit" name="ppmaSubmit" value="Кнопочка. Догадаетесь, для чего :)"></center></td>
        </tr>
    </table>
</form>

<p>P.S. 1. Если кто-то до сих пор не понял, почему здесь ведутся разговоры про Олега Мамина, поясняю: этот сайт запускает его реализацию алгоритма.<br />
P.S. 2. Олег сказал: «там у меня тока считается не t_t(s) которое у него, а t_t'(s) которое он описывает на 152 стр в книжке».<br />
P.S. 3. <b>Кириллица уже <u>поддерживается</u>;</b> скажем спасибо Коле за те 18 байт, которые он добавил в код :)</p>

</div>

<?php
    }
?>

</div>

</body>
</html>
<?php
}
?>
