@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Lead Details </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                      
                                                                                                
                                                           

                                                            <h4><strong>Name:</strong> {{ $lead->name }} <a  href="{{ route('leads.edit', $lead) }}" class="btn btn-primary " style="float:right;"><i class="fas fa-edit"></i></a></h4>
                                                            <h4><strong>Case Ref:</strong> {{ $lead->case_ref }}</h4>
                                                            <h4><strong>Source:</strong> {{ $lead->source }}</h4>
                                                            <h4><strong>Status:</strong> {{ $lead->status }}</h4>
                                                            <h4><strong>Assigned To:</strong> {{ $lead->user->name ?? 'Unassigned' }}</h4>

                                                            <hr>
                                                            <h3>Contacts</h3>
                                                            @foreach($lead->contacts as $contact)
                                                                <div class="mb-3" style="font-size: large;font-weight: 500;">
                                                                    <strong>{{ $contact->full_name }}</strong> 
                                                                    <a href="{{ route('leadcontact', $contact->id) }}" class="btn btn-info m-1" style="float:right;"><i class="fas fa-edit"></i></a>
                                                                    <form action="{{ route('deleteleadcontact', $contact->id) }}" method="POST" style="display: inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger m-1" style="float:right;" >
                                                                        <i class="fas fa-trash"></i>
                                                                        </button>

                                                                    </form>

                                                                    <br>
                                                                    Role: {{ $contact->role }}<br>
                                                                    Email: {{ $contact->email }}<br>
                                                                    Phone: {{ $contact->phone }}<br>
                                                                    Address: {{ $contact->address }}
                                                                </div>
                                                            @endforeach

                                                            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back</a>
                                                        
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
      
    })
    </script>
    @endpush
    
    @include('layouts.footer')
    
