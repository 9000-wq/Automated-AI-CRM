@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Create Lead </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                                                        

                                                            <form id="leadform" >
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label" for="name">Enter Lead Name <span class="text-danger">*</span></label>
                                                                        <input type="text" id="name" name="name" placeholder="Lead Name" class="form-control mb-3" required>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="form-label" for="case_ref">Enter Case Ref <span class="text-danger">*</span></label>
                                                                        <input type="text" id="case_ref" name="case_ref" placeholder="Case Ref" class="form-control mb-3"> 
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="form-label" for="source">Enter Source <span class="text-danger">*</span></label>
                                                                        <input type="text" id="source" name="source" placeholder="Source" class="form-control mb-3">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label" for="">Choose Status <span class="text-danger">*</span></label>
                                                                        <select name="status" id="status" class="form-control mb-3">
                                                                            <option>New</option>
                                                                            <option>Contacted</option>
                                                                            <option>Follow-up</option>
                                                                            <option>Converted</option>
                                                                            <option>Lost</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <label class="form-label" for="assigned_to">Please select a user</label>
                                                                        <select name="assigned_to" id="assigned_to" class="form-select form-control mb-3">
                                                                            <option value="" selected disabled>Choose an Option</option>
                                                                            @foreach($users as $user)
                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                

                                                                
                                                                
                                                                <hr>
                                                                <h4 class="mt-3">Contact Details</h4>

                                                                <div id="contacts-container">
                                                                    <div class="contact-entry mb-3">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Enter Full Name:</label>
                                                                                <input type="text" name="contacts[0][full_name]" placeholder="Full Name" class="form-control mb-3">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Enter Role:</label>
                                                                                <input type="text" name="contacts[0][role]" placeholder="Role" class="form-control mb-3">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Enter Phone no:</label>
                                                                                <input type="text" name="contacts[0][phone]" placeholder="Phone" class="form-control mb-3">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label class="form-label">Enter Email:</label>
                                                                                <input type="email" name="contacts[0][email]" placeholder="Email" class="form-control mb-3">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <label class="form-label">Enter Address:</label>
                                                                                <textarea name="contacts[0][address]" placeholder="Address" class="form-control mb-3"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <button type="button" class="btn btn-success savebtn">Save Lead</button>
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
<script>
  $(document).ready(function(){
        $('.savebtn').click(function(){
            let form=document.getElementById('leadform');
            let data = new FormData(form);

            $.ajax({
                url:"{{ route('leads.store') }}",
                type:"post",
                data:data,
                processData:false,
                cache:false,
                contentType:false,
            }).done(function(response){

                $('.showalert').html("<h6 class='text-primary'>Lead Generated Successfully</h6>");
                window.location="{{route('leads.index')}}";

            }).fail(function(error){

            $('.showalert').html("<h6 class='text-danger'>"+error.responseJSON.message+"</h6>")

            })
        })
  })
</script>
@endpush

@include('layouts.footer')
