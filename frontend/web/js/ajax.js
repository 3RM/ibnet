function handleAjaxForm(e) {
 
    e.preventDefault();
 
    var
        $link = $(e.target),
        callUrl = $link.attr('action');
        formId = $link.data('formId'),
        onDone = $link.data('onDone'),
        onFail = $link.data('onFail'),
        onAlways = $link.data('onAlways'),
        postData = { _csrf: $('_csrf').val(), username: $('#username').val(), password: $('#password').val() };
 
    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: postData
    });
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    }
 
    // Assign fail handler
    if (typeof onFail === "string" && ajaxCallbacks.hasOwnProperty(onFail)) {
        ajaxRequest.fail(ajaxCallbacks[onFail]);
    }
 
    // Assign always handler
    if (typeof onAlways === "string" && ajaxCallbacks.hasOwnProperty(onAlways)) {
        ajaxRequest.always(ajaxCallbacks[onAlways]);
    }
 
}

function handleAjaxLink(e) {
 
    e.preventDefault();
 
    var
        $link = $(e.target),
        callUrl = $link.attr('href'),
        formId = $link.data('formId'),
        onDone = $link.data('onDone'),
        onFail = $link.data('onFail'),
        onAlways = $link.data('onAlways'),
        ajaxRequest;

    //$("#show_msg").html("Loading.... please wait...");
 
    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
    });
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    }
 
    // Assign fail handler
    if (typeof onFail === "string" && ajaxCallbacks.hasOwnProperty(onFail)) {
        ajaxRequest.fail(ajaxCallbacks[onFail]);
    }
 
    // Assign always handler
    if (typeof onAlways === "string" && ajaxCallbacks.hasOwnProperty(onAlways)) {
        ajaxRequest.always(ajaxCallbacks[onAlways]);
    }
 
}

function handleAjaxSpanLink(e) {
 
    e.preventDefault();
 
    var
        $link = $(e.target),
        callUrl = $link[0].parentNode.href,
        formId = $link.data('formId'),
        onDone = $link[0].parentNode.dataset.onDone,
        onFail = $link.data('onFail'),
        onAlways = $link.data('onAlways'),
        ajaxRequest;

    ajaxRequest = $.ajax({
        type: "post",
        dataType: 'json',
        url: callUrl,
        data: (typeof formId === "string" ? $('#' + formId).serializeArray() : null)
    });
 
    // Assign done handler
    if (typeof onDone === "string" && ajaxCallbacks.hasOwnProperty(onDone)) {
        ajaxRequest.done(ajaxCallbacks[onDone]);
    }
 
    // Assign fail handler
    if (typeof onFail === "string" && ajaxCallbacks.hasOwnProperty(onFail)) {
        ajaxRequest.fail(ajaxCallbacks[onFail]);
    }
 
    // Assign always handler
    if (typeof onAlways === "string" && ajaxCallbacks.hasOwnProperty(onAlways)) {
        ajaxRequest.always(ajaxCallbacks[onAlways]);
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
}