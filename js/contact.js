$('#contact_form').submit(function(e){
    e.preventDefault();
    $.post('/ajax/contact', $(this).serialize(), function(a){
        if (a.result) {
            $("#contact_send").show('slow');
            $('#contact_form').hide('slow');
        } else {
            alert(a.error);
        }
    }, 'json');
    return false;
});
