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

    // $("#show-result").html('<div class="loader"></div>');
 
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

    'viewUserDone': function (response) {
        $.get('/accounts/view-detail', {id:response.uid}, function(data) {
            $('#user-detail-modal').modal('show').find('#user-detail-content').html(data);
        });
    },
    'viewProfileDone': function (response) {
        $.get('/directory/view-detail', {id:response.pid}, function(data) {
            $('#profile-detail-modal').modal('show').find('#profile-detail-content').html(data);
        });
    },
}