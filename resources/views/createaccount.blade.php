@include('layouts.header')
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>Create new Account</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">

                                    <form id="crmform" method="POST" action="{{ route('account.store') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control mb-2 @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="industry">Industry</label>
                                                <input type="text" name="industry" id="industry"
                                                    class="form-control mb-2 @error('industry') is-invalid @enderror"
                                                    value="{{ old('industry') }}">
                                                @error('industry')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email"
                                                    class="form-control mb-2 @error('email') is-invalid @enderror"
                                                    value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="number">Phone</label>
                                                <input type="text" name="phone" id="number"
                                                    class="form-control mb-2 @error('phone') is-invalid @enderror"
                                                    value="{{ old('phone') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="website">Website</label>
                                                <input type="text" name="website" id="website"
                                                    class="form-control mb-2 @error('website') is-invalid @enderror"
                                                    value="{{ old('website') }}">
                                                @error('website')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="address">Address</label>
                                                <input type="text" name="address" id="address"
                                                    class="form-control mb-2 @error('address') is-invalid @enderror"
                                                    value="{{ old('address') }}">
                                                @error('address')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="city">City</label>
                                                <input type="text" name="city" id="city"
                                                    class="form-control mb-2 @error('city') is-invalid @enderror"
                                                    value="{{ old('city') }}">
                                                @error('city')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="country">Country</label>
                                                <input type="text" name="country" id="country"
                                                    class="form-control mb-2 @error('country') is-invalid @enderror"
                                                    value="{{ old('country') }}">
                                                @error('country')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="status">Status</label>
                                                <select name="status" id="status"
                                                    class="form-control mb-2 @error('status') is-invalid @enderror">
                                                    <option value="">-- Select Status --</option>
                                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div id="contactsWrapper" style="display: none;">
                                        <!-- Contact forms will be added here -->
                                        </div>

                                        <button id="showFormBtn" class="btn btn-primary mb-3">Add Contact</button><br>
                                                                  
                                        <button class="btn btn-primary savebtn" type="button">Save</button>
                                        <div class="showalert mt-3"></div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>



<!-- Template to clone -->
<div id="contactFormTemplate" class="contact-form d-none">


    <h3>Add Contact</h3>
    <div class="row mb-3">
        <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="contacts[0][name]" class="form-control" required>
        </div>
        <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="contacts[0][email]" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="contacts[0][phone]" class="form-control" required>
        </div>
        <div class="col-md-6">
        <label class="form-label">Birthday</label>
        <input type="date" name="contacts[0][birthday]" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Address</label>
        <input type="text" name="contacts[0][address]" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="contacts[0][description]" class="form-control" rows="3"></textarea>
    </div>

    <div class="mb-3">

        <label class="form-label">Role</label>    
        <input type="text" name="contacts[0][contact_role]" class="form-control">

    </div>

    <button type="button" title="Remove Contact" class="btn btn-danger remove-contact-btn mb-3"><i class="fas fa-trash"></i></button>
    <hr>
</div>


@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.savebtn').on('click', function (e) {
            e.preventDefault();

            $('.form-control').removeClass('is-invalid'); // reset previous errors
            $('.invalid-feedback').remove(); // clear old messages

            $.ajax({
                url: "{{ route('account.store') }}",
                type: "POST",
                data: $('#crmform').serialize(),
                success: function (response) {
                    $('.showalert').html(`<div class="alert alert-success">Account created successfully!</div>`);
                    $('#crmform')[0].reset();
                    window.location="{{route('home.account')}}";
                    
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger text-danger"><ul>';
                    $.each(errors, function (key, value) {
                        // Highlight field
                        const input = $(`[name="${key}"]`);
                        // input.addClass('is-invalid');
                        // if (input.next('.invalid-feedback').length === 0) {
                            // input.after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                        // }
                        errorHtml += `<li>${value[0]}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    $('.showalert').html(errorHtml);
                }
            });
        });



        let contactIndex = 0;
        function updateNames() {
        $('#contactsWrapper .contact-form').each(function (index) {
            $(this).find('input, select, textarea').each(function () {
            let name = $(this).attr('name');
            if (name) {
                let updated = name.replace(/contacts\[\d+\]/, `contacts[${index}]`);
                $(this).attr('name', updated);
            }
            });
        });
        }


        $('#showFormBtn').click(function () {
            $('#contactsWrapper').show();
            $('#submitBtn').show();

            const $template = $('#contactFormTemplate').clone().removeClass('d-none').removeAttr('id');
            $('#contactsWrapper').append($template);
            updateNames();
        });

        $(document).on('click','.remove-contact-btn' ,function () {
            $(this).closest('.contact-form').remove();
            updateNames();

            if ($('#contactsWrapper .contact-form').length === 0) {
            $('#contactsWrapper').hide();
            $('#submitBtn').hide();
            }
        });





    });
</script>
@endpush

@include('layouts.footer')
