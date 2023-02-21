<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
    <title>活動加入頁面</title>
</head>
<body>
<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <div class="card" style="width: 100%;" id="app">
        <!-- <img src="img/b1.png" class="card-img-top" alt="..."> -->
        <div class="card-body">
            <h5 class="card-title">執行結果</h5>
            {{$result}}<br>
            <a id='timer'>5</a>秒後轉跳
        </div>
    </div>
    
</body>

</html>

<script>
    setTimeout('countdown()', 1000);
    function countdown() {
        var s = document.getElementById('timer');
        s.innerHTML = s.innerHTML - 1;
        if (s.innerHTML == 0)
            window.location = 'https://liff.line.me/1655869245-O17LemkA';
        else
            setTimeout('countdown()', 1000);
    }
</script>
