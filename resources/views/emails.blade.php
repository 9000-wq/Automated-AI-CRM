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
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #emailTable tr:hover td {
        background-color: #f8f9fa;
    }

    .email-preview {
        display: block;
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .modal-toggle {
        display: none;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        overflow-y: auto;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* .modal-toggle:checked ~ .modal-overlay {
        display: flex;
    } */


        
    #compose-modal-toggle:checked ~ .modal-overlay.compose-modal {
    display: flex;
    }

    /* View modal only */
    #view-modal-toggle:checked ~ .modal-overlay.view-modal {
        display: flex;
    }

    .modal-box {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        width: 900px;
        max-width: 95%;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        position: relative;
        animation: slideIn 0.3s ease-out;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .modal-box.view-modal {
        width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    @keyframes slideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 1.8rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #6c757d;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #343a40;
    }

    .modal-box h2 {
        font-size: 1.8rem;
        color: #1a202c;
        margin-bottom: 20px;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
    }

    .email-controls {
        display: flex;
        gap: 12px;
        margin-bottom: 25px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-send {
        background: #1a73e8;
        color: white;
        border: none;
        box-shadow: 0 2px 8px rgba(26, 115, 232, 0.3);
    }

    .btn-send:hover {
        background: #1557b0;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #3c4043;
        border: 1px solid #dadce0;
    }

    .btn-secondary:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }

    .email-fields {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .parallel-fields {
        display: flex;
        gap: 20px;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .field-group label {
        margin-bottom: 8px;
        font-weight: 500;
        color: #2d3748;
        font-size: 0.9rem;
    }

    .field-group input,
    .field-group select,
    .field-group textarea {
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
        outline: none;
        border-color: #1a73e8;
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.15);
        background: #ffffff;
    }

    .field-group input[readonly] {
        background: #e9ecef;
        cursor: default;
    }

    .field-group textarea {
        min-height: 250px;
        resize: vertical;
    }

    .view-modal .field-group textarea,
    .view-modal .email-view-content {
        min-height: 150px;
        max-height: 300px;
        overflow-y: auto;
    }

    .required::after {
        content: " *";
        color: #e53e3e;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .alert-success {
        color: #52e273ff;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .refresh-btn {
        margin-left: 12px;
        cursor: pointer;
        background: #f8f9fa;
        border: 1px solid #dadce0;
        border-radius: 6px;
        padding: 8px 12px;
        transition: all 0.2s ease;
    }

    .refresh-btn:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }

    .provider-badge {
        background: #28a745;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-left: 10px;
    }

    .email-view-content {
        white-space: pre-wrap;
        word-break: break-word;
        padding: 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.95rem;
        color: #2d3748;
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
                    <li class="mb-2"><a href="{{ route('emails.sent') }}"><i class="bi bi-send me-2"></i> Sent</a></li>
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
                        <label for="compose-modal-toggle" class="btn"
                            style="background-color: #1a73e8; color: #fff; border: none; cursor: pointer;">Compose</label>
                    </div>
                </div>

                <!-- Email List -->
                <main class="content">
                    <div class="container-fluid p-0">
                        <h1 class="h3 mb-3" style="margin-left: -25px;"><strong>Emails</strong></h1>

                        <div class="card" style="border-radius: 10px; width: 100%; margin-left: -25px;">
                            <div
                                style="width: 100%; background-color: #1a73e8; height: 10px; border-radius: 10px 10px 0 0;">
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
        <input type="checkbox" id="compose-modal-toggle" class="modal-toggle">

        <!-- Compose Modal -->
        <div class="modal-overlay compose-modal">
            <div class="modal-box">
                <label for="compose-modal-toggle" class="modal-close">&times;</label>
                <h2>Compose Email</h2>
                <div class="email-controls">
                    <button class="btn btn-send" type="button" onclick="sendEmail()">Send</button>
                    <label for="compose-modal-toggle" class="btn btn-secondary">Cancel</label>
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

        <!-- Hidden Checkbox for View Email Modal -->
        <input type="checkbox" id="view-modal-toggle" class="modal-toggle">

        <!-- View Email Modal -->
        <div class="modal-overlay view-modal">
            <div class="modal-box">
                <label for="view-modal-toggle" class="modal-close">&times;</label>
                <h2>Email Details</h2>
                <div class="email-fields">
                    <div class="field-group">
                        <label>From</label>
                        <input type="text" id="viewEmailFrom" readonly>
                    </div>
                    <div class="field-group">
                        <label>Subject</label>
                        <input type="text" id="viewEmailSubject" readonly>
                    </div>
                    <div class="field-group">
                        <label>Date</label>
                        <input type="text" id="viewEmailDate" readonly>
                    </div>
                    <div class="field-group">
                        <label>Body</label>
                        <div id="viewEmailBody" class="email-view-content"></div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="alert alert-warning">
            <p>No email account connected. Please connect your Gmail account.</p>
            <a href="{{ route('google.redirect') }}" class="btn btn-danger">Connect Gmail</a>
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
    var perPage = 10;

    $(document).ready(function () {
        emailTable = $('#emailTable').DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            ajax: {
                url: "{{ route('emails') }}",
                type: "GET",
                data: function (d) {
                    d.pageToken = pageToken;
                    d.page = currentPage;
                    d.perPage = perPage;
                },
                dataSrc: function (json) {
                    pageToken = json.nextPageToken || '';
                    return json.data || [];
                }
            },
            columns: [
                { data: 'from' },
                {
                    data: 'subject',
                    render: data => (data ? String(data) : '(No Subject)')
                },
                {
                    data: 'body_full',
                    render: function (data, type, row) {
                        const preview = row.body_preview || '';
                        return `<span class="email-preview">${preview}</span>`;
                    }
                },
                {
                    data: 'date',
                    render: function (data) {
                        if (!data) return 'N/A';
                        const dateStr = typeof data === 'string' ? data : data.date;
                        return dateStr || 'N/A';
                    }
                }
            ],
            pageLength: perPage,
            ordering: true,
            order: [[3, 'desc']],
            searching: false,
            rowCallback: function(row, data) {
                $(row).on('click', function() {
                    // Populate view modal with email data
                    $('#viewEmailFrom').val(data.from || 'N/A');
                    $('#viewEmailSubject').val(data.subject || '(No Subject)');
                    $('#viewEmailDate').val(data.date || 'N/A');
                    $('#viewEmailBody').html(data.body_full || data.body_preview || 'No content available');
                    $('#view-modal-toggle').prop('checked', true);
                });
            }
        });
    });

    function updatePaginationControls() {
        $('.previous.paginate_button').off('click').on('click', function () {
            if (currentPage > 1) {
                currentPage--;
                emailTable.ajax.reload();
            }
        });

        $('.next.paginate_button').off('click').on('click', function () {
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
                        document.getElementById('compose-modal-toggle').checked = false;
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

        // Close modals when clicking outside
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('modal-overlay')) {
                document.getElementById('compose-modal-toggle').checked = false;
                document.getElementById('view-modal-toggle').checked = false;
            }
        });
    });
</script>

@include('layouts.footer')