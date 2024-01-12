// Inisialisasi QuaggaJS
Quagga.init({
    inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector("#scanner"),
        constraints: {
            width: 640,
            height: 480,
            facingMode: "environment"
        },
    },
    decoder: {
        readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader"]
    },
}, function (err) {
    if (err) {
        console.error(err);
        return;
    }
    console.log("Initialization finished. Ready to start");
    Quagga.start();
});

// Menangani hasil pemindaian
Quagga.onDetected(function (result) {
    var code = result.codeResult.code;
    document.querySelector("#result").innerText = "Detected barcode: " + code;

    // Tambahan: Berhenti pemindaian setelah barcode ditemukan
    Quagga.stop();
});
