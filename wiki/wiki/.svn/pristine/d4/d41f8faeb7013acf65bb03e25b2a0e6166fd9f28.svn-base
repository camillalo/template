/**
 * time: 2011.12.13
 * @ author llq
 */
var UI = {
    // 获取元素真实绝对位置
    getTruePos:function(el){
        var parentEl = null;
        var parentEls = el.parents();
        var elPos = el.offset();
        for(i = 0;i < parentEls.length; i++) {
            if($(parentEls[i]).css('position') == 'absolute' || $(parentEls[i]).css('position') == 'relative') {
                var parentEl = $(parentEls[i]);
                break;
            }
        }
        // 父元素存在绝对定位
        // 根据父元素确定位置
        if(parentEl !== null) {
            var pelPos = parentEl.offset();
            elPos = {'left':(elPos.left - pelPos.left), 'top':(elPos.top - pelPos.top)};
        }
        return elPos;
    },
    // 获取中间位置
    getCenterPos:function(el, size) {
        var fixTop = 60;
        var offset = el.offset();
        var size = arguments[1] ? size : {'h':el.height(), 'w':el.width()};
        return {
            'top':($(window).height() - size.h)/2 + $(window).scrollTop() - fixTop,
            'left':($(window).width() - size.w)/2 - offset.left
        };
    },
    // 申请运行一次函数
    setOnceRun:function(fn) {
        return function() {
            try {
                fn.apply(this, arguments);
            } catch(ex) {

            } finally {
                fn = null;
            }
        }
    },
    // 重新定位光标位置
    setCursor:function(iItem) {
        if($.browser.msie) {
            var range = iItem.createTextRange();
            range.collapse(false);
            range.select();
        } else {
            var ilength = $(iItem).val().length;
            $(iItem).focus();
            window.setTimeout(function() {
                iItem.setSelectionRange(ilength, ilength);
                iItem.focus();
            }, 0);
        }
    },
    /*
     * 弹出提示层
     * status 0 成功
     * status 1 错误
     * status 2 加载等待
     * status -1 隐藏
     */
    popTip:function(status, msg, flag){
        var status = arguments[0] ? status : 0;
        var msg = arguments[1] ? msg : 'ok';
        var flag = arguments[2] ? flag : false;
        var defaults = {
            'status':0, // 0 ok，1，err，2，load，-1 隐藏
            'target':'.pop-tip',
            'config':['ok', 'err', 'load']
        };
        var o = $.extend(defaults, {'status':status});
        var target = $(o.target);
        if(target.length <= 0) {
            var target = $('<div class="pop-tip"><span>&nbsp;</span></div>');
            $('body').append(target);
        }
        var psclick = !target.data('sclick') ? 0 : target.data('sclick');
        if(psclick == 1 && o.status != -1) { return false;}
        if(o.status == -1) {
            target.hide().data('sclick', 0);
            return false;
        } else {
            target.data('sclick', 1);
            target.find('span').removeClass('err load ok').addClass(o.config[o.status]).html(msg);
            var pos = UI.getCenterPos(target,{'h':target.height(),'w':target.width()});
            target.css({'left':pos.left, 'top':pos.top}).fadeIn(100);
            if(o.status < 2 && flag != true) {
                target.data('sclick', 1);
                setTimeout(function(){target.stop(true).fadeOut(function(){target.data('sclick', 0);});}, 1500);
            }

        }
    },
    /*
     * 表单默认值
     * 键盘按下消失，
     * 失去焦点内容为空时显示默认值
     */
    defaultVal:function(options) {
        var defaults = {
            'dom':document
        };
        var o = $.extend(defaults, options);
        $(":input[defaultval='true']", $(o.dom)).each(function(){
            var $this = $(this);
            $(this).keydown(function(){
                if($this.val() == $this[0].defaultValue){
                    $this.val('');
                }
            }).blur(function(){
                if($this.val() == ''){
                    $this.val($this[0].defaultValue);
                }
            });
        });
    },
    // 初始化弹出层
    _initPopWin:{
        _init:function(op){
            var defaults = {
                'target':'',// 需要弹出的弹出层
                'withMask':true,// 是否启用背景遮罩层
                'draggable':true,// 弹出层可拖动
                'open':'', // 弹出层关闭时触发事件
                'stop':'' // 弹出层关闭时触发事件
            };
            var o = $.extend(defaults, op);
            var target = $(o.target);
            // 设置遮罩层
            if(o.withMask == true) {
                var imask = $('.pop-box-mask');
                if(imask.length == 0) {
                    imask = $('<div class="pop-box-mask"></div>');
                    $('body').append(imask);
                }
            }
            // 定位弹出层
            target
                .off('click')
                .on('click', function(e){
                    e.stopPropagation();
                })
                .off('selectstart').on('selectstart', function(){
                    return false;
                })
                .css({'left':($(window).width() - target.width())/2,'top':($(window).height() - target.height())/2 - 50});

            // 关闭按钮关闭
            $('.pop-close', target).off('click').on('click', function(){
                UI._initPopWin._winHide(o);
                // 是否执行关闭事件.
            });

            // 遮罩层触发关闭
            if(o.withMask == true) {
                imask.off('click').on('click', function(){
                    UI._initPopWin._winHide(o);
                    // 是否执行关闭事件.
                });
            }
            // 绑定拖拽
            if(o.draggable == true) {
                target.drag({
                    'handle':'.pop-header',
                    'cursor':'move'
                });
            }
        },
        // 关闭弹出层
        _winHide:function(op){
            $('.pop-box-mask').length > 0 && $('.pop-box-mask').hide();
            $(op.target).hide();
            // 弹出层隐藏时触发事件
            if($.type(op.close) == 'function') {
                var closeX = UI.setOnceRun(op.close);
                closeX();
            }
        },
        _winShow:function(op){
            $('.pop-box-mask').length > 0 && $('.pop-box-mask').show();
            $(op.target).show();
            // 弹出层显示时时触发事件
            if($.type(op.open) == 'function') {
                var openX = UI.setOnceRun(op.open);
                openX();
            }
        }
    },
    /*
     * 弹出层使用
     * options不传值，初始化绑定弹出层
     * options传值，动态调用弹出层
     */
    popWin:function(options) {
        var defaults = {
            'fadeOut':false // 隐藏弹出层
        };
        if(!arguments[0]) {
            $("*[type='popwin']").each(function(){
                $(this).click(function(){
                    var defaults = {
                        'target':$(this).attr('target'),// 需要弹出的弹出层
                        'withMask':$(this).attr('withmask'),// 是否启用背景遮罩层
                        'draggable':$(this).attr('draggable'),// 弹出层可拖动
                        'open':$(this).attr('open') ,// 弹出层弹出时触发事件
                        'close':$(this).attr('close') // 弹出层关闭时触发事件
                    };
                    // init
                    UI._initPopWin._init(defaults);
                    UI._initPopWin._winShow(defaults);
                });
            });
        } else {
            var o = $.extend(defaults, options);
            if(o.fadeOut === true) {
                UI._initPopWin._winHide(options);
                return false;
            }
            UI._initPopWin._init(o);
            UI._initPopWin._winShow(options);
        }

    },

    // UI初始化
    initUI:function(options) {
        var defaults = {
            'defaultVal':'',
            'popWin':''
        };
        var o = $.extend(defaults, options);
        for(var j in o) {
            var fun = UI[j];
            o[j] ? fun(o[j]) : fun();
        }

    }

};

/*
 | -------------------------------------------------------------------------
 | jQuery绑定扩展
 | -------------------------------------------------------------------------
 */
(function($){
    $.fn.extend({
        // 拖拽元素
        drag:function(options) {
            var defaults = {
                'handle':'', // handle item
                'revert':'', // revert
                'cursor':'', // cursor
                'start':'', // start:function(){}
                'stop':'' // stop:function(){}
            };
            var o = $.extend(defaults,options);
            var els = $(this);
            // 主函数
            els.each(function(){

                // 没有设置绝对定位设置之
                if($(this).css('position') != 'absolute') $(this).css('position','absolute');

                // 定义handle
                var handle = $(this);
                if(o.handle != '' && $(o.handle).length > 0) {
                    handle = $(o.handle, handle);
                    handle.css({'cursor':o.cursor});
                }

                // 阻止事件冒泡
                $('input, select, button',$(this)).on('mousedown mouseup mousemove selectstart',function(e){
                    e.stopPropagation();
                });

                var activeEl = $(this);
                var isMove = false;

                // 按下鼠标拖拽前
                handle.on('mousedown',function(e){
                    isMove = true;
                    // 阻止事件冒泡
                    e.stopPropagation();

                    // 获取真实位置
                    movePos = UI.getTruePos(activeEl);
                    moveX = e.pageX - movePos.left;
                    moveY = e.pageY - movePos.top;

                    // 拖动开始执行的事件
                    hasStart = false;
                    if($.type(o.start) == 'function') {
                        startFun = UI.setOnceRun(o.start);
                        hasStart = true;
                    }

                });

                // 开始拖拽
                $(document).on('mousemove',function(e){
                    if(isMove) {
                        // 阻止事件冒泡
                        e.stopPropagation();
                        // 定义cursor
                        if(o.cursor != '') {
                            handle.css('cursor', o.cursor);
                        }
                        // 改变位置
                        activeEl.css({'left':(e.pageX - moveX), 'top':(e.pageY - moveY)});
                        // 执行拖拽开始回调函数
                        if(hasStart === true) {
                            startFun(e);
                        }

                        $('body').css({'-moz-user-select':'none', '-webkit-user-select':'none'});
                    }

                    // 拖拽结束
                }).on('mouseup', function(e){
                    // 阻止事件冒泡
                    e.stopPropagation();
                    // 拖动停止回到原点
                    if($.isNumeric( o.revert) &&  o.revert >= 0 ) {
                        activeEl.animate({'left':movePos.left, 'top':movePos.top},  o.revert);
                    }
                    // 注册拖动结束
                    var stopFun = o.stop;
                    if($.type(o.stop) == 'function') {
                        // 执行函数
                        stopFun(e);
                    };
                    // 标记拖拽结束
                    isMove = false;
                    $('body').css({'-moz-user-select':'auto', '-webkit-user-select':'auto'});
                });

            });
            return this;
        },
        // 弹出窗口
        popWin:function(options) {
            var defaults = {
                'target':'',// 需要弹出的弹出层
                'withMask':false,// 是否启用背景遮罩层
                'draggable':false,// 弹出层可拖动
                'open':'' ,// 弹出层弹出时触发事件
                'close':'' // 弹出层关闭时触发事件
            };
            var o = $.extend(defaults, options);
            // init
            var el = $(this);
            if(o.target != '') {
                UI._initPopWin._init(o);
                el.on('click', function(e){
                    UI._initPopWin._winShow(o);
                });
            }

            return this;
        }
    });
})(jQuery);


/**
 * 功能实现
 */
$(function(){
    // 初始化通用组件
    var initFuns = {
        'defaultVal':'',
        'popWin':''
    };
    UI.initUI(initFuns);


    var lc = $('.left');
    lc.width(lc.parent().width());
    lc.height($(window).height() - 80);
    $('#header').height(40 + parseInt($('.span9').css('margin-left')));
    $('.left-all ul li ul li a').width(lc.parent().width()-30);

});
