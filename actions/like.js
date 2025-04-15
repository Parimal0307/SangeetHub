document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function () {
            let postId = this.getAttribute("data-post-id");
            let icon = this;
            let likesCounter = document.getElementById(`likes-${postId}`);

            fetch("../actions/like_post.php", {
                method: "POST",
                body: `post_id=${postId}`,
                headers: { "Content-Type": "application/x-www-form-urlencoded" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    icon.classList.toggle("liked");

                    if (likesCounter) {
                        likesCounter.innerText = `${data.like_count} likes`;
                    }
                } else {
                    alert("Error: " + data.error);
                }
            }).catch(error => console.error("Error:", error));;
        });
    });
});