@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Edit Lead</strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                     
                                                                                                    
                                                        

                                                            <form method="POST" action="{{ route('leads.update', $lead) }}">
                                                                @csrf @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label for="name" class="form-label">Enter Lead Name <span class="text-danger">*</span></label>
                                                                        <input type="text" id="name" name="name" value="{{ $lead->name }}" class="form-control mb-3">
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label for="case_ref" class="form-label">Enter Case Ref <span class="text-danger">*</span></label>
                                                                        <input type="text" id="case_ref" name="case_ref" value="{{ $lead->case_ref }}" class="form-control mb-3">          
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label for="source" class="form-label">Enter Source <span class="text-danger">*</span></label>
                                                                        <input type="text" id="source" name="source" value="{{ $lead->source }}" class="form-control mb-3">
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label for="status" class="form-label">Choose status <span class="text-danger">*</span></label>
                                                                        <select name="status" id="status" class="form-control mb-3">
                                                                            @foreach(['New', 'Contacted', 'Follow-up', 'Converted', 'Lost'] as $status)
                                                                                <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                            
                                                                    <div class="col-md-6">
                                                                        <label for="assigned_to" class="form-label">Please select a user </label>
                                                                        <select name="assigned_to" id="assigned_to" class="form-select form-control mb-3">
                                                                            <option value="" selected disabled>Choose an Option</option>
                                                                            @foreach($users as $user)
                                                                            <option value="{{$user->id}}" {{ $lead->assigned_to  ==  $user->id ? 'selected' : '' }} >{{$user->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                


                                                                <button type="submit" class="btn btn-primary">Update Lead</button>
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
        
