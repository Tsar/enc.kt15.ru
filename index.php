<?php

// (!!!) Генераторы должны брать кодируемую фразу их файла input.txt в текущей папке и создавать output.txt с html-таблицей

//          id                    название                         массив файлов                                              команда для запуска
$generators['Haffman_IV'] = array('Хаффман by Иоанн Волков (C++)', array('generators/haffman/haffmanIV'),                     './haffmanIV');
$generators['PPMA_OM']    = array('PPMA by Олег Мамин (java)',     array('generators/ppma_Oleg_Mamin/PPMA_Launcher.class',
                                                                         'generators/ppma_Oleg_Mamin/PPMA_Oleg_Mamin.class'), 'java PPMA_Launcher');

foreach ($generators as $gen => $genArr) {
    foreach ($genArr[1] as $fName) {
        if (!file_exists($fName)) {
            echo 'Fatal error: file "' . $fName . '" does not exist!';
            exit();
        }
    }
}

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

if (isset($_POST['inputText'])) {
    if (strlen($_POST['inputText']) > 1000) {
        echo "Max allowed input text length is 1000.";
    } else {

        $g = '';
        foreach ($generators as $gen => $genArr) {
            if (isset($_POST['submit' . $gen])) {
                $g = $gen;
                break;
            }
        }

        if ($g === '') {
            echo "Cool hacker?";
        } else {

            $i = file_get_contents('solve/current.txt');
            file_put_contents('solve/current.txt', $i + 1);
            
            $dir = 'solve/' . $i . '/';
            if (!mkdir($dir)) {
                echo "Fatal error happened, try again!";
            } else {

                foreach ($generators[$g][1] as $fName) {
                    copy($fName, $dir . basename($fName));
                }

                if (!chdir($dir)) {
                    echo "Fatal error happened, try again!";
                } else {
                    file_put_contents('ip.txt', getClientIP());
                    file_put_contents('input.txt', $_POST['inputText']);

                    exec($generators[$g][2]);

                    showRedirectPage("/?result=" . $i);
                }
            }
        
        }

    }
} else {
?>
<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Теория информации: алгоритмы кодирования</title>
</head>
<body>

<h1 align=center>Теория информации: алгоритмы кодирования</h1>

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
            $reportName = 'solve/' . $_GET['result'] . '/output.txt';
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

<form method="post" action="index.php">
    <table id="submitTable">
        <tr class="odd">            
            <td>Кодируемая фраза:</td>
            <td><input type="text" size="70" name="inputText"></td>
        </tr>
<?php
        $i = 0;
        foreach ($generators as $gen => $genArr) {
?>
        <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
            <td colspan="2"><center><input type="submit" name="submit<?php echo $gen; ?>" value="<?php echo $genArr[0]; ?>"></center></td>
        </tr>
<?php
            ++$i;
        }
?>
    </table>
</form>

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
