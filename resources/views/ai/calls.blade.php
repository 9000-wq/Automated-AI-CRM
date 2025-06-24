@include('layouts.header')



			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>AI Calls</strong></h1>

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
                                                                    <th>Direction</th>
                                                                    <th>Transcript</th>
                                                                    <th>Sentiment</th>
                                                                    <th>Outcome</th>
                                                                    <th>Audio link</th>
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
        ajax: "{{ route('AiCalls') }}",
        columns: [
            {data: 'contactname', name: 'contactname'},
            {data: 'leadname', name: 'leadname'},
            {data: 'direction', name: 'direction'},
            {data: 'transcript', name: 'transcript'},
            {data: 'sentiment', name: 'sentiment'},
            {data: 'outcome', name: 'outcome'},
            {data: 'audio_link', name: 'audio_link'},
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
