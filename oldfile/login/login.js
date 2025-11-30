let selectedRole = "";

// Pilih role
const options = document.querySelectorAll(".option");
const nextButton = document.getElementById("next");

if(options.length > 0) {
  options.forEach(option => {
    option.addEventListener("click", () => {
      // reset semua option
      options.forEach(opt => {
        opt.classList.remove("selected");
        opt.classList.remove("dimmed");
      });

      // tandakan selected
      option.classList.add("selected");

      // dim semua option lain
      options.forEach(opt => {
        if(!opt.classList.contains("selected")) opt.classList.add("dimmed");
      });

      // simpan role
      selectedRole = option.id;

      // show button
      if(nextButton) nextButton.style.display = "block";
    });
  });
}

// klik next button
if(nextButton) {
  nextButton.addEventListener("click", () => {
    if(selectedRole === "admin") window.location.href = "../admin/pages/registration/formad.html";
    else if(selectedRole === "staff") window.location.href = "../staffs/formstaff.html";
    else alert("Sila pilih jenis akaun dahulu!");
  });
}
