@foreach($notes as $note)
    <div class="note border-bottom pb-2 mb-2 notes{{$note->id}}">
        <div class="dropdown text-end">
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="actionDropdown">
                <li><a class="dropdown-item text-danger" onClick="deletenotes({{$note->id}})" href="#">Delete</a></li>
            </ul>
        </div>
        <button class="btn btn-light border-0" style="float:right;" type="button" id="actionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
        </button> 
        <h6 class="mb-1">{{$note->name}} <small class="text-muted">• {{ \Carbon\Carbon::parse($note->created_at)->diffForHumans() }}
        </small> </h6>
        <p class="mb-0 mt-2">{{$note->notes}}</p>
    </div>

    

@endforeach


<script>
    function deletenotes(noteid){

        swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this note!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {

            const url = "{{route('deletenotes')}}";

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}',
                    noteid:noteid
                },
                success: function (response) {

                    swal("Poof! Your note has been deleted!", {
                    icon: "success",
                    });

                    $('.notes'+noteid).remove();
                    
                },
            })

            
        }
    });

    }
</script>
