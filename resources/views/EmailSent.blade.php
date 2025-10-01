@include('layouts.header')

<link rel="stylesheet" 
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    .sidebar-active {
        background-color: #e9ecef;
        border-radius: 5px;
        font-weight: bold;
    }

    /* Fix table cell overflow */
    table.dataTable td {
        white-space: normal !important;   /* Allow wrapping */
        word-break: break-word;           /* Break long words */
        max-width: 250px;                 /* Limit column width */
    }
</style>

<div class="container-fluid py-3">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 border-end bg-light vh-100">
            <h5 class="fw-bold mb-3">Emails</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><a href="{{ route('emails') }}"><i class="bi bi-inbox me-2"></i> Inbox</a></li>
                <!-- <li class="mb-2"><a href="#"><i class="bi bi-star me-2"></i> Important</a></li> -->
                <li class="mb-2"><a href="{{ route('emails.sent') }}" class="sidebar-active"><i class="bi bi-send me-2"></i> Sent</a></li>
                <!-- <li class="mb-2"><a href="#"><i class="bi bi-person-badge me-2"></i> My Personal</a></li>
                <li class="mb-2"><a href="#"><i class="bi bi-briefcase me-2"></i> Sales</a></li>
                <li class="mb-2"><a href="#"><i class="bi bi-archive me-2"></i> Archive</a></li>
                <li class="mb-2"><a href="#"><i class="bi bi-file-earmark-text me-2"></i> Drafts</a></li>
                <li class="mb-2"><a href="#"><i class="bi bi-trash me-2"></i> Trash</a></li> -->
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card" style="border-radius: 10px;">
                <div style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;"></div>
                <div class="card-body">
                    <h1 class="mb-3">Sent Emails</h1>

                    <table id="emailsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>To</th>
                                <th>CC</th>
                                <th>Subject</th>
                                <th>Body</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#emailsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('emails.sent') }}",
        columns: [
            { data: 'to', name: 'to' },
            { data: 'cc', name: 'cc' },
            { data: 'subject', name: 'subject' },
            { 
                data: 'body', 
                name: 'body',
                render: function(data, type, row) {
                    // Display first 100 chars with tooltip for full content
                    if (data) {
                        return `<span title="${data}">${data.substring(0, 100)}...</span>`;
                    }
                    return '';
                }
            }
        ],
        pageLength: 10
    });
});
</script>

@include('layouts.footer')
