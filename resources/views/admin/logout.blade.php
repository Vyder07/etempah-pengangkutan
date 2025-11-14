<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Log Keluar</title>
    <style>
        :root{--primary:#0c48fe;--bg:#f6f8fb}
        body{
            margin:0;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background-color:var(--bg);
            font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background-image: url('{{ asset('IMG/WARNING.jpg') }}');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .card{
            width:360px;
            padding:24px;
            background:#fff;
            border-radius:12px;
            box-shadow:0 8px 30px rgba(3,33,84,0.06);
            text-align:center;
        }
        .card h1{margin:0 0 8px;font-size:1.25rem;color:#111}
        .card p{margin:0 0 16px;color:#444}
        .btn{
            display:inline-block;
            padding:10px 18px;
            background:var(--primary);
            color:#fff;
            border-radius:8px;
            text-decoration:none;
            font-weight:600;
            border:none;
            cursor:pointer;
        }
        .btn.ghost{background:#e6eefb;color:var(--primary);margin-left:10px}
        .small{font-size:.9rem;color:#666;margin-top:12px}
    </style>
</head>
<body>
    <div class="card" role="status" aria-live="polite">
        <h1>Anda telah log keluar</h1>
        <p>Terima kasih. Sesi anda telah ditamatkan.</p>

        <div>
            <a id="loginBtn" class="btn" href="{{ route('admin.auth.login') }}">Log Masuk Semula</a>
            <button id="stayBtn" class="btn ghost" type="button">Tinggal di sini</button>
        </div>

        <div class="small">Anda akan dialihkan ke halaman log masuk dalam <span id="count">5</span> saat.</div>
    </div>

    <script>
        try { localStorage.removeItem('loggedInUser'); sessionStorage.removeItem('authToken'); }
        catch(e){}

        (function(){
            var t = 5, el = document.getElementById('count');
            var iv = setInterval(function(){
                t--; el.textContent = t;
                if(t <= 0){ clearInterval(iv); window.location.href = '{{ route("admin.auth.login") }}'; }
            }, 1000);

            document.getElementById('stayBtn').addEventListener('click', function(){
                clearInterval(iv);
                document.querySelector('.small').textContent = 'Pengalihan dibatalkan.';
            });

            document.getElementById('loginBtn').addEventListener('click', function(){
                try { localStorage.clear(); sessionStorage.clear(); } catch(e){}
            });
        })();
    </script>
</body>
</html>
