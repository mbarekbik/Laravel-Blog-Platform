const Toast = Swal.mixin({
    toast: true,
    position: 'bottom',
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

setInterval(() => {
    savePost(false);
}, 60000);

window.savePost = function(submit = false){
    const title = document.querySelector('input[name=title]').value;
    const excerpt = document.querySelector('textarea[name=excerpt]').value;
    const body = document.querySelector(".ql-editor").innerHTML;
    let image = document.querySelector('input[name=image]');
    const is_published = document.querySelector('input[name=is_published]').checked ? 'on' : null;
    const category = parseInt(document.querySelector('input[name=category_id]').value);
    const token = document.querySelector('input[name=_token]').value;

    const id = parseInt(document.querySelector('input[name=id_saved_post]').value);

    if(image || title !== '' || excerpt !== '' || body !== '<p><br></p>'){
        let form = new FormData();
        form.append('title', title);
        form.append('excerpt', excerpt);
        form.append('body', body);
        if (image && image.length !== 0 && !submit) {
            form.append('image', image.value);
        }
        form.append('is_published', is_published);
        form.append('category_id', category);
        form.append('_token', token);

        if(id !== 0){
            form.append('_method', 'PATCH');
        }

        fetch("/dashboard/posts-saved" + (id ? '/' + id : ''), {
            method: "POST",
            body: form,
            headers: {
                'Accept': 'application/json',
                'enctype': 'multipart/form-data',
                'X-CSRF-TOKEN': token,
            },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                Toast.fire({
                    icon: 'success',
                    title: 'Zapisano!'
                });
                if (image) {
                    document.querySelector('input[name=image]').value = null;
                }
                if(id === 0) {
                    redirectToNewUrl(data);
                }
            })
            .catch(error => {
                Toast.fire({
                    icon: 'error',
                    title: 'Niezapisano!'
                });
                console.error('Fetch Error: ', error);
            });
    }
}

function redirectToNewUrl(data) {
    document.querySelector('input[name=id_saved_post]').value = data.id;
    const newUrl = "/dashboard/posts/create?edit=" + data.id;
    history.pushState(null, null, newUrl);
}

window.addEventListener('beforeunload', function (event) {
    if (!window.submitEdit) {
        window.savePost(true);
    }
});
