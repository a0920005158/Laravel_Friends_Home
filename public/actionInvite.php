<?php
use App\Models\service_action;

echo service_action::all();
// $data = getActionListData(null, null);
// echo $data;

// function getActionListData($startdate, $enddate)
// {
//     $result = array();
//     if ($startdate == null || $enddate == null) {
//         return service_action::all();
//     } else {
//         $s_a_model = new service_action();
//         $result = $s_a_model->where('startdate', '>=', $startdate)->where('enddate', '<=', $enddate)->get();
//     }
//     echo $result;
//     usort($result, array($this, "cmp"));

//     return $result;
// }

// function cmp($a, $b)
// {
//     return $a['startdate'] < $b['startdate'];
// }
?>


<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>活動加入頁面</title>
</head>

<body>
    <script src="sdk.js"></script>
    <button id="login">登入 LINE</button>
    <button id="getContext">取得使用者類型資料</button>
    <textarea class="u-full-width" id="result-info" placeholder="結果會顯示在這"></textarea>
</body>

</html>

<script>
    window.addEventListener('load', () => {

        let liffID = '1655869245-O17LemkA';

        triggerLIFF();

        // 執行範例裡的所有功能
        function triggerLIFF() {

            // LIFF init
            liff.init({
                liffId: liffID
            }).then(() => {
                let isLoggedIn = liff.isLoggedIn();

                const btnLogin = document.getElementById('login');


                if(isLoggedIn){
                    btnLogin.style.display = "none";

                    // 取得使用者類型資料
                    const btnContent = document.getElementById('getContext');
                    btnContent.style.display = "block";
                    btnContent.addEventListener('click', () => {
                        const context = liff.getContext();
                        console.log('aaa');
                        console.log(context.userId);
                        // const outputContent = document.getElementById('result-info');
                        // console.log(context);
                        // outputContent.value = `${JSON.stringify(context)}`
                    });
                }else{
                    btnLogin.style.display = "block";

                    btnLogin.addEventListener('click', () => {
                        // 先確認使用者未登入
                        if (!isLoggedIn) {
                            liff.login({
                                redirectUri: 'https://ccf.bllin.net/actionInvite.php'
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

    })
</script>
