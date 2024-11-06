import './bootstrap';
import.meta.glob([ '../images/**', ]);
import './theme';

window.confirmDelete = function(id, name){
    Swal.fire({
        title: 'Czy jesteś pewien?',
        text: "Nie będziesz mógł tego przywrócić!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Tak, usuń!',
        cancelButtonText: 'Anuluj'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(name + '_' + id).submit();
            }
        })
}

window.cannot = function(message){
    Swal.fire(
        'Błąd',
        message,
        'error'
    )
}
