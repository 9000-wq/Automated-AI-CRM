@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Assign Plan</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                     
                                            <form id="planform">
												@csrf
												<div class="row">
													<div class="col-md-6">
														<label for="plnaname" class="form-label">Plan name</label>
														<select name="plnaname" id="plnaname" class="form-control- form-select mb-4 autoselect" >
															<option value="" selected>Select an option</option>
															@foreach($plans as $plan)
															<option value="{{$plan->id}}" data-price="{{$plan->price}}" data-billing="{{$plan->billing_cycle}}">{{$plan->name}}</option>
															@endforeach
														</select>
													</div>
													<input type="hidden" name="companyid" class="form-control" value="{{$companyid}}">
													<div class="col-md-6">
														<label for="price">Price</label>
														<input type="number" class="form-control mb-4" id="price" name="price">
													</div>
												</div>

												<div class="row">
													<div class="col-md-6">
														<label for="startdate">Start Date</label>
														<input type="date" name="startdate" id="startdate" class="form-control mb-4">
													</div>
													<div class="col-md-6">
														<label for="enddate">End Date</label>
														<input type="date" name="enddate" id="enddate" class="form-control mb-4">
													</div>
												</div>

												<button class="btn btn-primary savebtn" type="button">Save</button>
												<div class="showalert mt-3"></div>
                                            </form>
                                            
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
 $(document).ready(function(){
	$('.autoselect').change(function(){
        let selectedOption = $(this).find('option:selected');
        let price = selectedOption.data('price');
        let billing_cycle = selectedOption.data('billing');
       
        $('#price').val(price);

		let today = new Date();
		let endDate = new Date(today); 

		if (billing_cycle === 'yearly') {
			endDate.setFullYear(endDate.getFullYear() + 1);
		} else if (billing_cycle === 'monthly') {
			endDate.setMonth(endDate.getMonth() + 1);
		}

		let formattedStartDate = today.toISOString().split('T')[0];
		let formattedEndDate = endDate.toISOString().split('T')[0];
		$('#startdate').val(formattedStartDate);
		$('#enddate').val(formattedEndDate);

    });

	$('.savebtn').click(function(){
		let form = document.getElementById('planform');
		let data = new FormData(form);

		$.ajax({
			url:"{{route('savenewplan')}}",
			data:data,
			type:"Post",
			processData: false,
			cache: false,
			contentType: false,
		}).done(function(response){
            $('.showalert').html("<h6 class='text-primary'>"+response.message+"</h6>");
			window.location="{{route('manageprices')}}";
		}).fail(function(error){
			$('.showalert').html("<h6 class='text-danger'>"+error.responseJSON.message+"</h6>")
		})

	})
 })
</script>
@endpush
@include('layouts.footer')