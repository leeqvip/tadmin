function autoLeftNav() {
    if ($(window).width() < 1024) {
        $('.left-sidebar').addClass('active');
    } else {
        $('.left-sidebar').removeClass('active');
    }
}

function autoActiveNav() {
    $(".sidebar-nav .sidebar-nav-link").each(function(index, el) {
        var currentUrl = location.href;
        var navUrl = $(this).children('a').attr('href');
        if (currentUrl.indexOf(navUrl) > 0) {
            $(this).children('a').addClass('sub-active').parents('.sidebar-nav-sub').show('400', function() {}).prev('.sidebar-nav-sub-title').addClass('active')
        }
    });
}

function autoResize() {
    var minHeight = 0;
    $(".content-wrapper").children().each(function(index, el) {
        minHeight += parseInt($(this).outerHeight(true));
    });
    var needHeight = $(this).height() - $(".admin-header").height();
    minHeight = Math.max(minHeight, needHeight)
    $(".content-wrapper").css('min-height', parseInt(minHeight));
}

function renderWangEditor(obj){
    obj.hide();
    $div = $('<div></div>');
    obj.before($div);
    var E = window.wangEditor
    var editor = new E($div[0]);
    // editor.customConfig.uploadImgShowBase64 = true;
    editor.customConfig.uploadImgServer = '/tadmin/upload/image'    
    
    editor.customConfig.onchange = function(html) {
        obj.val(html)
    }
    editor.create()
    editor.txt.html(obj.val())
}

function bootWangEditor(obj){
    if (!window.wangEditor) {
        $.ajax({
            url: adminApp.path.assets + '/editor/wangEditor/wangEditor.min.js',
            dataType: 'script'
        }).done(function() {
            renderWangEditor(obj)
        });
    }else{
        renderWangEditor(obj)
    }    
}

function bootUEditor(obj){
    if (!window.uEditor) {
        window.UEDITOR_HOME_URL = adminApp.path.assets + '/editor/ueditor/'
        window.ueControllerPath = '/tadmin/upload/ueditor'
        $.ajax({
            url: window.UEDITOR_HOME_URL + 'ueditor.config.js',            
            dataType: 'script',            
        })
        .done(function() {
            $.ajax({
                url: window.UEDITOR_HOME_URL + 'ueditor.all.min.js',            
                dataType: 'script',            
            })
            .done(function(data) {                
                $.ajax({
                    url: window.UEDITOR_HOME_URL + 'lang/zh-cn/zh-cn.js',            
                    dataType: 'script',            
                })
                .done(function() {
                    var ue = UE.getEditor(obj.attr('id'));
                });
            });
        });
        
    }
}

function adminInit() {
    autoResize();
    autoLeftNav();
}
$(document).ready(function() {
    // 风格切换
    $('.tpl-skiner-toggle').on('click', function() {
        $('.tpl-skiner').toggleClass('active');
    })
    $('.tpl-skiner-content-bar').find('span').on('click', function() {
        $('body').attr('class', $(this).attr('data-color'))
        saveSelectColor.Color = $(this).attr('data-color');
        // 保存选择项
        storageSave(saveSelectColor);
    })
    // 侧边菜单开关
    $('.tpl-header-switch-button').on('click', function() {
        if ($('.left-sidebar').is('.active')) {
            if ($(window).width() > 1024) {
                $('.tpl-content-wrapper').removeClass('active');
            }
            $('.left-sidebar').removeClass('active');
        } else {
            $('.left-sidebar').addClass('active');
            if ($(window).width() > 1024) {
                $('.tpl-content-wrapper').addClass('active');
            }
        }
    });
    // 侧边菜单
    $('.sidebar-nav-sub-title').on('click', function() {
        $(this).siblings('.sidebar-nav-sub').slideToggle(80)
            .end()
            .find('.sidebar-nav-sub-ico').toggleClass('sidebar-nav-sub-ico-rotate');
    })
    $('.datetimepicker-btn').datetimepicker();
    $('table.am-text-middle').find('td').addClass('am-text-middle');
    $('.error-nopic').error(function() {
        $(this).attr('src', adminApp.path.assets + '/img/nopic.png');
    });
    $('.form-img').on('change', function() {
        var fileNames = '';
        $.each(this.files, function() {
            fileNames += '<span class="am-badge">' + this.name + '</span> ';
        });
        $(this).parents(".am-form-file").next('.file-list').html(fileNames);
    });
    $(".show-status").each(function(index, el) {
        var showStatus = $(this).attr('data-status')
        if (showStatus == 1) {
            $(this).addClass('am-text-success').append(' 正常');
        } else {
            $(this).append(' 隐藏');
        }
    });
    $('.form-editor').each(function(index, el) {
        var dataEditor = $(this).attr('data-editor');
        if(dataEditor == 'ueditor'){
            bootUEditor($(this))
        }else{
            bootWangEditor($(this))
        }
    });
});
$(window).resize(function() {
    adminInit();
});
adminInit();
autoActiveNav();
setInterval(function() {
    adminInit();
}, 500)