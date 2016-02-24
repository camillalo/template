<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>wiki | <?php echo isset($_title) ? $_title : '';?></title>
    <link href="<?php echo staticUrl('css/bootstrap.min.css');?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo staticUrl('css/bootstrap-responsive.min.css');?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo staticUrl('css/wiki.css');?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
        var $CONFIG = {};
        $CONFIG['uid'] = '';
        $CONFIG['isLogin'] = 0;
        $CONFIG['version'] = 1.1;
        $CONFIG['lang'] = 'zh-cn';
        $CONFIG['serverTime'] = <?php echo time();?>;
        $CONFIG['baseUrl'] = '<?php echo baseUrl();?>';
        $CONFIG['jsPath'] = '<?php echo staticUrl('js');?>';
        $CONFIG['cssPath'] = '<?php echo staticUrl('css');?>';
        $CONFIG['imgPath'] = '<?php echo staticUrl('img');?>';
        $CONFIG['redirect'] = '';
    </script>
    <script src="<?php echo staticUrl('js/jquery-1.8.2.min.js');?>"></script>
    <script src="<?php echo staticUrl('js/bootstrap.min.js');?>"></script>
    <script src="<?php echo staticUrl('js/wiki.js');?>"></script>
</head>
<body>

<style>
    #footer{
        display: none;
    }
    .pageloginbox{
        -webkit-box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        -moz-box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        width: 300px;
        height: 220px;
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left:-150px;
        margin-top:-180px;
        background: #fff;
        padding: 30px;
        border-radius: 5px;
    }
</style>
<div class="pageloginbox" style="">

    <form class="pagelogin" action="javascript:;">
        <div class="control-group">
            <div class="controls">
                <div class="block-green">
                登录WiKi
                </div>
            </div>
            <br/>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on">账号</span>
                    <input type="text" name="username" value=""/>
                </div>
            </div>
            <br/>
            <div class="controls">
                <div class="input-prepend">
                    <span class="add-on">密码</span>
                    <input type="password" name="password" value=""/>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <input type="hidden" name="ref" value="<?php if(isset($_GET['ref']) && $_GET['ref']) echo $_GET['ref'];?>"/>
            <button class="btn btn-info btn-login" type="button" >登录WiKi</button>
        </div>

    </form>
</div>

<script>
    $(function(){
        $('form').submit(function(){
            return false;
        });
        $('.btn-login').on('click', function(){
            $.ajax({
                type:'post',
                url:'/login',
                data:{
                    'username':$('.pagelogin input[name=username]').val(),
                    'password':$('.pagelogin input[name=password]').val()
                },
                dataType:'json',
                beforeSend:function(){
                    sclick = 1;
                    UI.popTip(2, '登录中……');
                },
                success:function(data) {
                    sclick = 0;
                    UI.popTip(-1,'222');
                    if(data.ret == 0) {
                        UI.popTip(0, '登录成功');
                        window.location.href = $('.pagelogin input[name=ref]').val() ? $('.pagelogin ' +
                            'input[name=ref]').val() : '/';
                    } else {
                        UI.popTip(1, data.msg);
                    }
                },
                error:function(){
                    UI.popTip(1, '网络忙，请稍后重试');
                    sclick = 0;
                    return false;
                }

            })
        })
    });
</script>
</body>
</html>