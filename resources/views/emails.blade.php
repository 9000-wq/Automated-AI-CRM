@include('layouts.header')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- ✅ DataTables CSS -->
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

                <label for="modal-toggle" class="btn"
                    style="background-color: #3b65ea; color: #fff; border: none; cursor: pointer;">
                    Compose
                </label>
            </div>

            <!-- Email List Card -->
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
                                        <th>To</th>
                                        <th>Subject</th>
                                        <th>Parent</th>
                                        <th>Date Sent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>jackadmin@espo.lo...</td>
                                        <td>Office Equipment Proposal</td>
                                        <td>Toby Bowen</td>
                                        <td>17:53</td>
                                    </tr>
                                    <tr>
                                        <td>jackadmin@espo.lo...</td>
                                        <td>Discount Case</td>
                                        <td>Discount Issue</td>
                                        <td>15:05</td>
                                    </tr>
                                    <tr>
                                        <td>Smart_T_Shop@ema...</td>
                                        <td>Special offer! 40% off for your next purchase!</td>
                                        <td>Speakers Bulk Purchase</td>
                                        <td>10:15</td>
                                    </tr>
                                    <tr>
                                        <td>Timothy Jackson</td>
                                        <td>Warranty Inquiry</td>
                                        <td>Janeville</td>
                                        <td>10:00</td>
                                    </tr>
                                    <tr>
                                        <td>roxa.beaulieu@exa...</td>
                                        <td>Price List & Catalog</td>
                                        <td>10 Printers Order & Install...</td>
                                        <td>Yesterday 11:56</td>
                                    </tr>
                                    <tr>
                                        <td>george.barrios@de...</td>
                                        <td>Business Lunch</td>
                                        <td>George Barrios</td>
                                        <td>Yesterday 09:30</td>
                                    </tr>
                                    <tr>
                                        <td>rosemarie-fortier@t...</td>
                                        <td>Payment Issue</td>
                                        <td>Payment Issue</td>
                                        <td>Yesterday 08:45</td>
                                    </tr>
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

<!-- Modal -->
<div class="modal-overlay">
    <div class="modal-box">
        <label for="modal-toggle" class="modal-close">&times;</label>
        <h2>Compose Email</h2>
        <div class="email-controls">
            <button class="btn btn-send" onclick="sendEmail()">Send</button>
            <label for="modal-toggle" class="btn btn-secondary">Cancel</label>
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
                            <option value="">Search by lead name</option>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

@push('scripts')
    <script>
        function sendEmail() {
            alert("Email sent! (hook your AJAX here)");
        }

        $(document).ready(function () {
            $('#emailTable').DataTable({
                "pageLength": 5,
                "lengthMenu": [5, 10, 20, 50],
                "searching": false

            });
        });
    </script>
@endpush

@include('layouts.footer')