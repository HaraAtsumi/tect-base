<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission3-5</title>
</head>
<body>
<?php
$filename = "mission3-5.txt";

if (isset($_POST['editNumberSubmit'])) {
    $editNumber = $_POST['editNumber'];
    $password = $_POST["password"];
    // 編集対象の投稿を取得
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    $editData = '';
    foreach ($lines as $line) {
        $data = explode("<>", $line);
        if (count($data) >= 5) {
            $postnumber = $data[0];
            if ($postnumber == $editNumber) {
                $storedPassword = $data[4];
                if ($password == $storedPassword) {
                    $editData = $line;
                    break;
                } else {
                    echo "パスワードが違います。<br>";
                }
            }
        }
    }

    // 編集対象の投稿が存在する場合にフォームを表示
    if (!empty($editData)) {
        $data = explode("<>", $editData);
        $editName = $data[1];
        $editComment = $data[2];
    }
}


if (!empty($_POST['name']) && !empty($_POST["comment"])) {
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $timestamp = date("Y/m/d H:i:s");
    $password = $_POST["password"];
    
    if (!empty($_POST['editNumber'])) {
        // 編集モードであれば、該当する投稿を上書き更新
        $editNumber = $_POST['editNumber'];

        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $fp = fopen($filename, 'w');
        foreach ($lines as $line) {
            $data = explode("<>", $line);
            if (count($data) >= 5) {
                $postnumber = $data[0];
                if ($postnumber == $editNumber) {
                    fwrite($fp, "$postnumber<>$name<>$comment<>$timestamp<>$password" . PHP_EOL);
                } else {
                    fwrite($fp, $line . PHP_EOL);
                }
            }
        }
        fclose($fp);
    } else {
        // 編集モードでなければ、新規投稿として追加
        $postnumber = 1;
        if (file_exists($filename)) {
            $lines = file($filename, FILE_IGNORE_NEW_LINES);
            $lastLine = end($lines);
            $data = explode("<>", $lastLine);
            $postnumber = $data[0] + 1;
        }

        $newData = "$postnumber<>$name<>$comment<>$timestamp<>$password" . PHP_EOL;
        file_put_contents($filename, $newData, FILE_APPEND);
    }
}
    
if (isset($_POST['deleteNumberSubmit'])) {
    $deleteNumber = $_POST['deleteNumber'];
    $password = $_POST["password"];

    // ファイルの内容を削除
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    $fp = fopen($filename, 'w');
    $passwordMatch = false; // パスワードが一致するかどうかのフラグ

    foreach ($lines as $line) {
        $data = explode("<>", $line);
        if (count($data) >= 5) {
            $postnumber = $data[0];
            $storedPassword = $data[4];
            if ($postnumber == $deleteNumber && $password == $storedPassword) {
                $passwordMatch = true;
                continue; // パスワードが一致する行はスキップして次の行に進む
            }
            fwrite($fp, $line . PHP_EOL);
        }
    }
    fclose($fp);

    if (!$passwordMatch && !empty($_POST['deleteNumber']) && !empty($_POST["password"])) {
        echo "パスワードが違います。<br>";
    }
    if(empty($_POST['deleteNumber']) && empty($_POST["password"])){
    echo "入力してください";
}
}
?>

<form action="" method="post">
    <input type="text" name="name" placeholder="名前" value="<?php echo isset($editName) ? $editName : ''; ?>">
    <input type="text" name="comment" placeholder="コメント" value="<?php echo isset($editComment) ? $editComment : ''; ?>">
    <input type="hidden" name="editNumber" value="<?php echo isset($editNumber) ? $editNumber : ''; ?>">
    <input type="text" name="password" placeholder="パスワード">
    <input type="submit" name="submit" value="<?php echo isset($editNumber) ? '更新' : '送信'; ?>">
</form>
<!-- 削除フォーム -->
<form action="" method="post">
    <input type="text" name="deleteNumber" placeholder="削除対象番号">
    <input type="text" name="password" placeholder="パスワード">
    <input type="submit" name="deleteNumberSubmit" value="削除">
</form>
<!-- 編集フォーム -->
<form action="" method="post">
    <input type="text" name="editNumber" placeholder="編集対象番号">
    <input type="text" name="password" placeholder="パスワード">
    <input type="submit" name="editNumberSubmit" value="編集">
</form>


<?php
// ファイルの内容を表示
if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $data = explode("<>", $line);
        if (count($data) >= 5) {
            $postnumber = $data[0];
            $name = $data[1];
            $comment = $data[2];
            $timestamp = $data[3];
            echo "$postnumber $name $comment $timestamp<br>";
        }
    }
}
?>
</body>
</html>