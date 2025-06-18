@include('layouts.header')



			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Leads Management </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                                                    
                                                        <a href="{{ route('leads.create') }}" class="btn btn-primary mb-3">Create New Lead</a>

                                                        @if(session('success'))
                                                            <div class="alert alert-success" style="float: right;color: #3b65ea;font-weight: 500;">{{ session('success') }}</div>
                                                        @endif

                                                        <table class="table table-striped data-table" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Source</th>
                                                                    <th>Redrence</th>
                                                                    <th>Status</th>
                                                                    <th>Assigned To</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                               
                                                            </tbody>
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
<script>
 $(document).ready(function(){
   
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('leads.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'source', name: 'source'},
            {data: 'case_ref', name: 'case_ref'},
            {data: 'status', name: 'status'},
            {data: 'assigned_user_name', name: 'assigned_user_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],

        dom: 'Bfrtip', // Show buttons
        buttons: [
            {
                extend: 'csv',
                className: 'btn btn-sm btn-outline-primary',
                text: 'Export CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-outline-success',
                text: 'Export Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-outline-danger',
                text: 'Export PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-outline-secondary',
                text: 'Print'
            }
        ]


    });


    $(document).on('click','.deletebtn',function(){
       let id= $(this).attr('id');
       
       swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this lead!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {

            const url = "{{route('leads.destroy')}}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                    leadid:id
                },
                success: function (response) {

                    swal("Poof! Your Lead has been deleted!", {
                    icon: "success",
                    });
                    location.reload(); // Reload or remove row dynamically
                },
            })

            
        }
        });

     })



 })
</script>
@endpush

@include('layouts.footer')
