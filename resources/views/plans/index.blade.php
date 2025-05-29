@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Plans Management </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                
                                                <a href="{{ route('plans.create') }}" style="font-size: large;font-weight: 600;"> Create New Plan</a>

                                                @if (session('success'))
                                                    <p style="text-align: right;font-size: large;color: #3b65ea;">{{ session('success') }}</p>
                                                @endif


                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Description</th>
                                                            <th>Price</th>
                                                            <th>Billing Cycle</th>
                                                            <th>Features</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($plans as $plan)
                                                        <tr>
                                                            <td>{{$plan->name}}</td>
                                                            <td>{{$plan->description}}</td>
                                                            <td>${{$plan->price}}</td>
                                                            <td>{{ucfirst($plan->billing_cycle)}}</td>
                                                            <td>{{$plan->features}}</td>
                                                            <td><button class="btn btn-danger deletebtn" id="{{$plan->id}}" ><i class="far fa-trash-alt"></i></button>
                                                            <a class="btn btn-primary " href="{{ route('plans.edit', $plan) }} id="{{$plan->id}}" ><i class="far fa-edit"></i></a></td>
                                                        </tr>
                                                        @endforeach

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
     $('.deletebtn').click(function(){
       let id= $(this).attr('id');
       
       swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this plan!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {

            const url = "{{route('plans.destroy')}}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                    planid:id
                },
                success: function (response) {

                    swal("Poof! Your imaginary file has been deleted!", {
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