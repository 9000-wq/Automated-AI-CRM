@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Users List </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                       
                                        <a href="{{ route('users.create') }}" style="font-size: 20px;font-weight: 600;">Create User</a>

                                        @if(session('success'))
                                            <p style="text-align: right;font-size: large;color: #3b65ea;">{{ session('success') }}</p>
                                        @endif

                                            <div>
                                                
                                                <table class="table table-striped data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th></th>
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

					

					

				</div>
			</main>

@push('scripts') 
<script type="text/javascript">
  $(function () {
        
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.index') }}",
        columns: [
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'user_role', name: 'user_role'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });


    $(document).on('click','.deletebtn',function(){
       let id= $(this).attr('id');
       
       swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this user!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {

            const url = "{{route('users.destroy')}}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                    userid:id
                },
                success: function (response) {

                    swal("Poof! Your user file has been deleted!", {
                    icon: "success",
                    });
                    location.reload(); // Reload or remove row dynamically
                },
            })

            
        }
        });

     })


        
  });
</script>
@endpush
@include('layouts.footer')