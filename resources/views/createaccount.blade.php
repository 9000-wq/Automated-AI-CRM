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
                                            <div class="col-md-4">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control mb-2 @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="industry">Industry</label>
                                                <input type="text" name="industry" id="industry"
                                                    class="form-control mb-2 @error('industry') is-invalid @enderror"
                                                    value="{{ old('industry') }}">
                                                @error('industry')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
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
                                            <div class="col-md-6">
                                                <label for="number">Phone</label>
                                                <input type="text" name="number" id="number"
                                                    class="form-control mb-2 @error('number') is-invalid @enderror"
                                                    value="{{ old('number') }}">
                                                @error('number')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
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
                                            <div class="col-md-6">
                                                <label for="address">Address</label>
                                                <input type="text" name="address" id="address"
                                                    class="form-control mb-2 @error('address') is-invalid @enderror"
                                                    value="{{ old('address') }}">
                                                @error('address')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
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
                                            <div class="col-md-6">
                                                <label for="country">Country</label>
                                                <input type="text" name="country" id="country"
                                                    class="form-control mb-2 @error('country') is-invalid @enderror"
                                                    value="{{ old('country') }}">
                                                @error('country')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
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
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (key, value) {
                        // Highlight field
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        if (input.next('.invalid-feedback').length === 0) {
                            input.after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                        }
                        errorHtml += `<li>${value[0]}</li>`;
                    });
                    errorHtml += '</ul></div>';
                    $('.showalert').html(errorHtml);
                }
            });
        });
    });
</script>
@endpush

@include('layouts.footer')
