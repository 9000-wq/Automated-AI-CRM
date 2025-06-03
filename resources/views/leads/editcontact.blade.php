@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>{{ count($contact) > 0 ? 'Edit' : 'Add' }} Lead Contact</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                     
                                                                                                    
                                                        

                                                            <form method="POST" action="{{ route('updateleadcontact')}}">
                                                                @csrf @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="full_name" class="form-label">Enter Full Name <span class="text-danger">*</span></label>
                                                                        <input type="text" id="full_name" name="full_name" value="{{ count($contact) > 0 ? $contact[0]->full_name : '' }}" class="form-control mb-3">
                                                                    </div>

                                                                    <input type="hidden" id="contactid" name="contactid" value="{{ count($contact) > 0 ? $contact[0]->id : $leadid  }}">

                                                                    <div class="col-md-6">
                                                                        <label for="role" class="form-label">Enter Role <span class="text-danger">*</span></label>
                                                                        <input type="text" id="role" name="role" value="{{ count($contact) > 0 ? $contact[0]->role : '' }}" class="form-control mb-3">          
                                                                    </div>

                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="phone" class="form-label">Enter Phone no <span class="text-danger">*</span></label>
                                                                        <input type="number" id="phone" name="phone" value="{{ count($contact) > 0 ? $contact[0]->phone : '' }}" class="form-control mb-3"> 
                                                                    </div>
                                            
                                                                    <div class="col-md-6">
                                                                        <label for="email" class="form-label">Enter Email <span class="text-danger">*</span></label>
                                                                        <input type="email" id="email" name="email" value="{{ count($contact) > 0 ? $contact[0]->email : '' }}" class="form-control mb-3">
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="address" class="form-label">Please Enter Address <span class="text-danger">*</span></label>
                                                                        <textarea name="address" id="address" class="form-control mb-3">{{ count($contact) > 0 ? $contact[0]->address : '' }}</textarea>
                                                                    </div>
                                                                </div>
                                    
                                                                


                                                                <button type="submit" class="btn btn-primary">{{ count($contact) > 0 ? 'Update' : 'Add' }} Contact</button>
                                                                @if (session('success'))
                                                                    <div class="text-primary mt-3">
                                                                        {{ session('success') }}
                                                                    </div>
                                                                @endif

                                                                @if ($errors->any())
                                                                    <div class="text-danger">
                                                                        <ul class="mb-0 mt-3">
                                                                            @foreach ($errors->all() as $error)
                                                                                <li>{{ $error }}</li>
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
                    </main>
        
                  
        @push('scripts')
        <script>
          $(document).ready(function(){
          
        })
        </script>
        @endpush
        
        @include('layouts.footer')
        
