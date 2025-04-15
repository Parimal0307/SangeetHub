document.getElementById("profile-upload").addEventListener("change", function() {
    let formData = new FormData();
    formData.append("profile_pic", this.files[0]);

    fetch("../actions/upload_profile.php", { method: "POST", body: formData })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    });
});

function updateName() {
    let newName = document.getElementById("new-name").value;
    
    fetch("update_name.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "display_name=" + newName
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        document.getElementById("display-name").innerText = newName;
    });
}

function showTab(tab) {
    document.getElementById("posts").style.display = tab === "posts" ? "block" : "none";
    document.getElementById("saved").style.display = tab === "saved" ? "block" : "none";
}