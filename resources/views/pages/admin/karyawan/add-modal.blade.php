@push('modal')
    <div class="modal fade" id="addTeknisiModal" tabindex="-1" role="dialog" aria-labelledby="addTeknisiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document" id="addTeknisiModal-Content">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTeknisiModalLabel">Tambah Teknisi</h5>
                    <div class="dropdown ms-auto modal-dropdown d-none">
                        <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown" id="dropdownButton"
                            aria-expanded="false">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-end  p-2 me-sm-n3" aria-labelledby="dropdownButton">
                            <li>
                                <a class="dropdown-item btn-selesai border-radius-md py-1" data-bs-toggle="modal"
                                    href="#checkPasswordModal">
                                    <i class="fa-solid fa-lock me-2"></i>
                                    Ubah Password
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="dropdown-item btn-selesai border-radius-md py-1"
                                    onclick="hapusTeknisi()">
                                    <i class="fa-solid fa-lock me-2 text-danger"></i>
                                    Hapus Teknisi
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    @include('components.forms.normal-input', [
                        'name' => 'namaTeknisi',
                        'label' => 'Nama',
                        'placeholder' => 'eg. John Doe',
                    ])
                    @include('components.forms.normal-input', [
                        'name' => 'specialityTeknisi',
                        'label' => 'Jabatan',
                        'placeholder' => 'eg. Teknisi Lapangan',
                    ])
                    @include('components.forms.no_telp-input', [
                        'name' => 'no_telpTeknisi',
                        'label' => 'No Telepon/Whatsapp',
                        'placeholder' => 'eg. 6281234567890',
                    ])
                    @include('components.forms.select-input', [
                        'name' => 'wilayah_idTeknisi',
                        'label' => 'Wilayah',
                        'id' => 'id',
                        'value' => 'nama_wilayah',
                        'placeholder' => 'Wilayah',
                        'option' => $wilayahs,
                    ])
                    @include('components.forms.normal-input', [
                        'name' => 'emailTeknisi',
                        'label' => 'email',
                        'type' => 'email',
                        'placeholder' => 'Email',
                    ])

                    <div class="d-flex align-items-center gap-3">

                        @include('components.forms.normal-input', [
                            'name' => 'passwordTeknisi',
                            'label' => 'password',
                            'placeholder' => 'Password',
                            'class' => 'flex-grow-1',
                        ])

                        <div class="form-group d-flex flex-column">
                            <label class="form-control-label opacity-0">-</label>
                            <button type="button" class="btn m-0 btn-primary btn-copy-password" data-bs-toggle="tooltip"
                                data-bs-title="Salin password"
                                onclick="copyText('.modal-body','Password',$('#passwordTeknisi').val())">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>

                        <div class="form-group d-flex flex-column">
                            <label class="form-control-label opacity-0">-</label>
                            <button type="button" id="generate-btn" class="btn m-0 btn-warning"
                                onclick="generateRandomPassword()">Generate</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary me-2 " data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-primary btn-simpan"><i
                            class="fa-solid fa-spinner fa-spin me-1 loader-simpan d-none"></i>Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkPasswordModal" tabindex="-1" role="dialog" aria-labelledby="checkPasswordModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document" id="checkPasswordModal-Content">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="formCheckPassword">
                        @include('components.forms.password-input', [
                            'name' => 'old_password',
                            'type' => 'password',
                            'label' => 'Masukkan password Admin',
                            'autofocus' => 'autofocus',
                        ])
                        <div class="text-end">
                            <button type="submit" class="btn bg-gradient-primary btn-next align-self-start">
                                <i class="fa-solid fa-spinner fa-spin me-2 d-none"></i>
                                Selanjutnya
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document" id="changePasswordModal-Content">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="formChangePassword">
                        @include('components.forms.password-input', [
                            'name' => 'password',
                            'type' => 'password',
                            'label' => 'Masukkan Password Baru',
                            'autofocus' => 'autofocus',
                        ])
                        @include('components.forms.password-input', [
                            'name' => 'password_confirmation',
                            'type' => 'password',
                            'label' => 'Konfirmasi Password',
                        ])
                        <div class="text-end">
                            <button type="button" class="btn bg-gradient-secondary me-2" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="fa-solid fa-spinner fa-spin me-2 d-none"></i>
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endpush


@push('js')
    <script>
        let teknisi_id;
        let isEditTeknisi = false;

        function generateRandomPassword() {
            var length = 6;
            var charset =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            var password = "";

            for (var i = 0; i < length; i++) {
                var randomIndex = Math.floor(Math.random() * charset.length);
                password += charset.charAt(randomIndex);
            }
            $("#passwordTeknisi").val(password);
        }


        function editTeknisi(e, id) {
            $('#addTeknisiModalLabel').text('Edit Karyawan')
            $('.modal-dropdown').removeClass('d-none')
            isEditTeknisi = true;
            teknisi_id = id;
            $('.btn-edit-teknisi').attr('disabled', true)
            $(e).html('<i class="fa-solid fa-spinner fa-spin spinner"></i>')
            axios.get(`${appUrl}/api/teknisi/${id}/edit`)
                .then(response => {
                    data = response.data;
                    $('#namaTeknisi').val(data.nama);
                    $('#specialityTeknisi').val(data.speciality);
                    $('#no_telpTeknisi').val(data.no_telp);
                    $('#wilayah_idTeknisi').val(data.wilayah_id);
                    $('#emailTeknisi').val(data.email);
                    $('#passwordTeknisi').parent().parent().addClass('d-none');
                })
                .finally(() => {
                    $(e).html('<i class="fa-solid fa-pen-to-square"></i>')
                    $('.btn-edit-teknisi').attr('disabled', false)

                    $('#addTeknisiModal').modal('show');
                })
        }


        function addTeknisi() {
            $('#addTeknisiModalLabel').text('Tambah Karyawan')
            $('.modal-dropdown').addClass('d-none')
            if (isEditTeknisi) {
                teknisi_id = undefined;
                isEditTeknisi = false;
                $('#namaTeknisi').val('');
                $('#specialityTeknisi').val('');
                $('#no_telpTeknisi').val('62');
                $('#wilayah_idTeknisi').prop('selectedIndex', 0);
                $('#emailTeknisi').val('');
                $('#passwordTeknisi').parent().parent().removeClass('d-none');
            }
        }


        $("#addTeknisiModal").on("shown.bs.modal", function() {
            generateRandomPassword();
            $(this).on("keypress", function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    $("#addTeknisiModal .btn-simpan").click();
                }
            });

            const addTeknisiModalTooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const addTeknisiModalTooltipList = [...addTeknisiModalTooltipTriggerList].map(tooltipTriggerEl =>
                new bootstrap.Tooltip(
                    tooltipTriggerEl, {
                        container: '.form-group',
                    }))
        });

        $("#addTeknisiModal").on("hide.bs.modal", function() {
            $("#addTeknisiModal").find(".form-control, .form-select").removeClass("is-invalid");
            $("#addTeknisiModal").find(".invalidFeedback").hide();
            @if (isset($modal))
                $('{{ $modal }}').modal('show')
            @endif
        });

        $('#addTeknisiModal .btn-simpan').on("click", (e) => {
            $('.loader-simpan').removeClass('d-none')
            $(e.target).attr('disabled', true)
            data = {
                nama: $("#namaTeknisi").val(),
                speciality: $("#specialityTeknisi").val(),
                email: $("#emailTeknisi").val(),
                password: $("#passwordTeknisi").val(),
                no_telp: $("#no_telpTeknisi").val(),
                wilayah_id: $("#wilayah_idTeknisi").val(),
            };

            if (isEditTeknisi) {
                data._method = 'PUT'
                axios
                    .patch(`${appUrl}/api/teknisi/${teknisi_id}`, data)
                    .then((response) => {
                        resolveTeknisi(response)
                    })
                    .catch((error) => {
                        rejectTeknisi(error)
                    })
                    .finally(() => {

                    })
                return;
            }
            axios
                .post(`${appUrl}/api/teknisi`, data)
                .then((response) => {
                    resolveTeknisi(response)
                })
                .catch((error) => {
                    rejectTeknisi(error)
                });
        });

        function resolveTeknisi(response) {
            $('.loader-simpan').addClass('d-none')
            $('#addTeknisiModal .btn-simpan').removeAttr('disabled')
            $("#addTeknisiModal").find(".form-control").val("");
            $("#addTeknisiModal").find(".form-select").val("");
            $("#addTeknisiModal").find("#no_telp").val("62");
            @if (!isset($modal))
                table.ajax.reload();
            @endif
            $("#addTeknisiModal").modal("hide");

            @include('components.swal.success')
        }

        function rejectTeknisi(error) {
            $('.loader-simpan').addClass('d-none')
            $('#addTeknisiModal .btn-simpan').removeAttr('disabled')
            $("#addTeknisiModal").find(".invalid-feedback").text("");
            $("#addTeknisiModal").find(".form-select").removeClass("is-invalid");
            var errors = error.response.data.errors;
            if (error.response.status == 422) {
                for (const key in errors) {
                    if (errors.hasOwnProperty(key)) {
                        $("#" + key + 'Teknisi').addClass("is-invalid");
                        $("#" + key + 'Teknisi' + "Feedback").show();
                        $("#" + key + 'Teknisi' + "Feedback").text(errors[key]);
                    }
                }
            } else {
                @include('components.swal.error')
                $("#addTeknisiModal").modal("hide");
            }
        }

        function hapusTeknisi() {
            Swal.fire({

                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus teknisi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn bg-gradient-primary',
                    cancelButton: 'btn bg-gradient-secondary me-2'
                },
                buttonsStyling: false,
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#addTeknisiModal").modal("hide");
                    axios.delete(`${appUrl}/api/teknisi/${teknisi_id}`)
                        .then(response => {
                            @include('components.swal.success')
                            table.ajax.reload()
                        })
                        .catch(error => {
                            @include('components.swal.error')
                        })
                }
            });
        }
        $('#checkPasswordModal, #changePasswordModal').on('shown.bs.modal', e => {
            $(e.target).find('input[autofocus]').focus()
        })
        let old_password, password;
        $('#formCheckPassword').on("submit", (e) => {
            e.preventDefault()
            $('.btn-next').attr('disabled', true)
            $('.btn-next i').removeClass('d-none')
            data = {
                old_password: $('#old_password').val()
            };
            axios.post(`${appUrl}/api/profile/check-password`, data)
                .then(response => {
                    $('.btn-next').removeAttr('disabled')
                    $('.btn-next i').addClass('d-none')
                    old_password = $('#old_password').val()
                    $('#old_password').removeClass('is-invalid');
                    $('#old_password').val('');
                    $("#old_passwordFeedback").hide();
                    $("#old_passwordFeedback").text('');
                    $('#checkPasswordModal').modal('hide');
                    $('#changePasswordModal').modal('show')
                })
                .catch(error => {
                    $('.btn-next').removeAttr('disabled')
                    $('.btn-next i').addClass('d-none')
                    $('#old_password').addClass('is-invalid');
                    $("#old_passwordFeedback").show();
                    $("#old_passwordFeedback").text(error.response.data.errors['old_password']);
                })
        });

        $('#formChangePassword').on("submit", e => {
            e.preventDefault()
            const btn = $('#formChangePassword .btn')
            const loader = btn.find('i')
            btn.attr('disabled', true)
            loader.removeClass('d-none')
            data = {
                old_password: old_password,
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val(),
            };
            axios.patch(`${appUrl}/api/teknisi/change-password/${teknisi_id}`, data)
                .then(response => {
                    btn.attr('disabled', false)
                    loader.addClass('d-none')
                    $('#changePasswordModal').find('.form-control').val('');
                    $('#changePasswordModal').find('.form-control').removeClass('is-invalid');
                    $('#changePasswordModal').find('.invalid-feedback').hide();
                    $('#changePasswordModal').find('.invalid-feedback').text('');
                    $('#changePasswordModal').modal('hide');
                    @include('components.swal.success')
                })
                .catch(error => {
                    btn.attr('disabled', false)
                    loader.addClass('d-none')
                    $("#changePasswordModal").find(".invalid-feedback").text("");
                    $("#changePasswordModal").find(".form-control").removeClass("is-invalid");
                    var errors = error.response.data.errors;
                    if (error.response.status == 422) {
                        for (const key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Feedback").show();
                                $("#" + key + "Feedback").text(errors[key]);
                            }
                        }
                    } else {
                        @include('components.swal.error')
                        $("#changePasswordModal").modal("hide");
                    }
                })
        });
    </script>
@endpush
