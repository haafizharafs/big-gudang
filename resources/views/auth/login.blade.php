@extends('layouts.app')
@section('content')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                @include('layouts.navbars.guest.navbar')
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto pt-5 mt-5">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <h4 class="font-weight-bolder">Sign In</h4>
                                    <p class="mb-0">Masukkan email dan password untuk melanjutkan</p>
                                </div>
                                <div class="card-body">
                                    <div class="d-none alert alert-danger text-white" role="alert" id="_alert">
                                        Email atau password salah
                                    </div>
                                    <form id="loginForm" novalidate>
                                        <div class="flex flex-col mb-3">
                                            <input type="email" id="email" class="form-control form-control-lg"
                                                placeholder="Email" autofocus autocomplete="email">
                                            <div id="emailFeedback" class="invalid-feedback"></div>
                                        </div>
                                        <div class="flex flex-col mb-3">
                                            <div class="input-group has-validation">
                                                <input type="password" id="password" style="border-right: none !important"
                                                    class="form-control form-control-lg" placeholder="Password"
                                                    autocomplete="current-password">
                                                <span onclick="showPassword(this)" style="border-left: none"
                                                    class="input-group-text"><i class="fa-solid fa-eye"></i></span>
                                                <div id="passwordFeedback" class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <button type="submit" id="loginBtn"
                                            class="btn btn-lg btn-danger btn-lg w-100 mt-4 mb-0">Masuk</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-danger h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center align-items-center overflow-hidden"
                                style="background-image: url('img/bg-login.jpg');background-size: cover; background-position: center center;">
                                <span class="mask bg-white opacity-6"></span>
                                <img src="{{ asset('img/logos/big-warna-full.png') }}" class="position-relative"
                                    alt="big net logo" width="250px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
@push('js')
    <script>
        const loginForm = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const alert = document.getElementById('_alert');
        loginForm.addEventListener('submit', e => {
            e.preventDefault()
            btn.disabled = true;
            btn.innerHTML = 'Loading...'
            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }
            axios.post(`${appUrl}/login`, data)
                .then(response => {
                    console.log(response.data);
                    window.location = '/'
                })
                .catch(error => {
                    const errors = error.response.data.errors;
                    if (error.response.status == 422) {
                        Object.keys(errors).forEach(key => {
                            const el = document.getElementById(key);
                            const fEl = document.getElementById(key + 'Feedback');
                            el.classList.add('is-invalid')
                            fEl.innerHTML = errors[key]
                        });
                    } else {
                        alert.classList.remove('d-none');
                        alert.innerHTML = 'Email atau password salah'
                    }
                })

                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Masuk'
                })
        })
    </script>
@endpush
