@include('layouts.header')



			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>AI Emails</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                                                    

                                                        @if(session('success'))
                                                            <div class="alert alert-success" style="float: right;color: #3b65ea;font-weight: 500;">{{ session('success') }}</div>
                                                        @endif

                                                        <table class="table table-striped data-table" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Contact Name</th>
                                                                    <th>Lead Name</th>
                                                                    <th>Content</th>
                                                                    <th>Subject</th>
                                                                    <th>Status</th>
                                                                    <th>Opened At</th>
                                                                    <th>Clicked At</th>
                                                                    <th>Replied At</th>
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
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('AiEmails') }}",
        columns: [
            {data: 'contactname', name: 'contactname'},
            {data: 'leadname', name: 'leadname'},
            {data: 'content', name: 'content'},
            {data: 'subject', name: 'subject'},
            {data: 'status', name: 'status'},
            {data: 'opened_at', name: 'opened_at'},
            {data: 'clicked_at', name: 'clicked_at'},
            {data: 'replied_at', name: 'replied_at'},
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





 })
</script>
@endpush

@include('layouts.footer')
