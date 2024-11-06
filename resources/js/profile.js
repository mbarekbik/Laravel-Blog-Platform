const modal = document.querySelector('.modal');
const profileModal = modal.querySelector(".modal-profile");
const notificationModal = modal.querySelector(".modal-notifications");
const isZero = parseInt(document.querySelector(".notifications_count").innerHTML) === 0;
const isEmpty = notificationModal.querySelector(".empty") !== null;
let sentReadNotifications = isZero || isEmpty;

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal')) {
        if (modal.style.display === 'flex') {
            modal.style.display = 'none';
            document.body.style.overflow = "auto";
        }
    }
});

document.querySelector('.profile').addEventListener('click', function (event) {
    modal.style.display = 'flex';
    document.body.style.overflow = "hidden";
    if (event.target.classList.contains('notifications_count')){
        profileModal.classList.add("hidden");
        notificationModal.classList.remove("hidden");
        readNotifications();
    } else {
        profileModal.classList.remove("hidden");
        notificationModal.classList.add("hidden");
    }
});

document.querySelector('.close.close-modal').addEventListener('click', function () {
    modal.style.display = 'none';
    document.body.style.overflow = "auto";
});

document.querySelector('.modal-notifications .back').addEventListener('click', function () {
    profileModal.classList.remove("hidden");
    notificationModal.classList.add("hidden");
});

document.querySelector('.notifications').addEventListener('click', function () {
    profileModal.classList.add("hidden");
    notificationModal.classList.remove("hidden");
    readNotifications();
});

const date = Date.now();

window.readNotifications = function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!sentReadNotifications) {
        fetch('/read-notifications', {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                'date': date,
            }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                if (response.status === 200) {
                    sentReadNotifications = true;
                    document.querySelector(".notifications_count").innerHTML = "0";
                    document.querySelector(".modal-profile .notifications").innerHTML = "0 <i class=\"fa-solid fa-angles-right\"></i>";
                }
            })
            .catch(error => {
                console.error('Wystąpił błąd podczas przetwarzania zapytania:', error);
            });
    }
}

window.clearNotifications = function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const notifications = notificationModal.querySelectorAll(".notification");
    const dateElements = document.querySelectorAll(".modal-notifications .date");
    fetch('/clear-notifications', {
        method: 'delete',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            if (response.status === 200) {
                for (const notification of notifications) {
                    notification.remove();
                }
                for (const dateElement of dateElements) {
                    dateElement.remove();
                }
                const notificationDiv = document.createElement('div');
                notificationDiv.classList.add('notification', 'action');

                const paragraph = document.createElement('p');
                paragraph.classList.add('empty');
                paragraph.textContent = 'Brak powiadomień';
                notificationDiv.appendChild(paragraph);
                notificationModal.appendChild(notificationDiv);
            }
        })
        .catch(error => {
            console.error('Wystąpił błąd podczas przetwarzania zapytania:', error);
        });
}

startTime();
function startTime() {
    const today = new Date();
    let h = today.getHours();
    let m = today.getMinutes();
    let s = today.getSeconds();
    m = checkTime(m);
    h = checkTime(h);
    document.getElementById('hours').innerHTML = h;
    document.getElementById('minutes').innerHTML = m;
    setTimeout(startTime, 1000);
}

function checkTime(i) {
    if (i < 10) {i = "0" + i}
    return i;
}
