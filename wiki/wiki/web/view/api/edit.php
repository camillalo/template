<div class="container">
    <div class="api-add">

        <form class="form-horizontal" method="post" action="/edit/<?php echo $api['id'];?>">
            <div class="control-group">
                <label class="control-label" for="zh_name">接口类别<font color="red">*</font></label>
                <div class="controls">
                    <select name="cat" id="cat">
                        <option value="">选择类别</option>
                        <?php foreach($cats as  $cat):?>
                            <option value="<?php echo $cat['id'];?>" <?php if($api['cid'] == $cat['id']) echo
                            'selected';?>>
                               <?php echo $cat['name'];?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="en_name">接口名称(英文)<font color="red">*</font></label>
                <div class="controls">
                    <input type="text" id="en_name" name="en_name" placeholder="如：order.add" value="<?php echo $api['en_name'];?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="zh_name">中文名称</label>
                <div class="controls">
                    <input type="text" id="zh_name" name="zh_name" placeholder="如：添加订单" value="<?php echo $api['zh_name'];?>">
                </div>
            </div>



            <div class="control-group">
                <label class="control-label" for="desc">场景说明</label>
                <div class="controls">
                    <textarea name="desc" id="desc" class="input-xxlarge" rows="5" placeholder="desc"><?php echo $api['desc'];?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="url">参数</label>
                <div class="controls">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>参数</th>
                            <th>必须</th>
                            <th>类型</th>
                            <th width="50%">说明</th>
                            <th>X</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($api['params'] as $param):?>
                        <tr class="param-tr" >
                            <td>
                                <input type="text" name="param[]" placeholder="param" class="span2" value="<?php echo
$param['param'];?>">
                            </td>
                            <td>
                                <select name="is_need[]" class="span1">
                                    <option value="no" <?php if($param['is_need'] == 'no') echo 'selected';
                                    ?>>no</option>
                                    <option value="yes" <?php if($param['is_need'] == 'yes') echo 'selected';
                                    ?>>yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="param_type[]"  class="span2">
                                    <option value="string" <?php if($param['param_type'] == 'string') echo 'selected';
                                    ?>>string </option>
                                    <option value="int" <?php if($param['param_type'] == 'int') echo 'selected';
                                    ?>>int</option>
                                    <option value="float" <?php if($param['param_type'] == 'float') echo 'selected';
                                    ?>>float</option>
                                    <option value="json" <?php if($param['param_type'] == 'json') echo 'selected';
                                    ?>>json</option>
                                </select>
                            </td>
                            <td>
<textarea name="param_desc[]"  cols="30" class="span4"><?php echo $param['param_desc'];?></textarea>
                            </td>
                            <td><a href="javascript:void(0);" class="param-del">X</a></td>
                        </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                    <div style="text-align: right;">
                        <button class="btn" type="button" id="param-add">添加参数</button>
                    </div>



                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-info btn-large">完成</button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    $(function(){

        var clo = '<tr class="param-tr" ><td><input type="text" name="param[]" placeholder="param" class="span2"></td><td><select name="is_need[]" class="span1"><option value="no">no</option><option value="yes">yes</option></select></td><td><select name="param_type[]"  class="span2"><option value="string">string </option><option value="int">int</option><option value="float">float</option><option value="json">json</option></select></td><td><textarea name="param_desc[]"  placeholder="param_desc" cols="30" class="span4"></textarea></td><td><a href="javascript:void(0);" class="param-del">X</a></td></tr>';
        $('#param-add').on('click', function(){
            $('tbody').append(clo);
            // 删除操作
            $('.param-del').on('click', function(){
                $(this).parents('tr').remove();
            });
        })


        $('form').submit(function(){

            if($('select[name=cat]').val() == '') {
                UI.popTip(1, '请选择项目类别');
                return false;
            }


            if($('input[name=en_name]').val() == '') {
                UI.popTip(1, '英文名不能为空');
                return false;
            }

        })

        // 删除操作
        $('.param-del').on('click', function(){
            $(this).parents('tr').remove();
        })
    })
</script>