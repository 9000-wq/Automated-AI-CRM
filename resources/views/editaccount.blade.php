@include('layouts.header')

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Edit Account</strong></h1>

        <div class="card" style="border-radius: 10px;">
            <div style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0px 0px;"></div>

            <div class="card-body">
                <form id="edit-account-form" data-action="{{ route('accounts.update', $account->id) }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control mb-2" value="{{ old('name', $account->name) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Industry <span class="text-danger">*</span></label>
                            <input type="text" name="industry" class="form-control mb-2" value="{{ old('industry', $account->industry) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control mb-2" value="{{ old('email', $account->email) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control mb-2" value="{{ old('phone', $account->phone) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Website <span class="text-danger">*</span></label>
                            <input type="text" name="website" class="form-control mb-2" value="{{ old('website', $account->website) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control mb-2" value="{{ old('address', $account->address) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control mb-2" value="{{ old('city', $account->city) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" class="form-control mb-2" value="{{ old('country', $account->country) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control mb-2">
                                <option value="">-- Select Status --</option>
                                <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $account->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="mt-4">Contacts</h4>
                    <div id="contact-container">
                        @foreach ($account->contacts as $index => $contact)
                        <div class="contact-group border p-3 mb-3">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label>Name</label>
                                    <input type="text" name="contacts[{{$index}}][name]" value="{{ $contact->name }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Email</label>
                                    <input type="email" name="contacts[{{$index}}][email]" value="{{ $contact->email }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Phone</label>
                                    <input type="text" name="contacts[{{$index}}][phone]" value="{{ $contact->phone }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Birthday</label>
                                    <input type="date" name="contacts[{{$index}}][birthday]" value="{{ $contact->birthday }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Address</label>
                                    <input type="text" name="contacts[{{$index}}][address]" value="{{ $contact->address }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Description</label>
                                    <input type="text" name="contacts[{{$index}}][description]" value="{{ $contact->description }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label>Role</label>
                                    <input type="text" name="contacts[{{$index}}][contact_role]" value="{{ $contact->contactRole->label ?? '' }}" class="form-control">
                                    <input type="hidden" name="contacts[{{$index}}][contact_id]" value="{{ $contact->id ?? '' }}">
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger remove-contact mt-2">Remove</button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-contact" class="btn btn-info">Add New Contact</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Account</button>
                        <a href="{{ route('home.account') }}" class="btn btn-secondary">Back</a>
                    </div>

                    <div id="ajax-errors" class="alert alert-danger mt-3 d-none">
                        <ul class="mb-0"></ul>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let contactIndex = {{ count($account->contacts) }};

$('#add-contact').click(function () {
    const html = `
        <div class="contact-group border p-3 mb-3">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label>Name</label>
                    <input type="text" name="contacts[${contactIndex}][name]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Email</label>
                    <input type="email" name="contacts[${contactIndex}][email]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Phone</label>
                    <input type="text" name="contacts[${contactIndex}][phone]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Birthday</label>
                    <input type="date" name="contacts[${contactIndex}][birthday]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Address</label>
                    <input type="text" name="contacts[${contactIndex}][address]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Description</label>
                    <input type="text" name="contacts[${contactIndex}][description]" class="form-control">
                </div>
                <div class="col-md-4 mb-2">
                    <label>Role</label>
                    <input type="text" name="contacts[${contactIndex}][contact_role]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-danger remove-contact mt-2">Remove</button>
        </div>`;
    $('#contact-container').append(html);
    contactIndex++;
});

$(document).on('click', '.remove-contact', function () {
    $(this).closest('.contact-group').remove();
});

$('#edit-account-form').submit(function (e) {
    e.preventDefault();

    let form = $(this);
    let url = form.data('action');
    let formData = new FormData(this);
    formData.append('_method', 'PUT');
    formData.append('_token', '{{ csrf_token() }}');

    $('#ajax-errors').addClass('d-none').find('ul').html('');

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            window.location.href = "{{ route('home.account') }}";
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorList = '';
                $.each(errors, function (key, value) {
                    errorList += '<li>' + value[0] + '</li>';
                });
                $('#ajax-errors').removeClass('d-none').find('ul').html(errorList);
            } else {
                alert('Something went wrong!');
            }
        }
    });
});
</script>

@include('layouts.footer')
