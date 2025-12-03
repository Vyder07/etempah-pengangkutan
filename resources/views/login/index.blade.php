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

        .option svg {
            width: 80px;
            height: 80px;
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="80" height="80">
                    <path d="M12 2C9.24 2 7 4.24 7 7s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3z"/>
                    <path d="M12 13c-4.42 0-8 2.69-8 6v2h16v-2c0-3.31-3.58-6-8-6zm6 6H6v-.5c0-2.03 2.64-4.5 6-4.5s6 2.47 6 4.5v.5z"/>
                    <path d="M17 8h4v2h-4zm0-3h4v2h-4zm0 6h4v2h-4z"/>
                </svg>
                <p>ADMIN</p>
            </div>
            <div class="option" id="staff">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="80" height="80">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
                    <path d="M12 13c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4zm6 5H6v-1c0-.37.53-1.14 1.84-1.81C9.16 14.54 10.71 14 12 14s2.84.54 4.16 1.19C17.47 15.86 18 16.63 18 17v1z"/>
                    <path d="M19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3z"/>
                </svg>
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
