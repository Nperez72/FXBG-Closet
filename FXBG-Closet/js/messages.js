function sendDelete(id) {
    sendAJAXRequest('deleteNotification.php?id=' + id, {}, deleteCallback);
}

function deleteCallback() {
    let response = JSON.parse(this.responseText);
    if (response.result) {
        // Remove the row from the table without refreshing the page
        $('tr[data-message-id="' + this.responseText + '"]').remove();
    } else {
        alert('Failed to delete the notification.');
    }
}

function sendAJAXRequest(url, requestData, onSuccess, onFailure) {
    var request = new XMLHttpRequest();
    request.open("POST", url, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onload = onSuccess;
    request.onerror = onFailure;
    request.send(JSON.stringify(requestData));
    return false;
}

$(function() {
    $('.delete-button').click(function(e) {
        e.preventDefault();

        let id = $(this).data('message-id');
        sendDelete(id); 
    });

    $('tr.message').click(function() {
        let id = $(this).data('message-id');
        window.location = 'viewNotification.php?id=' + id;
    });
});
