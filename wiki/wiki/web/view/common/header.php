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
<div id="header">
    <div class="navbar navbar-fixed-top">

            <div class="navbar-inner">
                <div class="container">
                    <a href="/" class="brand">WiKi</a>
                    <ul class="nav">
                        <li class="<?php if(isset($_active) && $_active == 'index') echo 'active';?>"><a href="/"> 列表</a></li>
                        <li class="<?php if(isset($_active) && $_active == 'debug') echo 'active';?>"><a href="/debug"> 调试</a></li>
                    </ul>
                    <ul class="nav pull-right " id="logincheck" data-login="<?php echo getUserInfo('username');?>">
                        <?php if(getUserInfo()):?>
                            <li class="<?php if(isset($_active) && $_active == 'add') echo 'active';?>"><a
                                    href="/add"
                                    id="write"><i class="icon-edit"></i> 撰写文档</a></li>
                            <li ><a href="javascript:;">欢迎你，<?php echo getUserInfo
                                    ('username');?></a></li>
                            <li ><a href="/logout">→退出</a></li>
                        <?php else:?>
                        <li class="tologin"><a href="javascript:;" ><i
                                    class="icon-user"></i>
                                登录</a></li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>

    </div>
</div>
<div class="row">
    <div class="span9"></div>
</div>

<?php if(getUserInfo()):?>
<?php else:?>
    <div class="pop-win" id="loginbox">
        <div class="pop-wrap">
            <div class="pop-header">
                <div class="pop-title">登录WiKi</div>
                <span class="pop-close">X</span>
            </div>
            <div class="pop-content">
                <form class="" action="javascript:;" id="loginform">
                    <div class="control-group">
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">账号</span>
                                <input type="text" name="username" value=""/>
                            </div>
                        </div>
                        <div class="controls">
                            <div class="input-prepend">
                                <span class="add-on">密码</span>
                                <input type="password" name="password" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <button class="btn btn-info" type="button" id="logit">登录WiKi</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
<?php endif;?>

<script>
    $(function(){
        $('.tologin').popWin({
            'target':'#loginbox',
            'withMask':true,
            'draggable':true
        });


        var sclick = 0;
        $('#logit').on('click', function(){
            if(sclick == 1) {
                //UI.popTip(1, '请求正在处理中，请不要重复发送');
                return false;
            } else {
                $.ajax({
                    type:'post',
                    url:'/login',
                    data:{
                        'username':$('input[name=username]').val(),
                        'password':$('input[name=password]').val()
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
                            window.location.href = window.location.href;
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
            }
        });
    })
</script>