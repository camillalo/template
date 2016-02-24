<div class="container">
    <div class="row">
        <div class="span3" style="position: relative;">
            <div class="left left-all">
                <ul>
                    <?php foreach($cats as $key => $cat):?>
                        <li>
                            <span style="font-weight: bold;" class="<?php if($key == 0) echo 'current';?>" data-target="#cat<?php echo $cat['id'];?>">
                                ○  <?php echo $cat['name'];?>
                            </span>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="span9">
            <div class="right">
                <?php foreach($cats as $cat):?>
                <?php if(isset($apis[$cat['id']])):?>
                <div id="cat<?php echo $cat['id'];?>"  class="block-green"><b><?php echo $cat['name'];?></b></div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <?php foreach($apis[$cat['id']] as $api):?>
                                <tr>
                                    <td width="30%"><a href="/api/<?php echo $api['id'];?>"><?php echo $api['en_name'];
                                            ?></a></td>
                                    <td><?php echo $api['zh_name'];?></td>
                                    <td>
                                        <a href="/api/<?php echo $api['id'];?>">详情</a>
                                        <a href="/edit/<?php echo $api['id'];?>">编辑</a>
                                        <a href="/debug/<?php echo $api['id'];?>">调试</a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>

                <?php endif;?>
                <?php endforeach;?>

            </div>
        </div>
    </div>


</div>

<div class="container" style="height: 500px;"></div>
<script>
    $(function(){
        $('.left ul li span').on('click', function(){
            $(this).addClass('current').parent('li').siblings('li').children('span').removeClass('current');
            var activeItem = $(this).data('target');

            $('body,html').animate({scrollTop:$(activeItem).offset().top - 60},200);
        })
    })
</script>