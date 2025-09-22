@include('layouts.header')

<style>
     .hidden { 
      display: none;
    }
</style>
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Edit Company Info</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                <form action="{{ route('updatecompanyinfo', $companyinfo->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="companyName" class="form-label">Company Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control mt-1" value="{{$companyinfo->company_name}}" name="companyName" id="companyName">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control mt-1" value="{{$companyinfo->company_email}}" name="company_email" id="company_email">
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-md-6">
                                                            <label for="companyAddress" class="form-label">Company Address <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control mt-1" value="{{$companyinfo->company_address}}" name="companyAddress" id="companyAddress">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control mt-1" value="{{$companyinfo->country}}" name="country" id="country">
                                                        </div>
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-md-6">
                                                            <label for="companyDescription" class="form-label">Company Description <span class="text-danger">*</span></label>
                                                            <textarea  class="form-control mt-1" name="companyDescription" id="companyDescription">{{$companyinfo->company_description}}</textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="priceGuidelines" class="form-label">Price Guidelines <span class="text-danger">*</span></label>
                                                            <textarea  class="form-control mt-1" name="priceGuidelines" id="priceGuidelines">{{$companyinfo->price_guidelines}}</textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="set_a_prompt" class="form-label">Set a Prompt <span class="text-danger">*</span></label>
                                                            <textarea  class="form-control mt-1" name="set_a_prompt" id="set_a_prompt">{{$companyinfo->set_a_prompt}}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <h4>Select Business Type:</h4>
                                                        <label class="mb-2">
                                                            <input type="radio"  style="height: 16px !important;" class="form-check-input mb-2" name="business_type" value="service"> Service
                                                        </label>
                                                        <label style="margin-left: 40px;">
                                                            <input type="radio"  style="height: 16px !important;" class="form-check-input mb-2"  name="business_type" value="product"> Product
                                                        </label>
                                                    </div>

                                                    <div id="serviceBox" class="textarea-box hidden">
                                                        <label>Service Knowledge:</label><br>
                                                        <textarea rows="3" cols="40" name="serviceKnowledge" id="serviceKnowledge" class="form-control mb-4 mt-2">{{ $companyinfo->business_type == 'service' ? $companyinfo->bussiness_knowledge : '' }}</textarea>
                                                    </div>

                                                    <div id="productBox" class="textarea-box hidden">
                                                        <label>Product Knowledge:</label><br>
                                                        <textarea rows="3" cols="40" name="productKnowledge" id="productKnowledge" class="form-control mb-4 mt-2">{{ $companyinfo->business_type == 'product' ? $companyinfo->bussiness_knowledge : '' }}</textarea>
                                                    </div>

                                                    <button  type="submit" class="btn btn-primary">Update Info</button>
                                                     @if ($errors->any())
                                                        <div class="alert alert-danger mt-4 ">
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                    <li class="text-danger">{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

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
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>        
<script type="text/javascript">
 


    document.querySelectorAll('input[name="business_type"]').forEach((radio) => {
        radio.addEventListener('change', function () {
        if (this.value === "service") {
            document.getElementById("serviceBox").classList.remove("hidden");
            document.getElementById("productBox").classList.add("hidden");
        } else if (this.value === "product") {
            document.getElementById("productBox").classList.remove("hidden");
            document.getElementById("serviceBox").classList.add("hidden");
        }
        });
    });

    function selectradio() {
        let radio = "{{ $companyinfo->business_type }}"; // Value from backend

        let target = document.querySelector(`input[name="business_type"][value="${radio}"]`);
        if (target) {
            target.checked = true; // mark it checked
            target.dispatchEvent(new Event('change')); // 👈 manually trigger change event
        }
    }

    selectradio()

</script>
@endpush
@include('layouts.footer')
