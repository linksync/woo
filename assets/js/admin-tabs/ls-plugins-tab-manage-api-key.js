function openAudit(id) {
    jQuery('#pop_up_' + id).fadeIn(0500);
}

function closeAudit(id) {
    jQuery('#pop_up_' + id).fadeOut();
}

function openAuditdel(id) {
    jQuery('#pop_up_del_' + id).fadeIn(0500);
}
function closeAuditdel(id) {
    jQuery('#pop_up_del_' + id).fadeOut();
}

function checkEmptyLaidKey() {
    var laidField = jQuery("input[name='apikey']");
    if (laidField.val() == '') {
        laidField.css('border', '1px solid red');
        return false;
    } else {
        return true;
    }
}