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
<script src="js/sdk.js"></script>
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<div class="card" style="width: 100%;">
  <img src="img/b1.png" class="card-img-top" alt="...">
  <div class="card-body">
    <h5 class="card-title">家友會參加活動列表</h5>
    <div id="login" style="color:red;">
        請先登入Line進行操作<br>
        <button>登入 LINE</button>
    </div>

    <table class="table" id="table">
        <thead>
            <tr>
            <th scope="col">活動名稱</th>
            <th scope="col">開始時間</th>
            <th scope="col">結束時間</th>
            <th scope="col">參加</th>
            <th scope="col">取消參加</th>
            <th scope="col">測試</th>
            </tr>
        </thead>
        <tbody>
            @foreach( $action as $ac )
                <tr>
                    <th scope="row">{{$ac['name']}}</th>
                    <td>{{$ac['startdate']}}</td>
                    <td>{{$ac['enddate']}}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" id="invite" onclick="addMem({{ $ac['aid'] }})">參加</button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" id="cancel_invite">取消參加</button>
                    </td>
                    <td>{{$mid}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>
</div>
</body>

</html>

<script>
    var userId = "";
    var name = "";
    var $mid = "";
    window.addEventListener('load', () => {
        triggerLIFF();
    })

    // 執行範例裡的所有功能
    function triggerLIFF() {
        let liffID = '1655869245-O17LemkA';
        // LIFF init
        liff.init({
            liffId: liffID
        }).then(() => {
            let isLoggedIn = liff.isLoggedIn();
            const btnLogin = document.getElementById('login');
            const table = document.getElementById('table');
            if(isLoggedIn){
                btnLogin.style.display = "none";
                const context = liff.getContext();
                userId = context.userId;
                getUserData(response=>{
                    name = response.response.name;
                    $mid = response.response.mid;
                    table.style.display = "block";
                });
            }else{
                table.style.display = "none";
                btnLogin.style.display = "block";
                btnLogin.addEventListener('click', () => {
                    // 先確認使用者未登入
                    if (!isLoggedIn) {
                        liff.login({
                            redirectUri: 'https://ccf.bllin.net/actionInvite'
                        });
                    }
                });
            }
            // 關閉 LIFF
            // const btnClose = document.getElementById('closeLIFF');
            // btnClose.addEventListener('click', () => {
            //     // 先確認是否在 LINE App 內
            //     if (isInClient) {
            //         liff.closeWindow();
            //     }
            // });
        }).catch(error => {
            console.log(error);
        });
    }
    function checkMem(actionmem){
        console.log(actionmem);
        return true;
    }

    function getUserData(callback) {
        iPost(
            "https://ccf.bllin.net/bgx/getUserData",
            false,
            {
                "userId":userId,
            },
            'json',
            (response) => {
                if (response.callState === 1) {
                    alert('發生錯誤!');
                } else {
                    callback(response);
                }
            },
            (error) => {
                alert('發生錯誤，請聯繫管理員!(error)');
            }
        );
    }

    function addMem(aid){
        iPost(
            "https://ccf.bllin.net/bgx/addActionMember",
            false,
            {
                "addUserId":userId,
                "aid":aid
            },
            'json',
            (response) => {
                if (response.callState === 1) {
                    alert('發生錯誤!');
                } else {
                    alert('活動加入成功!');
                    window.location.reload();
                }
            },
            (error) => {
                alert('發生錯誤，請聯繫管理員!(error)');
            }
        );
    }
    function iPost(url, async, data, dataType, iThen, iCatch) {
        $.ajax({
            async: async,
            type: "POST",
            url: url,
            dataType: dataType,
            data: data,
            success: function (data) {
                iThen(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //根據http status code 顯示不同警告提示
                switch (xhr.status) {
                    case 403:
                    alert("您无此页面访问权限! (" + xhr.status + ")");
                    break;
                    case 404:
                    alert("您访问页面不存在! (" + xhr.status + ")");
                    break;
                    case 500:
                    alert(
                        "伺服器发生错误，请回报客服协助处理! (" + xhr.status + ")"
                    );
                    default:
                    alert(
                        "伺服器发生错误，请回报客服协助处理! (" + xhr.status + ")"
                    );
                    break;
                    break;
                }
                iCatch(xhr.status);
            },
        });
    }
</script>
