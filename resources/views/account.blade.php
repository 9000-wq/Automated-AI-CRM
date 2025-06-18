@include('layouts.header')

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Account List</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">

                                    <a href="{{ route('account') }}" style="font-size: 20px;font-weight: 600;">Create New Account</a>

                                    @if(session('success'))
                                        <p style="text-align: right;font-size: large;color: #3b65ea;">{{ session('success') }}</p>
                                    @endif

                                    <table class="table table-striped data-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Industry</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Website</th>
                                                <th>Address</th>
                                                <th>City</th>
                                                <th>Country</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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

@push('scripts')
<script type="text/javascript">
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('home.account') }}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'industry', name: 'industry'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'website', name: 'website'},
                {data: 'address', name: 'address'},
                {data: 'city', name: 'city'},
                {data: 'country', name: 'country'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            dom: 'Bfrtip',
            buttons: [
                {extend: 'csv', className: 'btn btn-sm btn-outline-primary', text: 'Export CSV'},
                {extend: 'excel', className: 'btn btn-sm btn-outline-success', text: 'Export Excel'},
                {extend: 'pdf', className: 'btn btn-sm btn-outline-danger', text: 'Export PDF'},
                {extend: 'print', className: 'btn btn-sm btn-outline-secondary', text: 'Print'}
            ]
        });

        $(document).on('click', '.deletebtn', function () {
            let id = $(this).attr('id');
            let myurl = "{{ route('accounts.destroy', ':id') }}";
            myurl = myurl.replace(':id', id);

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this account!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: myurl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            swal("Account deleted successfully!", { icon: "success" });
                            table.ajax.reload();
                        },
                        error: function () {
                            swal("Something went wrong!", { icon: "error" });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@include('layouts.footer')
