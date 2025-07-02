@include('layouts.header')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('plugins/Tagging-System-Autocomplete/css/amsify.suggestags.css')}}">
<link href="{{asset('plugins/wysiwyg-editor-master/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
<link href="https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css" rel="stylesheet">


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

    /* Call item styling */
    .call-item {
        transition: all 0.2s;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    .call-item:hover {
        background-color: #e9ecef;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .call-time {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .call-description {
        margin-top: 5px;
        font-size: 0.9rem;
    }

   #modal-toggle {
      display: none;
    }
    
    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      overflow-y: scroll;
    }
    
    /* Show modal when checkbox is checked */
    #modal-toggle:checked ~ .modal-overlay {
      display: flex;
    }
    
    .modal-box {
      background: white;
      padding: 25px;
      border-radius: 8px;
      width: 900px;
      max-width: 90%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      position: relative;
    }
    
    .modal-close {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 1.8rem;
      cursor: pointer;
      background: none;
      border: none;
      color: #777;
      line-height: 1;
    }
    
    /* Email Form Styles */
    .email-controls {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    
    .btn {
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.2s;
    }
    
    .btn-send {
      background: #1a73e8;
      color: white;
      border: none;
    }
    
    .btn-send:hover {
      background: #0d62c9;
    }
    
    .btn-secondary {
      background: #f1f3f4;
      color: #3c4043;
      border: 1px solid #dadce0;
    }
    
    .btn-secondary:hover {
      background: #e8eaed;
    }
    
    .email-fields {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .parallel-fields {
      display: flex;
      gap: 15px;
    }
    
    .field-group {
      display: flex;
      flex-direction: column;
      flex: 1;
    }
    
    .field-group label {
      margin-bottom: 5px;
      font-weight: 500;
      color: #3c4043;
      font-size: 0.9rem;
    }
    
    .field-group input,
    .field-group select,
    .field-group textarea {
      padding: 10px;
      border: 1px solid #dadce0;
      border-radius: 4px;
      font-size: 0.95rem;
      transition: border 0.2s;
    }
    
    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
      outline: none;
      border-color: #1a73e8;
      box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
    }
    
    .field-group textarea {
      min-height: 200px;
      resize: vertical;
    }
    
    .required::after {
      content: " *";
      color: #d93025;
    }
    
    /* Open button styles */
    .open-modal-btn {
      padding: 10px 20px;
      background: #1a73e8;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
    }
    
    .open-modal-btn:hover {
      background: #0d62c9;
    }

    #fr-logo{
        display:none !important;
    }
</style>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Lead Details</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <!-- Lead Information Card -->
                        <div class="col-sm-8">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="mb-4"><strong>Name:</strong> {{ $lead->name }}</h3>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="mb-4">
                                                <strong>Case Ref:</strong> {{ $lead->case_ref }}
                                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary float-end"><i class="fas fa-edit"></i></a>
                                            </h3>
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
                                    <form id="notesForm">
                                        <div class="mb-3">
                                            <textarea class="form-control notesinput" rows="3" placeholder="Write your notes here..." required></textarea>
                                        </div>
                                        <input type="hidden" name="leadid" id="leadid" value="{{ $lead->id }}">
                                        <div class="text-end">
                                            <div class="showalert mt-3 text-start"></div>
                                            <button type="button" class="btn btn-primary savenotes px-4">Save Notes</button>
                                        </div>
                                    </form>

                                    <hr class="my-4">
                                    <h4>Notes</h4>
                                    <div class="mb-4">
                                        <div class="appendnotes"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Activities Cards -->
                        <div class="col-md-4">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">
                                    <h3>Contacts</h3>
                                    @foreach($lead->contacts as $contact)
                                        <div class="mb-3" style="font-size: large; font-weight: 500;">
                                            <strong>{{ $contact->full_name }}</strong>
                                            <a href="{{ route('leadcontact', ['contact'=>$contact->id,'leadid'=>$lead->id ]) }}" class="btn btn-info m-1 float-end"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('deleteleadcontact', [$contact->id, $lead->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger m-1 float-end">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <br>
                                            Name: {{ $contact->name }}<br>
                                            Role: {{ $contact->role->label }}<br>
                                            Email: {{ $contact->email }}<br>
                                            Phone: {{ $contact->phone }}<br>
                                            Address: {{ $contact->address }}<br>
                                        </div>
                                    @endforeach

                                    <a href="#" onclick="history.back()" class="btn btn-secondary">Back</a>
                                </div>
                            </div>

                            <!-- Activities Card -->
                           <!-- Activities Card -->
<div class="card mt-3" style="border-radius:10px;">
    <div style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;"></div>
    <div class="card-body activities-container">
       <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Activities</h3>
    <div>
        <label for="modal-toggle" title="Send Email">
            <i class="fas fa-envelope me-2" style="cursor: pointer;"></i>
        </label>
        <i class="fas fa-calendar-alt me-2"></i>
        <a href="{{ route('create.call', ['lead' => $lead->id]) }}" title="Make a Call">
            <i class="fas fa-phone me-2" style="cursor: pointer;"></i>
        </a>
        <!-- Three-dot button for history -->
        <a href="{{ route('call.history', ['lead' => $lead->id]) }}" title="View History">
            <i class="fas fa-ellipsis-v" style="cursor: pointer;"></i>
        </a>
    </div>
</div>
        
       @foreach($activities as $call)
<div class="call-item mb-3">
    <div class="d-flex justify-content-between">
        <strong>{{ $call->name }}</strong>
        <div>
            <span class="badge bg-{{ $call->status == 'planned' ? 'warning' : 'info' }}">
                {{ ucfirst(str_replace('_', ' ', $call->status)) }}
            </span>
            <!-- Updated three-dot button link -->
            <a href="{{ route('call.history', ['lead' => $lead->id, 'call' => $call->id]) }}" class="ms-2">
                <i class="fas fa-ellipsis-v" style="cursor: pointer;"></i>
            </a>
        </div>
    </div>
    <div class="call-time">
        {{ $call->date_start->format('M d, Y H:i') }} - {{ $call->date_end->format('H:i') }}
    </div>
    @if($call->description)
    <div class="call-description">{{ $call->description }}</div>
    @endif

    <a href="{{route('call-screen',['leadid'=>$lead->id])}}" target="_blank" class="btn btn-sm btn-primary mt-2"> <i class="fas fa-phone me-2" style="cursor: pointer;"></i> Call Now <i style="font-size: 20px;" class="material-icons">open_in_new</i></a>
</button>

</div>
@endforeach
    </div>
</div>
            


<!-- History Card -->
                            <div class="card mt-3" style="border-radius:10px;">
                                <div style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0 0;"></div>
                                <div class="card-body history-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3>History</h3>
                                    </div>
                                    
                                    @foreach($history as $call)
                                    <div class="call-item mb-3">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $call->name }}</strong>
                                            <span class="badge bg-success">
                                                Held
                                            </span>
                                        </div>
                                        <div class="call-time">
                                            <?php 
                                                $start = new DateTime($call->date_start);
                                                $end = new DateTime($call->date_end);
                                                echo $start->format('M d, Y H:i') . ' - ' . $end->format('H:i');
                                            ?>
                                        </div>
                                        @if($call->description)
                                        <div class="call-description">{{ $call->description }}</div>
                                        @endif
                                    </div>
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


   <!-- Hidden checkbox toggle -->
<input type="checkbox" id="modal-toggle">

<!-- Modal overlay -->
<div class="modal-overlay" >
  <div class="modal-box" >
    <label for="modal-toggle" class="modal-close">&times;</label>
    <h2>Compose Email</h2>
    
    <div class="email-controls">
        <button class="btn btn-primary" onclick="sendEmail()">Send</button>
        <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
    </div>

    <div class="messageinfo"></div>
    
    <form id="sendleademails">
        <div class="email-fields">
            <div class="parallel-fields">
                <div class="field-group">
                    <label class="required">To</label>
                    <input type="text" id="emailTo" placeholder="Enter your Email">
                </div>
            
                <div class="field-group">
                    <label>CC</label>
                    <input type="text" name="emailCC" id="emailCC">
                </div>
            </div>
        
            <div class="parallel-fields">
                <div class="field-group">
                    <label>Parent</label>
                    <select id="emailParent">
                        <option value="{{ $lead->id }}" selected>{{ $lead->name }}</option>
                    </select>
                </div>
            
                <div class="field-group">
                    <label class="required">Subject</label>
                    <input type="text" id="emailSubject" placeholder="No Subject">
                </div>
            </div>
        
            <div class="field-group">
                <label>Body</label>
                <textarea id="emailBody" ></textarea>
            </div>
        </div>
    </form>
  </div>
</div>
  

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{asset('plugins/Tagging-System-Autocomplete/js/jquery.amsify.suggestags.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js"></script>
<script>
    $(document).ready(function () {


      
        new FroalaEditor('#emailBody', {
            imageUploadURL: '{{route('leaduploadimage')}}',
            fileUploadURL: '{{route('leaduploadfile')}}',
            requestHeaders: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            fileAllowedTypes: [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            toolbarButtons: ['bold', 'italic', 'underline','align','formatOL','formatUL', 'insertImage', 'insertFile', 'undo', 'redo'],

        });

        // Mutiple selector input plugin 

        $('input[name="emailCC"]').amsifySuggestags({
            type :'amsify',
        });

        // Notes functionality
        $('.savenotes').click(function () {
            let notes = $('.notesinput').val();
            let leadid = $('#leadid').val();

            $.ajax({
                url: "{{ route('leads.savenotes') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    leadid: leadid,
                    notes: notes
                },
                success: function (response) {
                    load_notes(leadid, '');
                    $('.notesinput').val("");
                },
                error: function (error) {
                    $('.showalert').html("<h6 class='text-danger'>" + error.responseJSON.message + "</h6>");
                }
            });
        });

        function load_notes(leadId, page) {
            let url = "{{ route('leads.notes', ['lead' => '__LEAD_ID__']) }}".replace('__LEAD_ID__', leadId) + `?page=${page}`;

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    if (page === '') {
                        $('.appendnotes').empty();
                    } else {
                        $('#load-more').remove();
                    }
                    $('.appendnotes').append(response);

                    if ($(response).filter('.note').length < 10) {
                        $('#load-more').remove();
                    }
                },
                error: function () {
                    alert('Could not load more notes.');
                }
            });
        }

        load_notes("{{ $lead->id }}", '');

        $(document).on('click', '#load-more', function () {
            let button = $(this);
            let page = button.data('page');
            load_notes("{{ $lead->id }}", page);
        });

        // Refresh calls periodically (every 30 seconds)
        function refreshCalls() {
            $.ajax({
                url: '/calls/by-lead/{{ $lead->id }}',
                type: 'GET',
                success: function(response) {
                    // Update activities section
                    let activitiesHtml = `
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Activities</h3>
                            <div>
                                <i class="fas fa-envelope me-2"></i>
                                <i class="fas fa-calendar-alt me-2"></i>
                                <a href="{{ route('create.call', ['lead' => $lead->id]) }}" title="Make a Call">
                                    <i class="fas fa-phone me-2" style="cursor: pointer;"></i>
                                </a>
                            </div>
                        </div>`;
                    
                    response.activities.forEach(function(call) {
                        let start = new Date(call.date_start);
                        let end = new Date(call.date_end);
                        
                        // activitiesHtml += `
                        // <div class="call-item mb-3">
                        //     <div class="d-flex justify-content-between">
                        //         <strong>${call.name}</strong>
                        //         <span class="badge bg-${call.status === 'planned' ? 'warning' : 'info'}">
                        //             ${call.status === 'planned' ? 'Planned'}
                        //         </span>
                        //     </div>
                        //     <div class="call-time">
                        //         ${start.toLocaleString('default', { month: 'short' })} ${start.getDate()}, ${start.getFullYear()} ${start.getHours()}:${start.getMinutes().toString().padStart(2, '0')} - 
                        //         ${end.getHours()}:${end.getMinutes().toString().padStart(2, '0')}
                        //     </div>
                        //     ${call.description ? `<div class="call-description">${call.description}</div>` : ''}
                        // </div>`; 

                        activitiesHtml += `
                            <div class="call-item mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>${call.name}</strong>
                                    <span class="badge bg-${call.status === 'planned' ? 'warning' : 'info'}">
                                        ${call.status.charAt(0).toUpperCase() + call.status.slice(1)}
                                    </span>
                                </div>
                                <div class="call-time">
                                    ${start.toLocaleString('default', { month: 'short' })} ${start.getDate()}, ${start.getFullYear()} ${start.getHours()}:${start.getMinutes().toString().padStart(2, '0')} - 
                                    ${end.getHours()}:${end.getMinutes().toString().padStart(2, '0')}
                                </div>
                                ${call.description ? `<div class="call-description">${call.description}</div>` : ''}
                            </div>`;

                        });


                    $('.activities-container').html(activitiesHtml);

                    // Update history section
                    let historyHtml = `
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>History</h3>
                        </div>`;
                    
                    response.history.forEach(function(call) {
                        let start = new Date(call.date_start);
                        let end = new Date(call.date_end);
                        
                        historyHtml += `
                        <div class="call-item mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>${call.name}</strong>
                                <span class="badge bg-success">
                                    Held
                                </span>
                            </div>
                            <div class="call-time">
                                ${start.toLocaleString('default', { month: 'short' })} ${start.getDate()}, ${start.getFullYear()} ${start.getHours()}:${start.getMinutes().toString().padStart(2, '0')} - 
                                ${end.getHours()}:${end.getMinutes().toString().padStart(2, '0')}
                            </div>
                            ${call.description ? `<div class="call-description">${call.description}</div>` : ''}
                        </div>`;
                    });
                    $('.history-container').html(historyHtml);
                }
            });
        }

        // Initial load and periodic refresh
        refreshCalls();
        setInterval(refreshCalls, 30000); // Refresh every 30 seconds

       


  
      
  
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
            closeModal();
            }
        });

  


    });


    function closeModal() {

        document.getElementById('modal-toggle').checked = false;
    
    }


    // Email sending function
    function sendEmail() {
        const to = document.getElementById('emailTo').value;
        const cc = document.getElementById('emailCC').value;
        const leadid = document.getElementById('emailParent').value;
        const leadname = document.getElementById('emailParent').options[
            document.getElementById('emailParent').selectedIndex
        ].text;
        const subject = document.getElementById('emailSubject').value;
        const body = document.getElementById('emailBody').value;
        
       
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
      
        
      
        $.ajax({
            url:"{{route('sendleademail')}}",
            data:{ 
            to: to,
            cc: cc,
            leadid: leadid,
            leadname:leadname,
            subject: subject,
            body: body,
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"post",
        }).done(function(response){
  
            document.getElementById('emailTo').value = '';
            document.getElementById('emailCC').value = '';
            document.getElementById('emailSubject').value = '';
            document.getElementById('emailBody').value = '';

            
            $('.messageinfo').html('<div class="text-primary">Email sent Successfully.</div>');
            closeModal();

        }).fail(function(xhr){
            console.log(xhr)

            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorHtml = '<div class="alert alert-danger"><ul>';
                $.each(errors, function (key, value) {
                    errorHtml += '<li class="text-danger">' + value[0] + '</li>';
                });
                errorHtml += '</ul></div>';
                $('.messageinfo').html(errorHtml);
            } else {
                $('.messageinfo').html('<div class="alert alert-danger">An error occurred.</div>');
            }

           
        })
        
    }


</script>
@endpush

@include('layouts.footer')