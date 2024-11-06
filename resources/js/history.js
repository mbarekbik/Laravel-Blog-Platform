const Toast = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

let pathString = window.location.pathname;
let pathSegments = pathString.split('/');
let currentSegment = pathSegments[pathSegments.length - 2];
let currentPostId = pathSegments[pathSegments.length - 5];

let last_id = parseInt(currentSegment === 'current' ? 0 : currentSegment);
let remembered_post_id = currentPostId;

window.show = function (id = null, history_id, compare = false) {
    if (id) {
        remembered_post_id = id;
    } else {
        id = remembered_post_id;
    }

    const url = "/dashboard/posts/history/" + id + '/' + history_id;
    let postElementId = '#first_post';

    history_id = history_id === 'current' ? 0 : history_id;

    if (last_id === history_id) {
        return;
    }

    if (compare) {
        postElementId = '#second_post';
        document.querySelectorAll(".post__preview").forEach((el) => {
            el.classList.add('post__compare');
        });
        document.querySelector(postElementId).style.display = 'block';
        document.querySelector(".compact-history-list").style.display = 'none';
        document.querySelector("header.header_post_edit .leave-compare").style.display = "block";
    }

    const loading = document.querySelector(".loading");
    const post_preview = document.querySelector(".post__preview" + postElementId);

    loading.classList.remove('hidden');
    post_preview.style.overflow = 'hidden';

    fetch(url)
        .then(response => {
            if (!response.ok) {
                Toast.fire({
                    icon: 'error',
                    title: 'Nie można wczytać!'
                })
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const category = document.querySelector(".post__preview" + postElementId + " .post_container .info .category");
            document.querySelector(postElementId + " .preview_title").innerHTML = data.title;
            category.innerHTML = data.category.name;
            category.style.background = data.category.backgroundColor + 'CC';
            category.style.color = data.category.textColor;

            const postInfo = document.querySelector(".post__preview" + postElementId + " .post_container .top .info");
            const readTime = postInfo.querySelector(".reading-info .reading-time");
            const readInfo = postInfo.querySelector(".reading-info");

            if (data.read_time === null) {
                if (readInfo) {
                    readInfo.innerHTML = "";
                }
            }

            if (readTime) {
                readTime.innerHTML = data.read_time + " min";
            } else {
                const readTimeTextElement = document.createElement('p');
                readTimeTextElement.className = 'reading-text';
                readTimeTextElement.textContent = 'Czas czytania: ';

                const watchIcon = document.createElement('i');
                watchIcon.className = 'fa-solid fa-clock';

                const readTimeElement = document.createElement('p');
                readTimeElement.className = 'reading-time';
                readTimeElement.textContent = data.read_time + ' min';

                postInfo.querySelector(".reading-info").appendChild(readTimeTextElement, watchIcon, readTimeElement);
            }

            const date = new Date(data.created_at);
            const formattedDate = date.toLocaleDateString("pl-PL", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
            });

            const author = document.querySelector(".post__preview" + postElementId + " .post_container .info .date").innerHTML.slice(10);
            document.querySelector(".post__preview" + postElementId + " .post_container .info .date").innerHTML = formattedDate + author;

            const body =
                '<div class="actions"><a><i class="fa-solid fa-arrow-left"></i> Powrót do strony głównej</a><a>Następny post <i class="fa-solid fa-arrow-right"></i></a></div>';

            document.querySelector(postElementId + " .post_body").innerHTML = data.body + body;

            const output = document.querySelector(postElementId + " .output");
            output.src = data.image_path;

            document.querySelector(postElementId + " .excerpt").innerHTML = data.excerpt;

            const visibility = document.querySelector(postElementId + " input[name=is_published]");

            visibility.checked = data.is_published === 1;

            if (last_id !== history_id && !compare) {
                document.querySelector(".history_card.h_" + history_id).classList.add("active");
                document.querySelector(".history_card.h_" + last_id).classList.remove("active");

                const actions = document.querySelector(".history_card.h_" + history_id + " .actions");

                if (history_id !== 0) {
                    if (actions) {
                        actions.classList.remove('hidden');
                    }
                }

                const lastActions = document.querySelector(".history_card.h_" + last_id + " .actions");

                if (lastActions) {
                    lastActions.classList.add('hidden');
                }

                const compare = document.querySelector(".history_card.h_" + history_id + " .compare");

                compare.classList.add('hidden');

                const lastCompare = document.querySelector(".history_card.h_" + last_id + " .compare");

                if (lastCompare) {
                    lastCompare.classList.remove('hidden');
                }

                const history_id_string = history_id === 0 ? 'current' : history_id;
                const newUrl = "/dashboard/posts/" + id + "/edit/history/" + history_id_string + "/show";
                history.pushState(null, null, newUrl);
            }
            if (!compare) {
                last_id = history_id;
            }
        })
        .finally(() => {
            loading.classList.add('hidden');
            post_preview.style.overflow = 'auto';
        })
        .catch(error => {
            Toast.fire({
                icon: 'error',
                title: 'Nie można wczytać!'
            })
            console.error('There has been a problem with your fetch operation:', error);
        });
};

const firstPost = document.getElementById('first_post');
const secondPost = document.getElementById('second_post');

window.compare = function (event, id) {
    event.stopPropagation();
    const windowWidth = window.innerWidth;
    if (windowWidth > 840) {
    } else {
        firstPost.style.display = "none";
        document.querySelector(".switch-compare").style.display = 'block';
    }
    show(null, id, true);
};

window.leaveCompare = function () {
    document.querySelector("#second_post").style.display = "none";
    document.querySelectorAll(".post__preview").forEach((el) => {
       el.classList.remove('post__compare');
    });
    document.querySelector(".compact-history-list").style.display = "block";
    document.querySelector(".leave-compare").style.display = "none";
}

firstPost.addEventListener('scroll', function() {
    secondPost.scrollTop = firstPost.scrollTop;
});

secondPost.addEventListener('scroll', function() {
    firstPost.scrollTop = secondPost.scrollTop;
});

let show_first_post = false;
window.switchShowCompare = function () {
    if (show_first_post) {
        firstPost.style.display = 'none';
        secondPost.style.display = 'flex';
    } else {
        secondPost.style.display = 'none';
        firstPost.style.display = 'flex';
    }
    show_first_post = !show_first_post;
};

window.revert = function (postId, historyId) {
    Swal.fire({
        title: "Czy jesteś pewien?",
        html: "Czy na pewno chcesz przywrócić?<p style='font-size: 15px; font-weight: 400; margin-top: 5px;'>Informacja:<br>Po przywróceniu post zostanie zaktualizowany, będzie można dodatkowo edytować.</p>",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Tak, przywróć!",
        cancelButtonText: "Anuluj",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                "/dashboard/posts/history/" +
                postId +
                "/" +
                historyId +
                "/revert";
        }
    });
};

window.extend_history = function () {
    const history_list = document.getElementById('history_list');
    const extend_history = document.querySelector('.extend-history');

    if (history_list.style.height === '0px') {
        history_list.style.height = 'auto';
        history_list.style.visibility = 'visible';
        extend_history.innerHTML = "Ukryj kompaktową historię";
    } else {
        history_list.style.height = '0px';
        history_list.style.visibility = 'hidden';
        extend_history.innerHTML = "Pokaż kompaktową historię";
    }

}

function convertDateTime(date) {
    var tzoffset = new Date().getTimezoneOffset() * 60000;

    date = new Date(Date.parse(date) - tzoffset)
        .toISOString()
        .slice(0, -1)
        .replace("T", " ");
    date = date.slice(0, -7);

    return date;
}
