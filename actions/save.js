document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".save-btn").forEach(button => {
        button.addEventListener("click", function () {
            let postId = this.getAttribute("data-post-id");

            fetch("../actions/save_post.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `post_id=${encodeURIComponent(postId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle("saved"); // Toggle only if success
                } else {
                    console.error("Error from server:", data.error);
                }
            });
        });
    });
});