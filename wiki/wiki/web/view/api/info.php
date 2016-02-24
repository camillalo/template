<div class="container">
    <div class="row">
        <div class="span3">
            <div class="left left-all">
                <ul class="parent">
                    <?php foreach($cats as $cat):?>
                    <?php if(isset($apis[$cat['id']])):?>
                            <li>
                            <span class="<?php if($cat['id'] == $current['cid']) echo 'active';?>"><?php echo
                                    $cat['name'] ;?> <b
                                    class="caret"></b></span>
                                <ul style="<?php if($cat['id'] == $current['cid']) echo 'display:block;';?>">
                                    <?php if(isset($apis[$cat['id']])):?>
                                        <?php foreach($apis[$cat['id']] as $api):?>
                                            <li class="inside">
                                                <a href="/api/<?php echo $api['id'];?>" class="<?php if($current['id']
                                                    ==
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
        <div class="span9">
            <?php if(!empty($current)):?>
            <div class="right">

                <h4>接口名称：</h4>
                <div class="block-red">
                    <b class="red"><?php echo $current['en_name'];?></b>
                </div>

                <h4>场景说明：</h4>
                <div class="well">
                    <?php echo nl2br($current['desc']);?>
                </div>
                <h4>参数说明：</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>参数</th>
                        <th>必须</th>
                        <th>类型</th>
                        <th>说明</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach((array)$current['params'] as $param):?>
                        <tr>
                            <td><?php echo $param['param'];?></td>
                            <td><?php echo $param['is_need'];?></td>
                            <td><?php echo $param['param_type'];?></td>
                            <td><?php echo $param['param_desc'];?></td>
                        </tr>
                    <?php endforeach;?>

                    </tbody>
                </table>
                <div >
                    <a class="btn btn-small btn-info" href="/edit/<?php echo $current['id'];?>">编 辑</a>
                    <a class="btn btn-small" href="/debug/<?php echo $current['id'];?>">调 试</a>
                </div>
                <br/>
                <h4>操作日志</h4>


            </div>
            <?php endif;?>
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
        })
    })
</script>


