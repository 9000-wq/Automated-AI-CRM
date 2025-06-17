@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Client Plans History</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                            <div class=" m-4">
                                                
                                                    @foreach($plans as $plan)
                                                    <div class="row">
                                                    <div class="col-md-12 mb-4">
                                                                @php
                                                                    $isExpired = \Carbon\Carbon::parse($plan->end_date)->isPast();
                                                                @endphp
                                                                <h1 class="card-title" style="font-size: large;color: black;">Plan Name: {{ $plan->name }}</h1>
                                                                <h5 class="card-subtitle mb-2  mt-3" style="font-size: medium;">Plan Type: {{ ucfirst($plan->billing_cycle) }}  @if (!$isExpired)<button class="btn btn-danger btn-lg deletebtn" id="{{$plan->id}}" style="float:right;"><i class="far fa-trash-alt"></i></button>@endif</h5>
                                                                <p style="font-size: medium;"><strong>Price:</strong> ${{ $plan->price }}</p>
                                                                <p style="font-size: medium;"><strong>Start:</strong> {{ date('d-m-Y',strtotime($plan->start_date)) }}</p>
                                                                <p style="font-size: medium;" ><strong>End:</strong> {{ date('d-m-Y',strtotime($plan->end_date))  }}</p>
                                                               
                                                                <span  style="font-size: medium;" class="badge bg-{{ $isExpired ? 'danger' : 'success' }}">
                                                                    {{ $isExpired ? 'Expired' : 'Active' }}
                                                                </span>
                                                          
                                                    </div>
                                                    </div>
                                                    <hr>
                                                    @endforeach
                                                
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
 
$(document).on('click','.deletebtn',function(){
    let id=$(this).attr('id');   

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this plan!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {

            const url = "{{route('deletenewplan')}}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
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

</script>
@endpush
@include('layouts.footer')