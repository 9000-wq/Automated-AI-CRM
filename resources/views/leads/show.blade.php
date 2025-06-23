@include('layouts.header')

<style>
    textarea.form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(59, 101, 234, 0.25);
        border-color: #3b65ea;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem 1rem;
        }

        textarea {
            font-size: 0.95rem;
        }
    }
</style>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Lead Details</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <!-- Lead Information Card -->
                        <div class="col-sm-8">
                            <div class="card" style='border-radius:10px;'>
                                <div
                                    style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;">
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="mb-4"><strong>Name:</strong> {{ $lead->name }}</h3>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="mb-4">
                                                <strong>Case Ref:</strong> {{ $lead->case_ref }}
                                                <a href="{{ route('leads.edit', $lead) }}"
                                                    class="btn btn-primary float-end"><i class="fas fa-edit"></i></a>
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="mb-4"><strong>Source:</strong> {{ $lead->source }}</h3>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="mb-4"><strong>Assigned To:</strong>
                                                {{ $lead->user->name ?? 'Unassigned' }}</h3>
                                        </div>
                                    </div>

                                    @php
                                        $statusColors = [
                                            'New' => 'primary',
                                            'Contacted' => 'info',
                                            'Follow-up' => 'warning',
                                            'Converted' => 'success',
                                            'Lost' => 'danger',
                                        ];
                                    @endphp

                                    <h3 class="mb-4">
                                        <strong>Status:</strong>
                                        <span class="text-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                            {{ $lead->status }}
                                        </span>
                                    </h3>

                                    <hr>

                                    <h3 class="mt-3 mb-4"><strong>Lead Notes</strong></h3>
                                    <form id="notesForm">
                                        <div class="mb-3">
                                            <textarea class="form-control notesinput" rows="3"
                                                placeholder="Write your notes here..." required></textarea>
                                        </div>
                                        <input type="hidden" name="leadid" id="leadid" value="{{ $lead->id }}">
                                        <div class="text-end">
                                            <div class="showalert mt-3 text-start"></div>
                                            <button type="button" class="btn btn-primary savenotes px-4">Save
                                                Notes</button>
                                        </div>
                                    </form>

                                    <hr class="my-4">
                                    <h4>Notes</h4>
                                    <div class="mb-4">
                                        <div class="appendnotes"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Activities Cards -->
                        <div class="col-md-4">
                            <div class="card" style='border-radius:10px;'>
                                <div
                                    style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;">
                                </div>
                                <div class="card-body">
                                    <h3>Contacts</h3>
                                    @foreach($lead->contacts as $contact)
                                        <div class="mb-3" style="font-size: large; font-weight: 500;">
                                            <strong>{{ $contact->full_name }}</strong>
                                            <a href="{{ route('leadcontact', $contact->id) }}"
                                                class="btn btn-info m-1 float-end"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('deleteleadcontact', [$contact->id, $lead->id]) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger m-1 float-end">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <br>
                                            Name: {{ $contact->name }}<br>
                                            Role: {{ $contact->role->label }}<br>
                                            Email: {{ $contact->email }}<br>
                                            Phone: {{ $contact->phone }}<br>
                                            Address: {{ $contact->address }}
                                        </div>
                                    @endforeach

                                    <a href="#" onclick="history.back()" class="btn btn-secondary">Back</a>
                                </div>
                            </div>

                            <!-- History Card -->
                            <div class="card mt-3" style="border-radius:10px;">
                                <div
                                    style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3>Activities</h3>
                                        <div>
                                            <i class="fas fa-envelope me-2"></i>
                                            <i class="fas fa-calendar-alt me-2"></i>

                                            <!-- Updated call icon link -->
                                            <a href="{{ route('create.call', ['lead' => $lead->id]) }}"
                                                title="Make a Call">

                                                <i class="fas fa-phone me-2" style="cursor: pointer;"></i>
                                            </a>

                                            <i class="fas fa-ellipsis-h"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <div class="card mt-3" style="border-radius:10px;">
                                <div
                                    style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;">
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3>History</h3>
                                        <div>
                                           

                                            <i class="fas fa-ellipsis-h"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- End Contacts and Activities -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.savenotes').click(function () {
                let notes = $('.notesinput').val();
                let leadid = $('#leadid').val();

                $.ajax({
                    url: "{{ route('leads.savenotes') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        leadid: leadid,
                        notes: notes
                    },
                    success: function (response) {
                        load_notes(leadid, '');
                        $('.notesinput').val("");
                    },
                    error: function (error) {
                        $('.showalert').html("<h6 class='text-danger'>" + error.responseJSON.message + "</h6>");
                    }
                });
            });

            function load_notes(leadId, page) {
                let url = "{{ route('leads.notes', ['lead' => '__LEAD_ID__']) }}".replace('__LEAD_ID__', leadId) + `?page=${page}`;

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        if (page === '') {
                            $('.appendnotes').empty();
                        } else {
                            $('#load-more').remove();
                        }
                        $('.appendnotes').append(response);

                        if ($(response).filter('.note').length < 10) {
                            $('#load-more').remove();
                        }
                    },
                    error: function () {
                        alert('Could not load more notes.');
                    }
                });
            }

            load_notes("{{ $lead->id }}", '');

            $(document).on('click', '#load-more', function () {
                let button = $(this);
                let page = button.data('page');
                load_notes("{{ $lead->id }}", page);
            });
        });
    </script>
@endpush

@include('layouts.footer')