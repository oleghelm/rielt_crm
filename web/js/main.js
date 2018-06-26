+function ($) {

    $(document).ready(function() {
        initStartScripts();
        $('.fancybox').fancybox()
        
        $('body').on('click','.ajax-page-load',function(){
            var _this = $(this);
            var url = $(this).attr('href');
            var popup_id = '#' + ($(this).attr('data-popup-id') ? $(this).attr('data-popup-id') : "ajax-page-popup");
            console.log(popup_id);
            $.get(url,function(resp){
                $(popup_id).html(resp)
//                $.fancybox.close(close)
                $.fancybox.open( $(popup_id),{autoWidth : true});
                initStartScripts();
                if(typeof _this.data('list-update') !== 'undefined' && _this.data('list-update') != "" && $(_this.data('list-update')).length){
                    $(_this.data('list-update')).each(function(){
                        loadAjaxList($(this));
                    })
                }
            })
            return false;
        })
        $('body').on('submit',"form.ajax-page-formsubmit",function(){
            var _this = $(this);
            var url = $(this).attr('action');
            var data = $(this).serialize();
            var action = $(this).serializeArray();
            console.log(action);
            $.post(url,data,function(resp){
                $('#ajax-page-popup').html(resp);
                initStartScripts();
                if(typeof _this.data('list-update') !== 'undefined' && _this.data('list-update') != "" && $(_this.data('list-update')).length){
                    $(_this.data('list-update')).each(function(){
                        loadAjaxList($(this));
                    })
                }
            })
            return false;
        })
        $('body').on('click','.ajax-grab-ticket',function(){
            var _this = $(this);
            var url = $(this).attr('href');
            var data = $(this).data('data');
            $.post(url,data,function(resp){
                $('#ajax-page-popup').html('<h2>'+resp.message+'</h2>');
                $.fancybox.close()
                $.fancybox.open( $('#ajax-page-popup'),{autoWidth : true});
                if(typeof _this.data('list-update') !== 'undefined' && _this.data('list-update') != "" && $(_this.data('list-update')).length){
                    $(_this.data('list-update')).each(function(){
                        loadAjaxList($(this));
                    })
                }
            })
            return false;
        })
        $('body').on('click','.ajax-call-done',function(){
            var url = $(this).attr('href');
            var data = $(this).data('data');
            var _this = $(this);
            $.post(url,data,function(resp){
                if(resp.code === 1 || resp.code === '1'){
                    $('#ajax-form-popup').html('<h2>'+resp.message+'</h2>');
//                    $.fancybox.close('#ajax-form-popup')
                    $.fancybox.open( $('#ajax-form-popup'),{autoWidth : true});
                    $("."+_this.data('for_hide')).slideUp('fast');
                    $("."+_this.data('for_done')).removeClass('danger');
                    if(_this.data('reload_ajax_page')!=""){
                        $("#ajax-page-popup").load(_this.data('reload_ajax_page'))
                    }
                }
            })
            return false;
        })
        $('body').on('click',".close-popup",function(){
            $.fancybox.close()
        })
        ////ajax-list start
        $(".ajax-list.ajax-onready-load").each(function(){
            loadAjaxList($(this));
        })
        ////ajax-list end
        
        $('.dom-ria-check').change(function(){
            console.log($(this).prop('checked'))
            var res = $(this).prop('checked') ? 1 : 0;
            var url = $(this).data('url');
            $.post(url,'domria='+res,function(resp){
                console.log(resp);
            })
        })
        
        $("a.edit_old_client").click(function(){
            if($("#object_form_client").val()==="") {
                alert('Виберіть клієнта');
                return false;
            }
            var url = '/crm/clients/'+$("#object_form_client").val()+'/edit?ajax=Y';
            $.get(url,function(resp){
                $('#ajax-page-popup').html(resp)
                $.fancybox.close()
                $.fancybox.open( $('#ajax-page-popup'),{autoWidth : true});
                initStartScripts();
            })
            return false;
        })
        
        
        
        $('.js-header-search-toggle').on('click', function() {
            $('.search-bar').slideToggle();
        });
        
        
        
//        $.fn.datepicker.dates['uk'] = {
//            days: ["Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота", "Неділя"],
//            daysShort: ["Нед", "Пон", "Вів", "Сер", "Чет", "П'ят", "Суб", "Нед"],
//            daysMin: ["Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Нд"],
//            months: ["Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"],
//            monthsShort: ["Січ", "Дют", "Бер", "Кві", "Тра", "Чер", "Лип", "Сер", "Вер", "Жов", "Лис", "Гру"],
//            today: "Сьогодні"
//        };
    });

    $('body').on('click',".textCollection-wrap .removeCollectionValue",function(){
        var input = $(this).prev();
        var icon = $(this).find("i");
        input.data("old-name", input.attr('name'));
        input.attr('name',"");
        input.attr("disabled","disabled");
        $(this).removeClass('removeCollectionValue').addClass('restoreCollectionValue');
        icon.removeClass('fa-remove').addClass("fa-backward")
    })
    $('body').on('click',".textCollection-wrap .restoreCollectionValue",function(){
        var input = $(this).prev();
        var icon = $(this).find("i");
        input.attr('name',input.data("old-name"));
        input.removeAttr("disabled","disabled");
        $(this).addClass('removeCollectionValue').removeClass('restoreCollectionValue');
        icon.addClass('fa-remove').removeClass("fa-backward")
    })
    $('body').on('click',".addTextCollectionValue",function(){
        var collectionsList = jQuery('#'+$(this).data("collection-id"));//get list ID
        var newWidget = collectionsList.data('prototype'); // get element prototype
        var counter = collectionsList.data('count'); // get count of elements
        console.log($(this).data("collection-id"));
        if(typeof counter === 'string'){ // if get not int - convert
            counter = parseInt(counter);
        }
        counter++; //increment count of elments
        newWidget = newWidget.replace(/__name__/g, counter); // make code of line
        collectionsList.data('count',counter); // update counter value
        collectionsList.append(newWidget)
    })
    
    $('.showOtherFIlter').click(function(){
        var filter = $(".otherFIlter");
        var icon = $(this).find('.fa')
        if(filter.is(":visible")){
            filter.slideUp('fast');
            icon.removeClass('fa-arrow-up').addClass('fa-arrow-down')
        } else {
            filter.slideDown('fast');
            icon.removeClass('fa-arrow-down').addClass('fa-arrow-up')
        }
    })
    
    $('body').on('click','.favourite-add', function(){
        var btn = $(this);
        $.get(btn.attr('href'),function(resp){
            if(resp.status == '1'){
                btn.removeClass('btn-default').addClass('btn-warning').attr('title','Видалили з обраних');
            } else {
                btn.removeClass('btn-warning').addClass('btn-default').attr('title','Додати в обрані');
            }
        });
        return false;
    })
    $('body').on('click','.favourite-add-to-bid', function(){
        var btn = $(this);
        var bid = '';
        var url = btn.attr('href');
        if($("#check_bid_select").length && $("#check_bid_select").val()!=""){
            bid = '?bid='+$("#check_bid_select").val();
        }
        $.get(url+bid,function(resp){
            if($("#ajax-form-popup").is(':visible'))
                $.fancybox.close('#ajax-form-popup')
            $('#ajax-form-popup').html('<div style="min-width:300px;">'+resp.html.replace('#PATH#',url)+'</div>').css({'overflow': 'visible'});
            $.fancybox.open( $('#ajax-form-popup'),{autoWidth : true});
            $("#ajax-form-popup select").chosen({search_contains: true})
        });
        return false;
    })
    $('body').on('click','.favourite-del-from-bid', function(){
        var btn = $(this);
        $.get(btn.attr('href'),function(resp){
            if(resp.status === '1')
                btn.parents('tr').remove();
        })
        return false;
    })

}(jQuery);



function initStartScripts(){
//    $('.js-datepicker').datepicker({format: 'yyyy-mm-dd'});
    $('.js-datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        locale: 'uk'
    });
    $('.js-timepicker').datetimepicker({
        format: 'HH:mm',
        locale: 'uk'
    });
    $('.js-datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        locale: 'uk'
    });
    $(".xyz select").chosen({search_contains: true})
    $("#ajax-page-popup select").chosen({search_contains: true})
    if($(".textCollection").length){
        setTextCollections();
    }
}
function loadAjaxList(listWrap,src){
    if(typeof src === 'undefined'){
        src = listWrap.data('list-src');
    }
    listWrap.load(src);
}

function setTextCollections(){
    $(".textCollection").each(function(){
        if(!$(this).hasClass('textCollection-checked')){
            var block = $(this).next();
            var id = block.attr('id');
            var prototype = block.data('prototype');
            prototype = prototype.replace('form-group','form-group input-group');
            prototype = prototype.replace('</div>','<a href="javascript:void(0);" class="btn btn-warning input-group-addon removeCollectionValue"><i class="fa fa-remove"></i></a></div>');
            block.data('prototype',prototype);

            block.find(".form-group").addClass('input-group');
            block.find(".form-group input").after('<a href="javascript:void(0);" class="btn btn-warning input-group-addon removeCollectionValue"><i class="fa fa-remove"></i></a>');

            if(block.find(".form-group.input-group").length){
                var arr = block.find(".form-group.input-group").last().find('input').attr('name').split('[')
                block.data('count',arr[arr.length-1].split(']')[0]);//set count
            } else
            block.data('count',$(".textCollection").length);//set count
            

            block.after('<a href="javascript:void(0);" class="btn btn-success addTextCollectionValue" data-collection-id="'+id+'"><i class="fa fa-plus"></i></a>');

            block.addClass('textCollection-wrap');

            $(this).addClass('textCollection-checked')
        }
    })
}













////////////////////////////////////////////
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
 $("#menu-toggle-2").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled-2");
    $('#menu ul').hide();
});

 function initMenu() {
  $('#menu ul').hide();
  $('#menu ul').children('.current').parent().show();
  //$('#menu ul:first').show();
  $('#menu li a').click(
    function() {
      var checkElement = $(this).next();
      if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
        return false;
        }
      if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
        $('#menu ul:visible').slideUp('normal');
        checkElement.slideDown('normal');
        return false;
        }
      }
    );
  }
$(document).ready(function() {initMenu();});

