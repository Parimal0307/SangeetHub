document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function () {
            let postId = this.getAttribute("data-post-id");
            let commentInput = document.getElementById(`comment-input-${postId}`);
            let commentText = commentInput.value.trim();

            if (commentText === "") return;

            fetch("../actions/add_comment.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `post_id=${postId}&comment=${encodeURIComponent(commentText)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let commentsDiv = document.getElementById(`comments-${postId}`);

                    // Create a new comment div
                    let newCommentDiv = document.createElement("div");
                    newCommentDiv.classList.add("comment-item");

                    // Create an image element
                    let img = document.createElement("img");
                    img.src = `../uploads/profiles/${data.profile_pic}`;

                    // Create a paragraph for the comment
                    let newComment = document.createElement("p");
                    newComment.innerHTML = `<strong>${data.username}</strong> ${data.comment}`;

                    // Append image and comment to the new comment div
                    newCommentDiv.appendChild(img);
                    newCommentDiv.appendChild(newComment);

                    // Append the new comment div to the comments section
                    commentsDiv.appendChild(newCommentDiv);

                    // clear input field
                    commentInput.value = "";
                } else {
                    console.error("Error:", data.error);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});