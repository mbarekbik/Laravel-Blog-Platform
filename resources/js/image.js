const imageModal = document.querySelector('.image_modal');
const backgroundElement = document.querySelector('.background');
const body = document.body;
const images = [];

window.getFileInfo = function (directory, name) {
    const imagePath = `/dashboard/images/${directory}/${name}`;
    let foundImage = [];

    for (const image of images) {
        if (image.name.concat('.', image.extension) === name) {
            foundImage = image;
            break;
        }
    }

    if (foundImage.length === 0) {
        fetch(imagePath)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                images.push(data);
                drawModal(data);
            })
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
            })
            .finally(() => {
                imageModal.style.display = "flex";
                body.style.overflow = "hidden";
                backgroundElement.style.display = "block";
            });
    } else {
        drawModal(foundImage);
        imageModal.style.display = "flex";
        body.style.overflow = "hidden";
        backgroundElement.style.display = "block";
    }
}

function drawModal(data) {
    const imgElement = imageModal.querySelector('.thumbnail');
    const directoryElement = imageModal.querySelector('.directory');
    const filenameElement = imageModal.querySelector('.filename');
    const sizeElement = imageModal.querySelector('.size');
    const usageCountElement = imageModal.querySelector('.usage_count span');
    const button = imageModal.querySelector('.button') ?? null;
    const usedElement = imageModal.querySelector('.used');
    const useInfoElement = imageModal.querySelector(".use_info");

    backgroundElement.src = data.path;
    imgElement.src = data.path;
    directoryElement.innerHTML = `<i class="fa-solid fa-folder"></i> ${data.directory}`;
    filenameElement.textContent = data.name + '.' + data.extension;
    sizeElement.innerHTML = `<i class="fa-solid fa-database"></i> ${formatBytes(data.size)}`;
    usageCountElement.textContent = data.used.length;
    button ? button.dataset.name = data.name + '.' + data.extension : null;
    button ? button.dataset.directory = data.directory : null;

    usedElement.innerHTML = '';

    if (data.used.length > 0) {
        useInfoElement.style.display = 'block';
    } else {
        useInfoElement.style.display = 'none';
    }

    data.used.forEach(item => {
        const usageDiv = document.createElement('div');
        if (item.type === "Użytkownik") {
            usageDiv.classList.add('user');
            usageDiv.innerHTML = `
                    <img src="${item.thumbnail}" alt="">
                    <div class="info">
                        <div class="type">${item.type}</div>
                        <div class="location">${item.location}</div>
                        <div class="name">${item.firstname} ${item.lastname}</div>
                    </div>
                `;
        } else {
            usageDiv.classList.add('post');
            usageDiv.innerHTML = `
                    ${item.thumbnail ? `<img src="${item.thumbnail}" alt="">` : ''}
                    <div class="info">
                        <div class="type">${item.type}</div>
                        <div class="location${item.location === 'Ciało' ? ' body': ''}">${item.location}</div>
                        <div class="title">${item.title}</div>
                    </div>
                `;
        }
        usedElement.appendChild(usageDiv);
    });
}

window.closeModal = function () {
    if (imageModal.style.display === "none") {
        imageModal.style.display = "flex";
        body.style.overflow = "hidden";
        backgroundElement.style.display = "block";
    } else {
        imageModal.style.display = "none";
        body.style.overflow = "auto";
        backgroundElement.style.display = "none";
    }
}

window.deleteImage = function (event) {
    Swal.fire({
        title: "Czy jesteś pewien?",
        html: "Czy na pewno chcesz usunąć?<p style='font-size: 15px; font-weight: 400; margin-top: 5px;'>Informacja:<br>Ta akcja nie jest zalecana.</p>",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Potwiedzam, usuń!",
        cancelButtonText: "Anuluj",
    }).then((result) => {
        if (result.isConfirmed) {
            const imageName = event.target.dataset.name;
            const imageDirectory = event.target.dataset.directory;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/dashboard/images/${imageDirectory}/${imageName}`;
            form.style.display = 'none';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            const csrfTokenField = document.createElement('input');
            csrfTokenField.type = 'hidden';
            csrfTokenField.name = '_token';
            csrfTokenField.value = csrfToken;
            form.appendChild(csrfTokenField);

            document.body.appendChild(form);

            form.submit();
        }
    });
}

function formatBytes(bytes, precision = 2) {
    const kilobyte = 1024;
    const megabyte = kilobyte * 1024;

    if (bytes < kilobyte) {
        return bytes + ' <span>B</span>';
    } else if (bytes < megabyte) {
        return (bytes / kilobyte).toFixed(precision) + ' <span>KB</span>';
    } else {
        return (bytes / megabyte).toFixed(precision) + ' <span>MB</span>';
    }
}
