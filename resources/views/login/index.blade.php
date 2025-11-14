<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Akaun</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('{{ asset('IMG/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(0, 0, 0, 0.389);
            backdrop-filter: blur(2px);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            min-height: 300px;
        }

        h2 {
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .account-options {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            gap: 40px;
        }

        .option {
            width: 120px;
            padding: 15px;
            background: #0ca5fe;
            border-radius: 12px;
            cursor: pointer;
            transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .option:hover {
            transform: translateY(-5px);
            background: #0c48fed7;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .option img {
            width: 110px;
            height: 90px;
            filter: brightness(0) invert(1);
        }

        .option p {
            font-weight: bold;
            margin-top: 10px;
            color: white;
        }

        .option.selected {
            background: #0c04fac5;
            color: white;
            transform: translateY(-5px);
            opacity: 1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .option.dimmed {
            opacity: 0.5;
        }

        button {
            padding: 10px 20px;
            font-weight: bold;
            font-size: 16px;
            border: none;
            background-color: #0ca5fe;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #001ee0d5;
        }

        #next {
            display: none;
            margin: 0 auto;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>PILIH JENIS AKAUN</h2>
        <div class="account-options">
            <div class="option" id="admin">
                <img src="{{ asset('login/Images/Admin Support.png') }}" alt="Admin Icon">
                <p>ADMIN</p>
            </div>
            <div class="option" id="staff">
                <img src="{{ asset('login/Images/Staff Concept.png') }}" alt="Staff Icon">
                <p>STAFF</p>
            </div>
        </div>

        <button id="next">SETERUSNYA</button>
    </div>

    <script>
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
                if(selectedRole === "admin") {
                    // Save role to localStorage for use in registration
                    try { localStorage.setItem('selectedRole', 'admin'); } catch(e) {}
                    window.location.href = "{{ route('admin.auth.login') }}";
                }
                else if(selectedRole === "staff") {
                    // Save role to localStorage for use in registration
                    try { localStorage.setItem('selectedRole', 'staff'); } catch(e) {}
                    window.location.href = "{{ route('staff.auth.login') }}";
                }
                else {
                    alert("Sila pilih jenis akaun dahulu!");
                }
            });
        }
    </script>
</body>
</html>
