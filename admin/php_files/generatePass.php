<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Live Password Hash Generator</title>

<style>
    body { font-family: Arial, sans-serif; padding: 30px; max-width: 450px; }
    input, textarea, button { width: 100%; padding: 10px; margin-top: 10px; }
    textarea { height: 90px; }
    .copy-btn { background: #28a745; color: #fff; border: none; cursor: pointer; }
</style>
</head>
<body>

<h3>Password Hash Generator (Live)</h3>

<label>Enter Password:</label>
<input type="text" id="password" oninput="generateHash()">

<label>Hashed Password:</label>
<textarea id="hashOutput" readonly></textarea>

<button class="copy-btn" onclick="copyHash()">Copy Hash</button>

<script>
let typingTimer;
const delay = 300; // ms (prevents hashing on every keystroke)

function generateHash() {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        const password = document.getElementById("password").value.trim();
        const hashField = document.getElementById("hashOutput");

        // Clear hash if input is empty
        if (password === "") {
            hashField.value = "";
            return;
        }

        fetch("hash.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "password=" + encodeURIComponent(password)
        })
        .then(res => res.text())
        .then(data => {
            hashField.value = data;
        });
    }, delay);
}

function copyHash() {
    const hashField = document.getElementById("hashOutput");
    if (!hashField.value) return;

    hashField.select();
    document.execCommand("copy");
    alert("Hash copied!");
}
</script>

</body>
</html>
