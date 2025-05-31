// @msg = 'message to show', @type = 'must be either success(1) or failed(0)'
function showToastMessage(msg, type) {

    // create Toast class object
    var Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var icon = 'success';

    if (type === 0) {
        icon = 'error';
    }

    Toast.fire({
        icon: icon,
        title: msg
    });
}