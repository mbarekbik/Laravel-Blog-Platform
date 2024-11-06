const title = document.querySelector("input[name=title]");
const title_length = document.querySelector("p.info_title_length");

title.addEventListener("input", (event) => {
    title_length.innerHTML =
        "Maksymalnie 255 znaków. <span class='current_title_length'>" +
        title.value.length +
        "/255</span>";
    if (title.value.length >= 255) {
        const length = document.querySelector("span.current_title_length");
        if (length.style.color !== "#eb4d4b") {
            length.style.color = "#eb4d4b";
            length.style.fontWeight = "700";
        }
    }
});

const excerpt = document.querySelector("textarea[name=excerpt]");
const excerpt_length = document.querySelector("p.excerpt_length");

excerpt.addEventListener("input", (event) => {
    excerpt_length.innerHTML =
        "Maksymalnie 510 znaków. <span class='current_excerpt_length'>" +
        excerpt.value.length +
        "/510</span>";
    if (excerpt.value.length >= 510) {
        const length = document.querySelector("span.current_excerpt_length");
        if (length.style.color !== "#eb4d4b") {
            length.style.color = "#eb4d4b";
            length.style.fontWeight = "700";
        }
    }
});

var toolbarOptions = [
    ["bold", "italic", "underline", "strike"],
    ["blockquote", "code-block"],
    ["image"],
    [
        { align: "" },
        { align: "center" },
        { align: "right" },
        { align: "justify" },
    ],
    [{ size: ["small", false, "large", "huge"] }],
];

window.quill = new Quill("#editor", {
    modules: {
        toolbar: toolbarOptions,
    },
    theme: "snow",
});

let hiddenArea = document.getElementById('hiddenArea');

quill.setContents(quill.clipboard.convert(hiddenArea.value));

const selectImageModal = document.querySelector(".select-image");
const browseImages = document.querySelector(".browse-images");
const optionsImageModal = selectImageModal.querySelectorAll(".option");
const imageInput = document.querySelector("input[name=image]");
let range = null;
let imageLoadMode = null;

quill.getModule("toolbar").addHandler("image", () => {
    range = quill.getSelection();
    imageLoadMode = 'body';
    showSelectImageModal();
});

window.showSelectImageModal = function () {
    document.body.style.overflow = "hidden";
    selectImageModal.style.display = "flex";
}

let offset = 0;
let isLoaded = false;
let allImagesLoaded = false;
const options = {
    root: null,
    rootMargin: '0px',
    threshold: 1,
};
const observer = new IntersectionObserver(callback, options);
const elementToObserve = document.querySelector(".load-images");

function callback(entries, observer) {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            if (isLoaded) {
                loadImages();
                isLoaded = false;
            }
        }
    });
}

window.selectFromStorage = function () {
    optionsImageModal.forEach(option => {
       option.style.display = "none";
    });
    browseImages.style.display = "block";

    if (offset % 20 === 0) {
        checkVisibility();
    }
}

const loading = document.querySelector(".loading");
let loadingElement = document.querySelector(".loading");
let parentElement = loadingElement.parentNode;
observer.observe(elementToObserve);

async function loadImages() {
    loading.classList.remove('hidden');
    const url = `/api/images?offset=${offset}`;
    await fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(async data => {
            for (const image of data) {
                addImageThumbnail(image.path);
            }
            offset += data.length;
            if (data.length < 20) {
                allImagesLoaded = true;
                observer.disconnect();
                elementToObserve.remove();
            }
            await checkVisibility();
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        })
        .finally(() => {
            loading.classList.add('hidden');
            isLoaded = true;
        });
}

function addImageThumbnail(url) {
    let imageElement = document.createElement("img");
    imageElement.src = url;
    imageElement.dataset.path = url;
    imageElement.addEventListener('click', function (event){
        if (imageLoadMode === 'thumbnail') {
            imageInput.value = event.target.dataset.path;
            document.getElementById("output").src = imageElement.src;
            savePost(false);
        } else if (imageLoadMode === 'body') {
            insertToEditor(event.target.dataset.path, quill);
        }
        hideImageModal();
    });
    parentElement.insertBefore(imageElement, loadingElement);
}

function checkVisibility() {
    const elementToObserve = document.querySelector(".load-images");

    if (elementToObserve) {
        const rect = elementToObserve.getBoundingClientRect();
        const isVisible = (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );

        if (isVisible) {
            loadImages();
            isLoaded = false;
        }
    }
}

window.hideBrowseImages = function () {
    browseImages.style.display = "none";
    optionsImageModal.forEach(option => {
        option.style.display = "flex";
    });
}

function hideImageModal() {
    document.body.style.overflow = "auto";
    selectImageModal.style.display = "none";
    hideBrowseImages();
}

selectImageModal.addEventListener("click", function (event) {
   if (event.target.classList.contains('select-image')) {
       hideImageModal();
   }
});

window.selectLocalImage = function () {
    const input = document.createElement("input");
    input.setAttribute("type", "file");
    input.setAttribute("accept", "image/*");
    input.click();

    if (imageLoadMode === 'body') {
        input.onchange = async () => {
            const file = input.files[0];

            if (/^image\//.test(file.type)) {
                let url = await imageHandler(file);
                if (url) {
                    insertToEditor(url, quill);
                }
            } else {
                console.warn("You could only upload images.");
            }

            hideImageModal();
        };
        input.oncancel = () => {
            hideImageModal();
        }
    } else if (imageLoadMode === 'thumbnail') {
        input.onchange = async () => {
            const file = input.files[0];

            if (/^image\//.test(file.type)) {
                let url = await imageHandler(file);
                output.src = url;
                imageInput.value = url;
                window.savePost(false);
            } else {
                console.warn("You could only upload images.");
            }

            hideImageModal();
        };
        input.oncancel = () => {
            hideImageModal();
        }
    }
};

async function imageHandler(image) {
    const existingUl = document.querySelector('.post_container ul');
    if (image.size > 10 * 1024 * 1024) {
        if (!existingUl) {
            const ulElement = document.createElement('ul');
            const liElement = document.createElement('li');

            liElement.textContent = 'Obraz jest za duży. Maksymalny rozmiar pliku to 10MB!';
            ulElement.appendChild(liElement);

            const postContainer = document.querySelector('.post_container');
            postContainer.insertBefore(ulElement, postContainer.firstChild);
        }
        return null;
    } else {
        if (existingUl) {
            existingUl.remove();
        }
    }

    const token = document.querySelector('input[name=_token]').value;
    let formData = new FormData();
    formData.append("image", image);
    formData.append("_token", token);

    let url = document.getElementById("content").dataset.imageUrl;

    try {
        const response = await fetch(url, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": token,
            },
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();
        if (data.url) {
            if (offset > 0 && offset % 20 !== 0 || allImagesLoaded) {
                addImageThumbnail(data.url);
            }
            return data.url;
        }
    } catch (error) {
        console.error("Error:", error);
    }

    return null;
}

window.calculateReadTime = function () {
    const body = document.querySelector(".ql-editor").innerHTML;
    const token = document.querySelector('input[name=_token]').value;

    const formData = new FormData();
    formData.append('body', body);
    formData.append('_token', token);

    fetch("/dashboard/calculate-read-time", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": token,
        },
    })
        .then(response => response.json())
        .then(data => {
            document.querySelector('.reading-time').innerHTML = data + " min";
        })
        .catch(error => {
            console.error("Error:", error);
            Toast.fire({
                icon: 'error',
                title: 'Błąd!'
            });
        });
};

window.insertToEditor = function (url, editor) {
    editor.insertEmbed(range.index, "image", url);
};

window.submitEdit = false;

window.submitForm = function () {
    window.submitEdit = true;
    let hiddenArea = document.getElementById("hiddenArea");
    let qlEditor = document.querySelector(".ql-editor");

    hiddenArea.value = qlEditor.innerHTML;

    document.getElementById("form").submit();
};

document.querySelectorAll(".ql-picker").forEach((tool) => {
    tool.addEventListener("mousedown", function (event) {
        event.preventDefault();
        event.stopPropagation();
    });
});

var change_image = document.querySelector(".change_image");

change_image.addEventListener("click", function () {
    imageLoadMode = 'thumbnail';
    showSelectImageModal();
});

window.changeToCategory = function (event, id) {
    const selectedCategory = document.querySelector(".category-selected");
    const sourceStyles = window.getComputedStyle(event.target);
    const categoryInput = document.querySelector("input[name=category_id]");

    selectedCategory.style.setProperty('background-color', sourceStyles.backgroundColor);
    selectedCategory.style.setProperty('color', sourceStyles.color);
    selectedCategory.innerHTML = event.target.innerHTML;
    selectedCategory.style.border = "none";
    categoryInput.value = event.target.dataset.id;
}

let visibleCategories = true
window.categoriesToggle = function () {
    const categories = document.querySelector(".categories_list");
    const toggleButton = document.querySelector(".categories_extend");

    if (visibleCategories) {
        visibleCategories = false;
        toggleButton.innerHTML = 'Rozwiń <i class="fa-solid fa-chevron-down"></i>';
    } else {
        visibleCategories = true;
        toggleButton.innerHTML = 'Ukryj <i class="fa-solid fa-chevron-up"></i>';
    }

    categories.classList.toggle('active');
}

// window.scrollTo(0, 0);
