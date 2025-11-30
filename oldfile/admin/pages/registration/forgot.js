const registeredEmails = ["admin@example.com", "staff@example.com"]; // Example registered emails

document.getElementById("login-form").addEventListener("submit", function(e) {
  e.preventDefault();
  const email = this.querySelector('input[type="text"]').value.trim();
  const message = document.getElementById("reset-message");

  if (registeredEmails.includes(email)) {
    message.textContent = "Verification link sent to your email.";
    message.style.color = "green";
    // Here you would send the actual email in a real app
  } else {
    message.textContent = "This email has not been signed up yet.";
    message.style.color = "#f4e91cff";
  }
});