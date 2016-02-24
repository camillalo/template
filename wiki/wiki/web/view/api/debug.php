<div class="container">
    <div class="row">
        <div class="span3">
            <div class="left left-all">
                <ul class="parent">
                    <?php foreach($cats as $cat):?>
                        <?php if(isset($apis[$cat['id']])):?>
                            <li>
                            <span style="font-weight: bold;" class="<?php if($cat['id'] == $current['cid']) echo 'active';?>"><?php echo $cat['name'];?> <b
                                    class="caret"></b></span>
                                <ul style="<?php if($cat['id'] == $current['cid']) echo 'display:block;';?>">
                                    <?php if(isset($apis[$cat['id']])):?>
                                        <?php foreach($apis[$cat['id']] as $api):?>
                                            <li class="inside">
                                                <a href="/debug/<?php echo $api['id'];?>" class="<?php if($current['id'] ==
                                                    $api['id']) echo 'active';
                                                ?>" title="<?php echo $api['zh_name'];?>"><?php echo $api['en_name'];?></a>
                                            </li>
                                        <?php endforeach;?>
                                    <?php endif;?>

                                </ul>
                            </li>
                        <?php endif;?>

                    <?php endforeach;?>

                </ul>
            </div>
        </div>
        <style>
            #tform  .controls{
               margin-left: 0;
               margin-bottom: 15px;
               display: inline-block;
            }
            #tform  .controls .add-on{
                width: 120px;
            }
        </style>
        <div class="span9">
                <div class="right">
                    <form class="form-horizontal" action="javascript:;" id="tform">
                        <input type="hidden" name="api_name" value="<?php echo $current['en_name'];?>"/>
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>参数</th>
                            <th>=</th>
                            <th>值</th>
                            <th>X</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach((array)$current['params'] as $param):?>
                            <tr class="param-tr">
                                <td title="<?php echo $param['param_desc'];
                                ?>"><input title="<?php echo $param['param_desc'];
                                    ?>" type="text" class="span2" name="param[]" value="<?php echo $param['param'];
                                    ?>"/></td>
                                <td>=</td>
                                <td><input type="text" value="" class="input-xxlarge" name="param_val[]"/></td>
                                <td><a href="javascript:void(0);" class="param-del">X</a></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <div>

                        输出格式：
                        <select name="out_type" class="span2">
                            <option value="json">json</option>
                            <option value="array">array</option>
                            <option value="xml">xml</option>
                            <option value="phparray">phparray</option>
                        </select>
                        <button class="btn btn-info" type="button" id="tsend">发送请求</button>
                        <button class="btn add-param" style="float: right;">添加参数</button>
                    </div>
                    </form>
                    <div>
                        <style>
                        .txt_show{
                            background-color: #fff;
                            border: 1px solid #ccc;
                            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
                            -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
                            box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
                            -webkit-transition: border linear .2s,box-shadow linear .2s;
                            -moz-transition: border linear .2s,box-shadow linear .2s;
                            -o-transition: border linear .2s,box-shadow linear .2s;
                            transition: border linear .2s,box-shadow linear .2s;
                            min-height: 100px;
                            padding: 10px;
                            font-size: 13px;
                        }
                        </style>
                        <h6>请求数据</h6>
                        <div>
                            <pre class="txt_show txt_to" contenteditable="true" placeholder="response"></pre>
                        </div>
                        <h6>返回数据</h6>
                        <div>
                            <pre class="txt_show txt_back" contenteditable="true" placeholder="response"></pre>
                        </div>

                    </div>

                </div>
        </div>
    </div>


</div>

<script>
    $(function(){

        $('ul li span').on('click',function(){
            $(this).parent('li').siblings('li').children('ul').slideUp();
            $(this).parent('li').siblings('li').children('span').removeClass('active');
            if($(this).next('ul').css('display') == 'none') {
                $(this).addClass('active').next('ul').slideDown();

            } else {
                $(this).removeClass('active').next('ul').slideUp();
            }
        });


        // 删除操作
        $('.param-del').on('click', function(){
            $(this).parents('tr').remove();
        })

        // 自定义参数
        var sparam = '<tr class="param-tr"><td><input type="text" class="span2" name="param[]" ' +
            'value=""/></td><td>=</td><td><input type="text" name="param_val[]" class="input-xxlarge" /></td> <td><a href="javascript:void(0);" class="param-del">X</a></td></tr>';

        $('.add-param').on('click',function(){
            $('tbody').append(sparam);
            $('.param-del').on('click',function(){
                $(this).parents('tr').remove();
            })
        });



        //////////////////////////////////
        // 调试请求发送
        //////////////////////////////////
        var sclick = 0;
        $('#tsend').on('click', function(){
            if(sclick == 100) {
                //UI.popTip(1, '请求正在处理中，请不要重复发送');
                return false;
            } else {
                $.ajax({
                    type:'post',
                    url:'/debug/send',
                    data:{
                        'form':$('#tform').serialize()
                    },
                    dataType:'json',
                    beforeSend:function(){
                        sclick = 1;
                        UI.popTip(2, '请求处理中……');
                    },
                    success:function(data) {
                        sclick = 0;
                        UI.popTip(-1,'222');
                        UI.popTip(0, '请求发送成功');
                        $('.txt_to').html(String(data.request));
                        $('.txt_back').html(String(data.response));
                    },
                    error:function(){
                        UI.popTip(1, '网络忙，请稍后重试');
                        sclick = 0;
                        return false;
                    }

                })
            }
        });

    });
</script>


