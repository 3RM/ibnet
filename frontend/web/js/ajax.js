function handleAjaxForm(e) {
 
    var
        $link = $(e.target),
        data = $(this).data('yiiActiveForm'),
        callUrl = $link.attr('action');
        formId = $link.attr('id'),
        onDone = $link.data('onDone'),
        postData = (typeof formId === "string" ? $('#' + formId).serializeArray() : null);

    if (data.validated) {
 
        ajaxRequest = $.ajax({
            type: "post",
            dataType: 'json',
            url: callUrl,
            data: postData
        });
        e.preventDefault();
        // e.stopImmediatePropagation();
        
        // Assign done handler
        if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
            ajaxRequest.done(ajaxCallbacks[onDone]);
        } 
    }
}

function handleLoginForm(e) {

    var
        $link = $(e.target),
        callUrl = $link.attr('action');
        onDone = $link.data('onDone'),
        postData = { _csrf: $('_csrf').val(), username: $('#username').val(), password: $('#password').val() };
 
    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: postData
    });
    e.preventDefault();
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    } 
}

function handleAjaxLink(e) {
  
    var
        $link = $(e.target),
        callUrl = $link.attr('href'),
        formId = $link.data('formId'),
        onDone = $link.data('onDone');

    //$("#show_msg").html("Loading.... please wait...");
 
    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
    });
    e.preventDefault();
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    } 
}

function handleAjaxSpanLink(e) {
  
    var
        $link = $(e.target),
        callUrl = $link[0].parentNode.href,
        formId = $link.data('formId'),
        onDone = $link[0].parentNode.dataset.onDone;

    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
    });
    e.preventDefault();
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    } 
}

var ajaxCallbacks = {

    'loginDone': function (response) {
        $('#login-result').html(response.body);
        if (response.success == true) {
            window.location.reload();
        }
    },
    'likeDone': function (response) {
        $('#like-result').html(response.body);
    },
    'nextDone': function (response) {
        $('#box3Content').html(response.body);
    },
    'emailDone': function (response) {
        $('#email-result').html(response.body);
        if (response.success == true) {
            window.location.reload();
        }
    },
    'visibleDone': function (response) {
        $('#visible-result-'+response.updateId).html(response.body);
    },
    'viewedDone': function (response) {
        document.getElementById('video-container').style.display = 'none';
    },
    'tagDone': function (response) {
        document.getElementById('tag-'+response.tid).style.display = 'none';
        // can also try .empty() https://api.jquery.com/empty/
    },
    'requestDone': function (response) {
        $('#item-'+response.requestId).parent().toggle();
    },
    'updateDone': function (response) {
        $('#update-'+response.updateId).toggle();
    },
    'placeAddDone': function (response) {
        if (response.success == true) {
            var pid = response.pid, 
                place = response.place;
            $('#places').val('');
            if (!document.getElementById('placeList')) {
                 $('#placeListContainer').append('<div id="placeList" class="item-list"><div id="place-'+pid+'" class="item-row place-row">'+place+'<a id="placeitem-'+pid+'" href="/ajax/delete-network-place?pid='+pid+'" data-on-done="placeDeleteDone"><span class="glyphicon glyphicon-remove"></span></a><br></div></div>');
            } else {
                $('#placeList').append('<div id="place-'+pid+'" class="item-row place-row">'+place+'<a id="placeitem-'+pid+'" href="/ajax/delete-network-place?pid='+pid+'" data-on-done="placeDeleteDone"><span class="glyphicon glyphicon-remove"></span></a><br></div>');
            }
            $(document).on("click",'#placeitem-'+pid, handleAjaxSpanLink);
        }
    },
    'placeDeleteDone': function (response) {
        document.getElementById('place-'+response.pid).remove();
        if (!$('.place-row').length) {
            document.getElementById('placeList').remove();
        }
    },
    'keywordAddDone': function (response) {
        if (response.success == true) {
            var kid = response.kid, 
                kw = response.keyword;
            $('#keywords').val('');
            if (!document.getElementById('keywordList')) {
                 $('#keywordListContainer').append('<div id="keywordList" class="item-list"><div id="keyword-'+kid+'" class="item-row keyword-row">'+kw+'<a id="keyworditem-'+kid+'" href="/ajax/delete-network-keyword?kid='+kid+'" data-on-done="keywordDeleteDone"><span class="glyphicon glyphicon-remove"></span></a><br></div></div>');
            } else {
                $('#keywordList').append('<div id="keyword-'+kid+'" class="item-row keyword-row">'+kw+'<a id="keyworditem-'+kid+'" href="/ajax/delete-network-keyword?kid='+kid+'" data-on-done="keywordDeleteDone"><span class="glyphicon glyphicon-remove"></span></a><br></div>');
            }
            $(document).on("click",'#keyworditem-'+kid, handleAjaxSpanLink);
        }    
    },
    'keywordDeleteDone': function (response) {
        document.getElementById('keyword-'+response.kid).remove();
        if (!$('.keyword-row').length) {
            document.getElementById('keywordList').remove();
        }
    },
}