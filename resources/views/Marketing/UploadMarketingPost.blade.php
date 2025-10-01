@include('layouts.header')
<style>
/* === Overlay Styling === */
    #loadingOverlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        z-index: 1050;
        display: none; /* hidden by default */
        transition: opacity 0.3s ease-in-out;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    #overlayMessage {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-top: 15px;
        animation: fadeText 1s ease-in-out infinite alternate;
    }
    @keyframes fadeText {
        from { opacity: 0.5; }
        to { opacity: 1; }
    }
/* Modal background */
.modal {
  display: none; 
  position: fixed; 
  z-index: 9999; 
  padding-top: 60px; 
  left: 0; 
  top: 0; 
  width: 100%; 
  height: 100%; 
  overflow: auto; 
  background-color: rgba(0,0,0,0.8);
}

/* Modal content (image) */
.modal-content {
  margin: auto;
  display: block;
  max-width: 90%;
  max-height: 80%;
  border-radius: 8px;
}

/* Close button */
.close {
  position: absolute;
  top: 15px;
  right: 25px;
  color: #fff;
  font-size: 35px;
  font-weight: bold;
  cursor: pointer;
}
</style>
<main class="content">
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3"><strong>Generate Post</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">

                                    {{-- Success Message --}}
                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    {{-- Validation Errors --}}
                                    @if($errors->any())
                                        <div class="alert alert-danger mt-3">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li class="text-danger">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Upload/Text Form --}}
                                    <form id="uploadForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Upload Document (PDF, DOCX, TXT)</label>
                                                <input type="file" name="document" class="form-control" accept=".pdf,.docx,.txt">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Or Enter Text</label>
                                                <textarea name="text" class="form-control" rows="4" placeholder="Write your text here..."></textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Generate</button>
                                    </form>

                                    <div id="uploadMessage" class="mt-3"></div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- User Posts Table --}}
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mt-4">Your Uploaded Posts</h4>
                                </div>
                                <div class="card-body">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Caption</th>
            <th>Hashtags</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($posts as $index => $post)
            <tr>
                <td>{{ $posts->firstItem() + $index }}</td>
                <td>
                    @if($post->image)
                        <img src="{{ $post->image }}" width="80" style="cursor:pointer;"
                             onclick="openModal('{{ $post->image }}')">
                    @endif
                </td>
                <td>{{ $post->caption }}</td>
                <td>{{ $post->hashtags }}</td>

<td>
    @if($post->status == 'posted')
        <button class="btn btn-success">Posted</button>
    @elseif($post->status == 'failed')
        <button class="btn btn-danger">Failed</button
    @elseif($post->status == 'pending')
        <button class="btn btn-warning">Pending</button>
    @endif
</td>
<td>
    <span class="d-flex gap-2">
@if(!empty($post->Link))
    <a href="{{ $post->Link }}" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
        View Post
    </a>
@else
    <button class="btn btn-secondary" disabled>No Link</button>
@endif

        <button class="btn btn-warning">View Analytics</button>
    </span>
</td>

            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No posts found</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center mt-3">
    {{ $posts->links('pagination::simple-default') }}
</div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>




<div id="imageModal" class="modal">
  <span class="close" onclick="closeModal()">&times;</span>
  <img class="modal-content" id="modalImage">
</div>


{{-- Overlay --}}
<div id="loadingOverlay">
    <div class="spinner-border text-primary mb-3" role="status"></div>
    <h5 class="fw-semibold text-dark" id="overlayMessage">Generating image...</h5>
</div>

@include('layouts.footer')

<script>
    
const overlay = document.getElementById("loadingOverlay");
const overlayMessage = document.getElementById("overlayMessage");
    let overlayMessages = [
    "Generating image...",
    "Creating ideas...",
    "Processing request...",
    "Applying changes...",
    "Finalizing preview..."
];
        let overlayInterval;
        function toggleOverlay(show = true) {
    if (show) {
        overlay.style.display = "flex";

        let i = 0;
        overlayMessage.textContent = overlayMessages[i];
        overlayInterval = setInterval(() => {
            i = (i + 1) % overlayMessages.length;
            overlayMessage.textContent = overlayMessages[i];
        }, 2000); 

    } else {
        overlay.style.display = "none";
        clearInterval(overlayInterval);
    }
}
$(document).ready(function () {
    


    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();
        toggleOverlay(true);


        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('marketing.upload.file') }}",
            method: "POST",
            data: formData,
            processData: false, 
            contentType: false, 
            success: function (response) {
           toggleOverlay(false);
                if (response.success) {
                    window.location.href = response.preview_url;
                }
            },
            error: function (xhr) {
                           toggleOverlay(false);

                let errors = xhr.responseJSON.errors;
                let errorHtml = '<div class="alert alert-danger"><ul>';
                $.each(errors, function (key, value) {
                    errorHtml += `<li>${value}</li>`;
                });
                errorHtml += '</ul></div>';
                $('#uploadMessage').html(errorHtml);
            }
        });
    });



});
</script>


<script>
function openModal(src) {
    let modal = document.getElementById("imageModal");
    let modalImg = document.getElementById("modalImage");

    modal.style.display = "block";
    modalImg.src = src;
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}
</script>

