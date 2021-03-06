<?php

// (!!!) Генераторы должны брать кодируемую фразу их файла input.txt в текущей папке и создавать output.txt с html-таблицей

//          id                    название                         массив файлов                                              команда для запуска
$generators['Haffman_IV'] = array('Хаффман by Иоанн Волков (C++)', array('generators/haffman_Ioann_Volkov/haffman'),          'chmod +x haffman && ./haffman > output.txt');
$generators['LZW_AE']     = array('LZW by Андрей Еремеев (java)',  array('generators/lzw_Andrei_Eremeev/LZW_Launcher.class',
                                                                         'generators/lzw_Andrei_Eremeev/LZW.class'),          'java LZW_Launcher');
#$generators['LZ78_v1_IV'] = array('LZ78 by Иоанн Волков [изначально пустой словарь] (python)',
#                                                                   array('generators/lz78_Ioann_Volkov/lz78_v1.py'),          'python lz78_v1.py > output.txt');
#$generators['LZ78_v2_IV'] = array('LZ78 by Иоанн Волков [изначально словарь содержит весь алфавит] (python)',
#                                                                   array('generators/lz78_Ioann_Volkov/lz78_v2.py'),          'python lz78_v2.py > output.txt');
$generators['PPMA_OM']    = array('PPMA by Олег Мамин (java)',     array('generators/ppma_Oleg_Mamin/PPMA_Launcher.class',
                                                                         'generators/ppma_Oleg_Mamin/PPMA_Oleg_Mamin.class'), 'java PPMA_Launcher');
$generators['Arith_KE']   = array('Арифметическое by Кирилл Елагин (python)', array('generators/all_Kirill_Elagin/compr.py'), 'python3 compr.py arith > output.txt');
$generators['LZ77_KE']    = array('LZ77 by Кирилл Елагин (python)',           array('generators/all_Kirill_Elagin/compr.py'), 'python3 compr.py lz77 > output.txt');
$generators['LZ78_KE']    = array('LZ78 by Кирилл Елагин (python)',           array('generators/all_Kirill_Elagin/compr.py'), 'python3 compr.py lz78 > output.txt');
$generators['PPMA_KE']    = array('PPMA by Кирилл Елагин (python)',           array('generators/all_Kirill_Elagin/compr.py'), 'python3 compr.py ppma > output.txt');

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
                    file_put_contents('algo.txt', $generators[$g][0]);

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
    <li><a href="index.php">Генераторы</a></li>
</ul>

<div id="content">
<?php
        if (is_numeric($_GET['result']) && is_dir('solve/' . $_GET['result'])) {
            $reportName = 'solve/' . $_GET['result'] . '/output.txt';
            if (file_exists($reportName)) {
                echo '<p><b>Алгоритм: ' . file_get_contents('solve/' . $_GET['result'] . '/algo.txt') . '</b></p>';
                echo '<p><b>Кодируемая фраза: ' . file_get_contents('solve/' . $_GET['result'] . '/input.txt') . '</b></p>';
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
    <li><a href="index.php" class="active">Генераторы</a></li>
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

<p align="center"><font size=2><i>Исходники этого сайта:</i> <a href="https://github.com/Tsar/enc.kt15.ru" target=_blank>https://github.com/Tsar/enc.kt15.ru</a></font></p>
</div>

</body>
</html>
<?php
}
?>
