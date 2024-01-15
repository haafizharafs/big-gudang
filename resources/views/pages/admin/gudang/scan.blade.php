<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logos/big-warna.png') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        main {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #reader {
            width: 600px;
        }
        #result {
            text-align: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

    <main>
        <div id="reader"></div>
        <div id="result"></div>
    </main>

    <script>
        const scanner = new Html5QrcodeScanner('reader', {
            qrbox: {
                width: 250,
                height: 250,
            },
            fps: 20,
        });

        scanner.render(success, error);

        function success(result) {
            var routeUrl = "{{ route('indexMutasi') }}";

            document.getElementById('result').innerHTML = `
            <h2>Sukses!</h2>
            <p>Hasil scan: ${result}</p>
            <a href="${routeUrl}" onclick="bukaModalTambahMutasiMasuk()">Kembali</a>
            `;

            scanner.clear();
            document.getElementById('reader').remove();
        }

        function error() {
            console.error(err);
        }
    </script>
</body>
</html>
