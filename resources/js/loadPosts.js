const host = window.location.host;
const protocol = window.location.protocol;
let offset = 20;
let isLoaded = true;

function callback(entries, observer) {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            if (isLoaded) {
                loadPosts();
                isLoaded = false;
            }
        }
    });
}

const options = {
    root: null,
    rootMargin: '0px',
    threshold: 1,
};

const observer = new IntersectionObserver(callback, options);
const elementToObserve = document.querySelector(".load-posts");
const loading = document.querySelector(".loading");
observer.observe(elementToObserve);

function loadPosts () {
    loading.classList.remove('hidden');
    fetch(`/api/posts?offset=${offset}`, {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            loading.classList.add('hidden');
            addPostTiles(data);
            offset += data.length;
            if (data.length < 20) {
                observer.disconnect();
                elementToObserve.remove();
            }
        })
        .catch(error => {
            console.error('Fetch Error: ', error.message);
        });
}

function addPostTiles(data) {
    data.forEach(post => {
        const isHighlighted = parseInt(post.is_highlighted) === 1;

        const link = document.createElement("a");
        link.href = `${protocol}//${host}/post/${post.slug}`;
        link.className = "read_post";

        const postContainer = document.createElement("div");
        postContainer.className = "post";

        if (isHighlighted) {
            const highlightedIcon = document.createElement("div");
            highlightedIcon.className = "highlighted_icon";
            highlightedIcon.textContent = "Wyróżnione ";

            const starIcon = document.createElement("i");
            starIcon.className = "fa-solid fa-star";
            highlightedIcon.appendChild(starIcon);

            postContainer.appendChild(highlightedIcon);
        }

        const image = document.createElement("img");
        image.src = `${protocol}//${host}${post.image_path}`;
        image.alt = post.title;

        const readDiv = document.createElement("div");
        readDiv.className = "read";
        readDiv.innerHTML = `<i class="fa-solid fa-angles-right" aria-hidden="true"></i>Przeczytaj`;

        const bodyContainer = document.createElement("div");
        bodyContainer.className = "body";

        const topInfo = document.createElement("div");
        topInfo.className = "top-info";

        const category = document.createElement("div");
        category.className = "category";
        category.style.background = post.category.backgroundColor;
        category.style.color = post.category.textColor;
        category.textContent = post.category.name;

        const clockIcon = document.createElement("i");
        clockIcon.className = "fa-solid fa-clock";
        const readingTime = document.createElement("p");
        readingTime.className = "reading-time";
        readingTime.textContent = `${post.read_time} min`;

        topInfo.appendChild(category);
        topInfo.appendChild(clockIcon);
        topInfo.appendChild(readingTime);

        const title = document.createElement("p");
        title.className = "title";
        title.textContent = post.title;

        const userContainer = document.createElement("div");
        userContainer.className = "user";

        const userImage = document.createElement("img");
        userImage.src = `${protocol}//${host}${post.user.image_path}`;
        userImage.alt = "user";

        const userInfoContainer = document.createElement("p");

        const userName = document.createElement("span");
        userName.className = "name";
        userName.textContent = `${post.user.firstname} ${post.user.lastname}`;

        const date = document.createElement("span");
        date.className = "date";
        date.textContent = post.created_at_formatted;

        userContainer.appendChild(userImage);
        userInfoContainer.appendChild(userName);
        userInfoContainer.appendChild(document.createElement("br"));
        userInfoContainer.appendChild(date);
        userContainer.appendChild(userInfoContainer);

        const shortBody = document.createElement("p");
        shortBody.className = "short_body";
        shortBody.textContent = post.excerpt;

        bodyContainer.appendChild(topInfo);
        bodyContainer.appendChild(title);
        bodyContainer.appendChild(userContainer);
        bodyContainer.appendChild(shortBody);

        postContainer.appendChild(image);
        postContainer.appendChild(readDiv);
        postContainer.appendChild(bodyContainer);

        link.appendChild(postContainer);

        const articlePosts = document.querySelector(".article .posts");
        articlePosts.appendChild(link);

    });
    isLoaded = true;
}
