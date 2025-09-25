@include('layouts.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    .sidebar-active {
        background-color: #e9ecef;
        border-radius: 5px;
        font-weight: bold;
    }

    #emailTable td {
        white-space: normal !important;
        word-break: break-word;
        max-width: 250px;
    }

    .email-preview {
        display: block;
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

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

    .alert {
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .alert-success {
        /* background: #d4edda; */
        color: #3bbe5a;
        /* border: 1px solid #c3e6cb; */
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .refresh-btn {
        margin-left: 10px;
        cursor: pointer;
        background: #f1f3f4;
        border: 1px solid #dadce0;
        border-radius: 4px;
        padding: 5px 10px;
    }

    .provider-badge {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-left: 10px;
    }
</style>

<div class="container-fluid py-3">
    @if(!empty($accounts) && isset($accounts[0]))
        @php
            $provider = $accounts[0]->provider;
        @endphp

        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <div>
                ✅ Connected as <strong>{{ $accounts[0]->email }}</strong>
                <span class="provider-badge">{{ strtoupper($provider) }}</span>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 border-end bg-light vh-100">
                <h5 class="fw-bold mb-3">Emails</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#"><i class="bi bi-inbox me-2"></i> Inbox</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-star me-2"></i> Important</a></li>
                    <li class="mb-2"><a href="{{ route('emails.sent') }}"><i class="bi bi-send me-2"></i> Sent</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-person-badge me-2"></i> My Personal</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-briefcase me-2"></i> Sales</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-archive me-2"></i> Archive</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-file-earmark-text me-2"></i> Drafts</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-trash me-2"></i> Trash</a></li>
                </ul>
            </div>

            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group w-50">
                        <select class="form-select" style="max-width: 80px;">
                            <option>All</option>
                            <option>Inbox</option>
                            <option>Important</option>
                        </select>
                        <input type="text" class="form-control" placeholder="Search emails...">
                        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>

                    <div>
                        <button class="refresh-btn" onclick="refreshInbox()" title="Refresh Inbox">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <label for="modal-toggle" class="btn"
                            style="background-color: #3b65ea; color: #fff; border: none; cursor: pointer;">Compose</label>
                    </div>
                </div>

                <!-- Email List -->
                <main class="content">
                    <div class="container-fluid p-0">
                        <h1 class="h3 mb-3" style="margin-left: -25px;"><strong>Emails</strong></h1>

                        <div class="card" style="border-radius: 10px; width: 100%; margin-left: -25px;">
                            <div
                                style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;">
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="emailTable">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>Subject</th>
                                            <th>Body</th>
                                            <th>Date Sent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Hidden Checkbox for Compose Modal -->
        <input type="checkbox" id="modal-toggle">

        <!-- Compose Modal -->
        <div class="modal-overlay">
            <div class="modal-box">
                <label for="modal-toggle" class="modal-close">&times;</label>
                <h2>Compose Email</h2>
                <div class="email-controls">
                    <button class="btn btn-send" type="button" onclick="sendEmail()">Send</button>
                    <label for="modal-toggle" class="btn btn-secondary">Cancel</label>
                </div>

                <div class="messageinfo"></div>

                <form id="sendleademails">
                    <div class="email-fields">
                        <div class="parallel-fields">
                            <div class="field-group">
                                <label class="required">To</label>
                                <input type="email" id="emailTo" placeholder="Enter recipient email" required>
                            </div>
                            <div class="field-group">
                                <label>CC</label>
                                <input type="email" id="emailCC">
                            </div>
                        </div>
                        <div class="parallel-fields">
                            <div class="field-group">
                                <label class="required">Subject</label>
                                <input type="text" id="emailSubject" placeholder="Email subject" required>
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Body</label>
                            <textarea id="emailBodyText"></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @else
        {{-- No Account Connected --}}
        <div class="alert alert-warning text-center">
            <div class="row mt-3">
                <div class="col-12 mb-2">
                    <a href="{{ route('google.redirect') }}" class="btn btn-danger w-100">
                        <i class="bi bi-google"></i> Connect Gmail
                    </a>
                </div>
                {{-- <div class="col-12">
                    <a href="{{ route('imap.connect') }}" class="btn btn-primary w-100">
                        <i class="bi bi-envelope"></i> Connect IMAP
                    </a>
                </div> --}}
            </div>
        </div>

    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTable with server-side processing
    var emailTable;
    var pageToken = '';
    var currentPage = 1;
    var perPage = 10; // <- added
    $(document).ready(function () {
        emailTable = $('#emailTable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            ajax: {
                url: "{{ route('emails') }}",
                type: "GET",
                data: function (d) {
                    d.pageToken = pageToken;  // Gmail pagination
                    d.page = currentPage;     // IMAP pagination
                    d.perPage = perPage;      // ← now defined
                },


                dataSrc: function (json) {
                    pageToken = json.nextPageToken || '';
                    return json.data || [];
                }
            },
            columns: [
                { data: 'from' },                                       // stays as-is
                {
                    data: 'subject',
                    render: data => (data ? String(data) : '(No Subject)') // force string
                },
                {
                    data: 'body_full',
                    render: function (data, type, row) {
                        const preview = row.body_preview || '';
                        const fullBody = data || preview;

                        // Ensure unique id per row
                        const id = 'email-body-' + row.id; // make sure row.id exists and is unique

                        // If full body is same as preview, don't show "See More"
                        if (!fullBody || fullBody === preview) return preview;

                        // Show preview + hidden full body + toggle link
                        return `
            <span id="${id}-preview">${preview}</span>
            <span id="${id}-full" style="display:none;">${fullBody}</span>
            <a href="javascript:void(0)" onclick="toggleBody('${id}')" style="color:#1a73e8; margin-left:5px;">See More</a>
        `;
                    }
                }
                ,
                {
                    data: 'date',
                    render: function (data) {
                        if (!data) return 'N/A';
                        // if it is already a string, use it; if it is the object, use data.date
                        const dateStr = typeof data === 'string' ? data : data.date;
                        return dateStr || 'N/A';
                    }
                }
            ],
            pageLength: perPage,
            ordering: true,
            order: [[3, 'desc']],
            searching: false,
        });
    });
    function toggleBody(id) {
        const preview = document.getElementById(id + '-preview');
        const full = document.getElementById(id + '-full');
        const link = full.nextElementSibling || preview.nextElementSibling; // the <a> tag

        if (!full || !preview || !link) return;

        if (full.style.display === 'none') {
            full.style.display = 'inline';
            preview.style.display = 'none';
            link.textContent = 'See Less';
        } else {
            full.style.display = 'none';
            preview.style.display = 'inline';
            link.textContent = 'See More';
        }
    }

    function updatePaginationControls() {
        $('.previous.paginate_button').off('click').on('click', function () {
            if (currentPage > 1) {
                currentPage--;
                emailTable.ajax.reload();
            }
        });

        $('.next.paginate_button').off('click').on('click', function () {
            // For Gmail, check nextPageToken, for IMAP check total pages
            let totalPages = Math.ceil(emailTable.page.info().recordsTotal / perPage);
            if (pageToken || currentPage < totalPages) {
                currentPage++;
                emailTable.ajax.reload();
            }
        });
    }


    // Function to refresh the inbox
    function refreshInbox() {
        pageToken = '';
        currentPage = 1;
        emailTable.ajax.reload();

        const refreshBtn = document.querySelector('.refresh-btn');
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise spinner"></i>';
        refreshBtn.disabled = true;

        setTimeout(() => {
            refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            refreshBtn.disabled = false;
        }, 2000);
    }


    // Send Email
    function sendEmail() {
        $('.messageinfo').html('');

        let to = $('#emailTo').val().trim();
        let subject = $('#emailSubject').val().trim();
        let body = $('#emailBodyText').val().trim();
        let cc = $('#emailCC').val().trim();

        if (!to || !subject) {
            $('.messageinfo').html('<div class="alert alert-danger">Please fill all required fields (To and Subject).</div>');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(to)) {
            $('.messageinfo').html('<div class="alert alert-danger">Please enter a valid email address for the recipient.</div>');
            return;
        }

        if (cc && !emailRegex.test(cc)) {
            $('.messageinfo').html('<div class="alert alert-danger">Please enter a valid email address for CC.</div>');
            return;
        }

        const sendButton = document.querySelector('.btn-send');
        const originalText = sendButton.textContent;
        sendButton.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Sending...';
        sendButton.disabled = true;

        $.ajax({
            url: "{{ route('emails.send') }}",
            method: "POST",
            data: {
                to: to,
                cc: cc,
                subject: subject,
                body: body,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                sendButton.innerHTML = originalText;
                sendButton.disabled = false;

                if (res.status === 'success') {
                    $('.messageinfo').html('<div class="alert alert-success">' + res.message + '</div>');
                    $('#sendleademails')[0].reset();

                    // Refresh inbox
                    pageToken = '';
                    currentPage = 1;
                    emailTable.ajax.reload();

                    setTimeout(function () {
                        document.getElementById('modal-toggle').checked = false;
                        $('.messageinfo').html('');
                    }, 2000);
                } else {
                    $('.messageinfo').html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            },
            error: function (xhr) {
                sendButton.innerHTML = originalText;
                sendButton.disabled = false;

                let errorMessage = 'Failed to send email';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }

                $('.messageinfo').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    }

    // Compose form handler
    document.addEventListener('DOMContentLoaded', function () {
        const composeForm = document.getElementById('sendleademails');
        if (composeForm) {
            composeForm.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                document.getElementById('modal-toggle').checked = false;
            }
        });
    });
</script>

@include('layouts.footer')