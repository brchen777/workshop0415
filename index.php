<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>結帳</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="title">DVD 結帳系統</div>
<form action="check.php" method="post">
    <div class="movie_num">紅標 <input type="text" name="red"> 片</div>
    <div class="movie_num">綠標 <input type="text" name="green"> 片</div>
    <div class="movie_num">藍標 <input type="text" name="blue"> 片</div>
    <div><button type="submit">計算</button></div>
</form>
</body>
</html>