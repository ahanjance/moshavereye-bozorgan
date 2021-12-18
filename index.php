<?php
    $listofq = array();
    $listofnames = array();
    $finalans = fopen("messages.txt", "r");
    $i = 0;
    while(!feof($finalans))
    {
        $listofq[$i] = fgets($finalans);
        $i++;
    }
    $persons = json_decode(file_get_contents('people.json'));
    $listsize = 0;
    foreach ($persons as $key => $value) 
    {
        $listofnames[$listsize] = $key;
        $listsize++;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $en_name = $_POST['person'];
        $question = $_POST['question'];
        $hashdler = hash('adler32', $question." ".$en_name);
        $msg = $listofq[hexdec($hashdler) % 16];
        foreach($persons as $key => $value)
        {
            if ($en_name == $key) 
            {
                $fa_name = $value;
                break;
            }
        }
    }
    else
    {
        $question = '';
        $en_name = $listofnames[array_rand($listofnames)];
        foreach($persons as $key => $value)
        {
            if ($en_name == $key) 
            {
                $fa_name = $value;
                break;
            }
        }
    }
    if(!preg_match("/^آیا/iu", $question))
        $msg = "سوال درستی پرسیده نشده";
    if(!preg_match("/\?$/i", $question) && !preg_match("/؟$/u", $question))
        $msg = "سوال درستی پرسیده نشده";
    if (empty($question))
    {
        $asking = '';
        $msg = "سوال خود را بپرس";
    }
    else
        $asking = 'پرسش:';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="styles/default.css">
    <title>مشاوره بزرگان</title>
</head>
<body>
<p id="copyright">تهیه شده برای درس کارگاه کامپیوتر،دانشکده کامییوتر، دانشگاه صنعتی شریف</p>
<div id="wrapper">
    <div id="title">
        <span id="label"><?php echo $asking ?></span>
        <span id="question"><?php echo $question ?></span>
    </div>
    <div id="container">
        <div id="message">
            <p><?php echo $msg ?></p>
        </div>
        <div id="person">
            <div id="person">
                <img src="images/people/<?php echo "$en_name.jpg" ?>"/>
                <p id="person-name"><?php echo $fa_name ?></p>
            </div>
        </div>
    </div>
    <div id="new-q">
        <form method = "post" action = "<?php print $_SERVER['PHP_SELF'];?>">
            سوال
            <input type="text" name="question" value="<?php echo $question ?>" maxlength="150" placeholder="..."/>
            را از
            <select name="person">
                <?php
                $sented = file_get_contents('people.json');
                $finallist = json_decode($sented);
                foreach($finallist as $key => $value)
                    if ($key == $en_name)
                        print "<option value=$key selected> $value </option>";
                    else
                        print "<option value=$key> $value </option>";
                ?>
            </select>
            <input type="submit" value="بپرس"/>
        </form>
    </div>
</div>
</body>
</html>