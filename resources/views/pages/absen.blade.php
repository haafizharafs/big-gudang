@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    <div class="container-fluid px-0 px-sm-4 py-3">
        <div class="d-flex justify-content-center">
            <div class="w-100 w-sm-75 w-md-50">
                <div class="card card-body" id="cardAbsen">
                    <div class="d-flex align-items-center">
                        <div class="text-sm" id="whichAbsen"></div>
                        <div class="ms-auto text-end">
                            <div class="text-sm">{{ now()->translatedFormat('l, j F Y') }}</div>
                        </div>
                    </div>
                    <div class="text-xs  mt-2"><i class="fa-regular fa-clock me-1"></i>{{ date('H:i') }}</div>
                    <div class="text-xs opacity-7 my-2"><i class="fa-solid fa-location-dot me-1"></i><span
                            id="koordinat"></span></div>
                    <div class="text-xs opacity-7" id="location"></div>
                    <div class="mt-2">
                        <video id="webcam" autoplay style="width: 100%; transform: scaleX(-1)" class="rounded-3"></video>
                        <canvas id="canvas" class="d-none rounded-3" style="width: 100%"></canvas>
                    </div>

                    <button id="cameraButton"
                        class="btn rounded-circle align-self-center d-flex justify-content-center align-items-center"
                        style="
                            width: 4rem;
                            height: 4rem;
                            margin-top: -5rem;
                            margin-bottom: 1rem;
                            z-index: 2;
                            background-color: #fff4;
                            color: white;
                            ">
                        <i class="fa-solid fa-camera fa-xl"></i>
                    </button>
                    <input type="hidden" id="foto">
                    <div id="fotoFeedback" class="invalid-feedback text-xs "></div>

                    <input type="text" id="aktivitas" class="form-control mt-2" placeholder="Aktivitas">
                    <div id="aktivitasFeedback" class="invalid-feedback text-xs mt-2"></div>

                    <button id="absenButton" class="btn bg-gradient-success w-100 mt-2" disabled>Absen</button>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const authId = `{{ auth()->user()->id }}`
        const cardBodyEl = document.getElementById('cardAbsen');
        const locationEl = document.getElementById('location');
        const koordinatEl = document.getElementById('koordinat');
        const video = document.getElementById('webcam');
        const captureButton = document.getElementById('capture');
        const canvas = document.getElementById('canvas');
        const foto = document.getElementById('foto');
        const absenButton = document.getElementById('absenButton');
        const cameraButton = document.getElementById('cameraButton');

        const url = (latitude, longitude) => {
            return `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&accept-language=id`
        }
        let koordinat, alamat = '';
        let taked = false;



        document.addEventListener('DOMContentLoaded', () => {
            getWhichAbsen()
            koordinatEl.innerHTML =
                '<i class="fa-solid fa-spin fa-spinner me-1"></i>Sedang mendapatkan koordinat...';
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    koordinat = position.coords.latitude + ',' + position.coords.longitude
                    koordinatEl.innerHTML = koordinat;
                    fetchLocation(position.coords.latitude, position.coords.longitude);
                }, (error) => {
                    cardBodyEl.innerHTML = `<div class="py-3 text-center ">
                        <div class="text-center cursor-pointer" onclick="location.reload"><i class="fa-solid fa-rotate-left fa-xl"></i></div>
                        <div class="text-sm">${geocodeErrMsg(error)}</div>
                        <div class="text-sm">Tekan ikon diatas untuk memuat ulang halaman</div>
                        <div class="text-sm">Jika masih terjadi masalah, <a href="https://wa.me/6281521544674" class="text-primary" target="blank">Hubungi administrator</a></div>
                    </div>`;
                });
            } else {
                cardBodyEl.innerHTML = `<div class="py-5 text-center">
                    <div class="text-center"><i class="fa-solid fa-triangle-exclamation fa-xl"></i></div>
                    <div>Fitur GPS tidak didukung oleh perangkat ini</div>
                </div>`;
            }
        })

        const AbsenSettedTime = @json(\App\Models\Absen::$settedTime);
        const absenLabel = ['Pertama', 'Kedua', 'Ketiga', 'Terakhir'];

        function getWhichAbsen() {
            const now = moment("{{ date('H:i') }}", 'HH:mm')
            AbsenSettedTime.forEach((time, i) => {
                if (i > AbsenSettedTime.length - 1) {
                    if (now.isSameOrAfter(moment(time, 'HH:mm'))) {
                        console.log(now);
                        document.getElementById('whichAbsen').innerHTML = 'Absen ' + absenLabel[i]
                        return;
                    }
                }
                if (now.isSameOrAfter(moment(AbsenSettedTime[i], 'HH:mm')) && now.isBefore(moment(AbsenSettedTime[
                        i + 1], 'HH:mm'))) {
                    console.log(now);
                    document.getElementById('whichAbsen').innerHTML = 'Absen ' + absenLabel[i]
                    return;
                }
            });
        }

        function fetchLocation(lat, lon) {
            alamat = ''
            locationEl.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Sedang mendapatkan lokasi...';
            axios.get(url(lat, lon))
                .then(response => {
                    console.log(response.data);
                    alamat = response.data.display_name;
                    locationEl.innerHTML = alamat;
                    absenButton.disabled = false
                })
                .catch(error => {
                    console.error(error);
                    locationEl.innerHTML = `<a href="javascript:;" class="" onclick="fetchLocation(${lat},${lon})">
                        <i class="fa-solid fa-rotate-left me-1"></i>
                        Gagal mendapatkan alamat, tekan untuk memuat ulang
                    </a>`
                    absenButton.disabled = true
                });
            startCamera()
        }

        function startCamera() {
            canvas.classList.add('d-none')
            video.classList.remove('d-none')
            navigator.mediaDevices.getUserMedia({
                video: true
            }).then((stream) => {
                video.srcObject = stream;
            });
        }

        document.addEventListener('keypress', e => {
            if (e.charCode == 32) {
                document.getElementById('cameraButton').click();
                document.getElementById('aktivitas').focus()
            }
            if (e.charCode == 13) document.getElementById('absenButton').click()
        })

        cameraButton.addEventListener('click', e => {
            if (!taked) {
                taked = true;
                e.currentTarget.innerHTML = '<i class="fa-solid fa-rotate-right fa-flip-horizontal fa-xl"></i>'
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').scale(-1, 1);
                canvas.getContext('2d').drawImage(video, 0, 0, -canvas.width, canvas.height);
                foto.value = canvas.toDataURL('image/jpeg')
                canvas.classList.remove('d-none')
                video.classList.add('d-none')
                const tracks = video.srcObject.getTracks();
                tracks.forEach(track => track.stop());
                video.srcObject = null;
            } else {
                taked = false
                foto.value = ''
                e.currentTarget.classList.replace('bg-gradient-warning', 'bg-gradient-primary')
                e.currentTarget.innerHTML = '<i class="fa-solid fa-camera fa-xl"></i>'
                startCamera()
            }
        });

        absenButton.addEventListener('click', e => {
            e.target.innerHTML = '<i class="fa-solid fa-spin fa-spinner me-1"></i>Memproses absen...'
            e.target.disabled = true
            const data = {
                foto: foto.value,
                koordinat: koordinat,
                alamat: alamat,
                aktivitas: document.getElementById('aktivitas').value,
            };
            axios.post(`${appUrl}/api/absen`, data, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    Success.fire(response.data.message).then(()=>{
                        location.href = `${appUrl}/karyawan/absen`
                    })
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        $('#Modal').find('.invalidFeedback').show();
                        if (error.response.data.errors['aktivitas'] == undefined) {
                            $('#aktivitas').removeClass('is-invalid');
                            $('#aktivitasFeedback').hide();
                        } else {
                            $('#aktivitasFeedback').text(error.response.data.errors[
                                'aktivitas']);
                            $('#aktivitas').addClass('is-invalid');
                            $('#aktivitasFeedback').show();
                        }
                        if (error.response.data.errors['koordinat'] == undefined) {
                            $('#koordinatFeedback').hide();
                        } else {
                            $('#koordinatFeedback').text(error.response.data.errors[
                                'koordinat']);
                            $('#koordinatFeedback').show();
                        }
                        if (error.response.data.errors['foto'] == undefined) {
                            $('#fotoFeedback').hide();
                        } else {
                            $('#fotoFeedback').text(error.response.data.errors['foto']);
                            $('#fotoFeedback').show();
                        }
                    } else {
                        Failed.fire('Terdapat kesalahan dalam memproses data!')
                    }
                })
                .finally(() => {
                    e.target.innerHTML = 'Absen'
                    e.target.disabled = false
                })
        });
    </script>
@endpush
