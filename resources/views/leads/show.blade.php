@include('layouts.header')

<style>
  textarea.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(59, 101, 234, 0.25);
    border-color: #3b65ea;
  }

  @media (max-width: 576px) {
    .card-body {
      padding: 1.5rem 1rem;
    }

    textarea {
      font-size: 0.95rem;
    }
  }
</style>

			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Lead Details </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-8">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                      
                                                                                                
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h3 class="mb-4"><strong>Name:</strong> {{ $lead->name }} </h3>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <h3 class="mb-4"><strong>Case Ref:</strong> {{ $lead->case_ref }} <a  href="{{ route('leads.edit', $lead) }}" class="btn btn-primary " style="float:right;"><i class="fas fa-edit"></i></a></h3>                                                       
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h3 class="mb-4"><strong>Source:</strong> {{ $lead->source }}</h3>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h3 class="mb-4"><strong>Assigned To:</strong> {{ $lead->user->name ?? 'Unassigned' }}</h3>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $statusColors = [
                                                                'New' => 'primary',
                                                                'Contacted' => 'info',
                                                                'Follow-up' => 'warning',
                                                                'Converted' => 'success',
                                                                'Lost' => 'danger',
                                                            ];
                                                        @endphp

                                                        <h3 class="mb-4">
                                                            <strong>Status:</strong>
                                                            <span class="text-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                                                {{ $lead->status }}
                                                            </span>
                                                        </h3>

                                                            <hr>
                                                            
                                                                
                                                                    <h3 class="mt-3 mb-4"><strong>Lead Notes</strong></h3>

                                                                        <!-- Comment Form -->
                                                                        <form id="notesForm">
                                                                            <div class="d-flex mb-3">
                                                                            <textarea class="form-control notesinput" rows="3" placeholder="Write your notes here..." required></textarea>
                                                                            </div>
                                                                            <input type="hidden" class="form-control" name="leadid" id="leadid" value="{{$lead->id}}">
                                                                            <div class="text-end">
                                                                            <div class="showalert mt-3" style="text-align: start;"></div>
                                                                            <button type="button" class="btn btn-primary savenotes px-4">Save Notes</button>
                                                                            </div>
                                                                        </form>

                                                                        <hr class="my-4">

                                                                        <!-- Sample Comment -->
                                                                        <div class="d-flex mb-4">
                                                                            <div>
                                                                            <h6 class="mb-1">Jane Doe <small class="text-muted">• 2 hours ago</small></h6>
                                                                            <p class="mb-0">This is a beautifully designed comment box! Works great on all screen sizes.</p>
                                                                            </div>
                                                                        </div>

                                                                   
                                                                
                                                        </div>
										
                                        </div>
                                        
                                    </div>

                                    <div class="col-md-4">

                                    
                                    <div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">

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
      
        $('.savenotes').click(function(){

            let notes=$('.notesinput').val();
            let leadid=$('#leadid').val();

            $.ajax({
                url: "{{route('leads.savenotes')}}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    leadid:leadid,
                    notes:notes
                },
                success: function (response) {


                },
                error: function (error) {
                    $('.showalert').html("<h6 class='text-danger'>"+error.responseJSON.message+"</h6>")
                }

            })


        })

    })
    </script>
    @endpush
    
    @include('layouts.footer')
    
