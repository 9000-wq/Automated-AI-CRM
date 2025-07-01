@include('layouts.header')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('plugins/Tagging-System-Autocomplete/css/amsify.suggestags.css')}}">
<link href="{{asset('plugins/wysiwyg-editor-master/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<style>
    /* Add the same modal styles from show.blade.php */
    #modal-toggle {
        display: none;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        overflow-y: scroll;
    }

    #modal-toggle:checked~.modal-overlay {
        display: flex;
    }

    .modal-box {
        background: white;
        padding: 25px;
        border-radius: 8px;
        width: 900px;
        max-width: 90%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 1.8rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #777;
        line-height: 1;
    }

    .email-controls {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.2s;
    }

    .btn-send {
        background: #1a73e8;
        color: white;
        border: none;
    }

    .btn-send:hover {
        background: #0d62c9;
    }

    .btn-secondary {
        background: #f1f3f4;
        color: #3c4043;
        border: 1px solid #dadce0;
    }

    .btn-secondary:hover {
        background: #e8eaed;
    }

    .email-fields {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .parallel-fields {
        display: flex;
        gap: 15px;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .field-group label {
        margin-bottom: 5px;
        font-weight: 500;
        color: #3c4043;
        font-size: 0.9rem;
    }

    .field-group input,
    .field-group select,
    .field-group textarea {
        padding: 10px;
        border: 1px solid #dadce0;
        border-radius: 4px;
        font-size: 0.95rem;
        transition: border 0.2s;
    }

    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
        outline: none;
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
    }

    .field-group textarea {
        min-height: 200px;
        resize: vertical;
    }

    .required::after {
        content: " *";
        color: #d93025;
    }

    .open-modal-btn {
        padding: 10px 20px;
        background: #1a73e8;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
    }

    .open-modal-btn:hover {
        background: #0d62c9;
    }

    #fr-logo {
        display: none !important;
    }
</style>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Emails</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card" style='border-radius:10px;'>
                                <div
                                    style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;">
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('email-logs') }}" style="font-size: 20px;font-weight: 600;"
                                        onclick="event.preventDefault(); document.getElementById('modal-toggle').click();">
                                        Compose Email
                                    </a>

                                    @if(session('success'))
                                        <p style="text-align: right;font-size: large;color: #3b65ea;">
                                            {{ session('success') }}
                                        </p>
                                    @endif

                                    <table class="table table-striped data-table">
                                        <thead>
                                            <tr>
                                                <th>To</th>
                                                <th>CC</th>
                                                <th>Parent</th>
                                                <th>Subject</th>
                                                <th>Body</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Hidden checkbox toggle -->
<input type="checkbox" id="modal-toggle">

<!-- Modal overlay - Same as in show.blade.php -->
<div class="modal-overlay">
    <div class="modal-box">
        <label for="modal-toggle" class="modal-close">&times;</label>
        <h2>Compose Email</h2>

        <div class="email-controls">
            <button class="btn btn-send" onclick="sendEmail()">Send</button>
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        </div>

        <div class="messageinfo"></div>

        <form id="sendleademails">
            <div class="email-fields">
                <div class="parallel-fields">
                    <div class="field-group">
                        <label class="required">To</label>
                        <input type="text" id="emailTo" placeholder="Enter recipient email" required>
                    </div>

                    <div class="field-group">
                        <label>CC</label>
                        <input type="text" name="emailCC" id="emailCC">
                    </div>
                </div>

                <div class="parallel-fields">
                    <div class="field-group">
                        <label>Lead <span class="text-danger">*</span></label>
                        <select class="lead-select" id="emailParent">
                            <option value="">Select a lead by ID (optional)</option>
                          
                        </select>
                    </div>

                    <div class="field-group">
                        <label class="required">Subject</label>
                        <input type="text" id="emailSubject" placeholder="Email subject" required>
                    </div>
                </div>

                <div class="field-group">
                    <label>Body</label>
                    <textarea id="emailBody"></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{asset('plugins/Tagging-System-Autocomplete/js/jquery.amsify.suggestags.js')}}"></script>
    <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Froala Editor
            new FroalaEditor('#emailBody', {
                imageUploadURL: '{{route('leaduploadimage')}}',
                fileUploadURL: '{{route('leaduploadfile')}}',
                requestHeaders: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                fileAllowedTypes: [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ],
                toolbarButtons: ['bold', 'italic', 'underline', 'align', 'formatOL', 'formatUL', 'insertImage', 'insertFile', 'undo', 'redo'],
            });

            // Initialize CC tags input
            $('input[name="emailCC"]').amsifySuggestags({
                type: 'amsify',
            });

            $('.lead-select').select2({
                placeholder: 'Search by lead name',
                allowClear: true,
                ajax: {
                    url: '{{ route("ajax.leads") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term,  // ✅ clearer param name (optional)
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data.map(function (item) {
                                return {
                                    id: item.id,       // ✅ form will submit ID
                                    text: item.name    // ✅ user sees name
                                };
                            }),
                            pagination: {
                                more: (params.page * 20) < data.total
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });


            // DataTable initialization
            var table = $('.data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('email-logs-data') }}",
                columns: [
                    { data: 'email', name: 'email' },
                    { data: 'cc', name: 'cc' },
                    { data: 'parent', name: 'parent' },
                    { data: 'subject', name: 'subject' },
                    { data: 'body', name: 'body' },
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', className: 'btn btn-sm btn-outline-primary', text: 'Export CSV' },
                    { extend: 'excel', className: 'btn btn-sm btn-outline-success', text: 'Export Excel' },
                    { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', text: 'Export PDF' },
                    { extend: 'print', className: 'btn btn-sm btn-outline-secondary', text: 'Print' }
                ],
                order: [[0, 'desc']]
            });

            // Close modal when clicking outside
            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('modal-overlay')) {
                    closeModal();
                }
            });
        });

        function closeModal() {
            document.getElementById('modal-toggle').checked = false;
        }

        // Email sending function
        function sendEmail() {
            const to = document.getElementById('emailTo').value;
            const cc = document.getElementById('emailCC').value;
            const leadid = document.getElementById('emailParent').value;
            const leadname = document.getElementById('emailParent').options[
                document.getElementById('emailParent').selectedIndex
            ].text;
            const subject = document.getElementById('emailSubject').value;
            const body = document.getElementById('emailBody').value;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        

            $.ajax({
                url: "{{ route('sendleademail') }}",
                data: {
                    to: to,
                    cc: cc,
                    leadid: leadid,
                    leadname: leadname,
                    subject: subject,
                    body: body
                },
                type: "post",
            }).done(function (response) {
                document.getElementById('emailTo').value = '';
                document.getElementById('emailCC').value = '';
                document.getElementById('emailSubject').value = '';
                document.getElementById('emailBody').value = '';

                $('.messageinfo').html('<div class="text-primary">Email sent successfully.</div>');

                // Refresh the DataTable
                $('.data-table').DataTable().ajax.reload();

                closeModal();
            }).fail(function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (key, value) {
                        errorHtml += '<li class="text-danger">' + value[0] + '</li>';
                    });
                    errorHtml += '</ul></div>';
                    $('.messageinfo').html(errorHtml);
                } else {
                    $('.messageinfo').html('<div class="alert alert-danger">An error occurred.</div>');
                }
            });
        }
    </script>
@endpush

@include('layouts.footer')