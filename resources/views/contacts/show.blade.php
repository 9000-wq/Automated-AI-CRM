@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Edit Contact</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                     
                                                                                                    
                                                        

                                                            <form method="POST" action="{{ route('contacts.update',$contact->id)}}">
                                                                @csrf @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="name" class="form-label">Enter Name <span class="text-danger">*</span></label>
                                                                        <input type="text" id="name" name="name" value="{{ old('name', is_object($contact) ? $contact->name : '') }}" class="form-control mb-3">
                                                                    </div>

                                                                    <input type="hidden" id="contactid" name="contactid" value="{{ is_object($contact) > 0 ? $contact->id : $leadid  }}">

                                                                    <div class="col-md-6">
                                                                        <label for="role" class="form-label">Enter Role <span class="text-danger">*</span></label>
                                                                        <input type="text" id="role" name="role"  value="{{ old('role', is_object($contact) > 0 ? $contact->role->label : '') }}"  class="form-control mb-3">          
                                                                    </div>

                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="phone" class="form-label">Enter Phone no <span class="text-danger">*</span></label>
                                                                        <input type="number" id="phone" name="phone" value="{{ old('phone', is_object($contact) > 0 ? $contact->phone : '') }}"  class="form-control mb-3"> 
                                                                    </div>
                                            
                                                                    <div class="col-md-6">
                                                                        <label for="email" class="form-label">Enter Email <span class="text-danger">*</span></label>
                                                                        <input type="email" id="email" name="email" value="{{ old('email', is_object($contact) > 0 ? $contact->email : '') }}" class="form-control mb-3">
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label" for="birthday">Enter Birthday </label>
                                                                        <input type="date" id="birthday" value="{{ old('birthday', is_object($contact) > 0 ? $contact->birthday : '') }}" name="birthday" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="address" class="form-label">Please Enter Address <span class="text-danger">*</span></label>
                                                                        <input name="address" id="address" value="{{ old('address', is_object($contact) > 0 ? $contact->address : '') }}" class="form-control mb-3"/>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label for="description" class="form-label">Please Enter Description <span class="text-danger">*</span></label>
                                                                        <textarea name="description" id="description" class="form-control mb-3">{{ old('description', is_object($contact) > 0 ? $contact->description : '') }}</textarea>
                                                                    </div>
                                                                </div>
                                    
                                                                


                                                                <button type="submit" class="btn btn-primary">Update Contact</button>
                                                                <a type="button" href="{{route('contacts.index')}}"  class="btn btn-secondary"><i class="fas fa-arrow-circle-left"></i><span class="m-2">Go Back</span></a>
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
        
