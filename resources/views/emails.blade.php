@include('layouts.header')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
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
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .email-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .refresh-btn {
        margin-left: 10px;
        cursor: pointer;
        background: #f1f3f4;
        border: 1px solid #dadce0;
        border-radius: 4px;
        padding: 5px 10px;
    }
</style>

@if(!empty($accounts) && isset($accounts[0]) && $accounts[0]->provider === 'gmail')
    <p class="text-success">✅ Connected as <strong>{{ $accounts[0]->email }}</strong></p>

    <div class="container-fluid py-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 border-end bg-light vh-100">
                <h5 class="fw-bold mb-3">Emails</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-danger fw-bold"><i class="bi bi-grid-3x3-gap me-2"></i> All</a>
                    </li>
                    <li class="mb-2"><a href="#"><i class="bi bi-inbox me-2"></i> Inbox</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-star me-2"></i> Important</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-send me-2"></i> Sent</a></li>
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
                        <input type="text" class="form-control" placeholder="Email Address">
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
    </div>

    <!-- Hidden Checkbox -->
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
    <a href="{{ route('google.redirect') }}" class="btn btn-danger">Connect Gmail</a>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTable with server-side processing
    var emailTable;
    var pageToken = '';
    var currentPage = 1;

    $(document).ready(function () {
        emailTable = $('#emailTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('emails') }}",
                data: function (d) {
                    d.pageToken = pageToken;
                    d.page = currentPage;
                }
            },
            columns: [
                { data: 'from', name: 'from' },
                { data: 'subject', name: 'subject' },
                { 
                    data: 'body_preview', 
                    name: 'body_preview',
                    render: function(data, type, row) {
                        return '<span class="email-preview">' + data + '</span>';
                    }
                },
                { 
                    data: 'date', 
                    name: 'date',
                    render: function(data, type, row) {
                        if (data) {
                            return new Date(data).toLocaleString();
                        }
                        return 'N/A';
                    }
                }
            ],
            paging: true,
            pageLength: 10,
            ordering: true,
            order: [[3, 'desc']], // Sort by date column (index 3) in descending order
            searching: false,
            language: {
                emptyTable: 'No emails found'
            },
            drawCallback: function (settings) {
                var api = this.api();
                var data = api.ajax.json();
                pageToken = data.nextPageToken || '';
                
                // Update paging controls
                $('.previous.paginate_button').off('click').on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        api.ajax.reload();
                    }
                });
                
                $('.next.paginate_button').off('click').on('click', function() {
                    if (pageToken) {
                        currentPage++;
                        api.ajax.reload();
                    }
                });
            }
        });
    });
    
    // Function to refresh the inbox
    function refreshInbox() {
        pageToken = '';
        currentPage = 1;
        emailTable.ajax.reload();

        const refreshBtn = document.querySelector('.refresh-btn');
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        refreshBtn.classList.add('refreshing');
        
        setTimeout(() => {
            refreshBtn.classList.remove('refreshing');
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

                    // ✅ Always reload inbox from page 1
                    pageToken = '';
                    currentPage = 1;
                    emailTable.ajax.reload(null, false);

                    setTimeout(function() {
                        document.getElementById('modal-toggle').checked = false;
                    }, 1000);
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
    document.addEventListener('DOMContentLoaded', function() {
        const composeForm = document.getElementById('sendleademails');
        if (composeForm) {
            composeForm.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                }
            });
        }
    });
</script>

@include('layouts.footer')