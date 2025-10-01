@include('layouts.header')

<style>
    body {
        background-color: #f8f9fa;
    }
    .preview-card {
        max-width: 650px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
    }
    .preview-image {
        max-height: 400px;
        object-fit: cover;
        width: 100%;
    }
    .hashtags {
        color: #0d6efd;
        font-size: 0.9rem;
    }
    .fb-actions, .ig-actions {
        border-top: 1px solid #e9ecef;
        padding-top: 10px;
        display: flex;
        justify-content: space-around;
    }
    .fb-actions button, .ig-actions button {
        border: none;
        background: none;
        font-weight: 600;
        cursor: pointer;
        color: #555;
    }
    .fb-actions button:hover, .ig-actions button:hover {
        color: #0d6efd;
    }

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
    /* === Professional Toolbar Styling === */
.editor-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #000000ff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 10px;
}

.editor-toolbar label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #ffffffff;
    margin-right: 4px;
}

.editor-toolbar input,
.editor-toolbar select {
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 3px 6px;
    font-size: 0.85rem;
}


.editor-toolbar button {
    border-radius: 6px;
    border: 1px solid #ccc;
    background: #ffffffff;
    color: black;
    padding: 4px 8px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}


/* Icons for Bold / Italic / Underline */
.editor-toolbar button.bold { font-weight: 700; }
.editor-toolbar button.italic { font-style: italic; }
.editor-toolbar button.underline { text-decoration: underline; }

</style>

<div class="container py-5">
    {{-- Alerts --}}
    <div id="alertContainer" class="mb-3" style="display:none;">
        <div class="alert" role="alert" id="alertBox"></div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="previewTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="fb-tab" data-bs-toggle="tab" data-bs-target="#fbPreview" type="button" role="tab">
                Facebook Preview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ig-tab" data-bs-toggle="tab" data-bs-target="#igPreview" type="button" role="tab">
                Instagram Preview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#editImage" type="button" role="tab">
                Edit Image
            </button>
        </li>
    </ul>

    <div class="tab-content" id="previewTabsContent">
        {{-- Facebook Preview --}}
        <div class="tab-pane fade show active" id="fbPreview" role="tabpanel">
            <div class="card shadow-lg preview-card">
                {{-- Header --}}
                <div class="card-header bg-white d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=OCM+Marketing" 
                        class="rounded-circle me-3" width="45" height="45" alt="User">
                    <div>
                        <h6 class="mb-0 fw-bold">OCM Marketing</h6>
                        <small class="text-muted">Scheduled Facebook Post</small>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <p id="fbCaptionPreview" class="mb-1">{{ $caption ?? '' }}</p>
                    <p class="hashtags mb-3" id="fbHashtagsPreview">{{ $hashtags ?? '' }}</p>

                    @if($image)
                        <img src="{{ $image }}" id="previewImage" class="preview-image mb-3">
                    @else
                        <p class="text-danger text-center my-4 fw-bold">⚠️ No image generated</p>
                    @endif

                    <div class="fb-actions">
                        <button>👍 Like</button>
                        <button>💬 Comment</button>
                        <button>↗️ Share</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Instagram Preview --}}
        <div class="tab-pane fade" id="igPreview" role="tabpanel">
            <div class="card shadow-lg preview-card">
                <div class="card-header bg-white d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=OCM+Marketing" 
                        class="rounded-circle me-3" width="45" height="45" alt="User" >
                    <div>
                        <h6 class="mb-0 fw-bold">OCM Marketing</h6>
                        <small class="text-muted">Instagram Preview</small>
                    </div>
                </div>

                @if($image)
                    <img src="{{ $image }}" class="preview-image" id="instaPreview">
                @endif

                <div class="card-body">
                    <p id="igCaptionPreview" class="mb-1">{{ $caption ?? '' }}</p>
                    <p class="hashtags" id="igHashtagsPreview">{{ $hashtags ?? '' }}</p>

                    <div class="ig-actions mt-3">
                        <button>❤️ Like</button>
                        <button>💬 Comment</button>
                        <button>📤 Share</button>
                    </div>
                </div>
            </div>
        </div>

      <div class="tab-pane fade" id="editImage" role="tabpanel">
  <div class="card shadow-lg preview-card p-3">

    <div class="editor-toolbar">
        <button id="addTextBtn" class="btn btn-sm btn-primary">+ Add Text</button>

        <!-- Typography -->
        <label>Font:</label>
        <select id="fontFamily">
            <option value="Arial">Arial</option>
            <option value="Times New Roman">Times New Roman</option>
            <option value="Courier New">Courier New</option>
            <option value="Impact">Impact</option>
            <option value="Georgia">Georgia</option>
            <option value="Verdana">Verdana</option>
        </select>

        <label>Size:</label>
        <input type="number" id="fontSize" value="22" min="10" max="100">

        <button id="boldText" class="bold">B</button>
        <button id="italicText" class="italic">I</button>
        <button id="underlineText" class="underline">U</button>

        <!-- Colors -->
        <label>
            Color:</label>
        <input type="color" id="textColor" value="#000000">

        <label>BG:</label>
        <input type="color" id="bgColor" value="#ffffff">

        <!-- Effects -->
        <label>Opacity:</label>
        <input type="range" id="textOpacity" min="0" max="1" step="0.1" value="1">

        <button id="toggleShadow">Shadow</button>

        <!-- Layout -->
        <label>Align:</label>
        <select id="textAlign">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </select>

        <label>Line:</label>
        <input type="number" id="lineHeight" value="1.2" step="0.1" min="0.5" max="3">

        <label>Spacing:</label>
        <input type="number" id="charSpacing" value="0" step="10" min="0" max="500">

        <button id="saveEdited" class="btn btn-sm btn-success ms-auto">💾 Save</button>
    </div>

    <canvas id="imageCanvas" width="600" height="400" style="border:1px solid #ccc; border-radius:8px;"></canvas>
  </div>
</div>


    {{-- Original Form --}}
    <div class="card shadow-lg mt-4">
        <div class="card-body">
            <form id="postForm">
                @csrf
                <div class="mb-3">
                    <label for="caption" class="form-label fw-semibold">Caption</label>
                    <textarea name="caption" id="caption" class="form-control" rows="3">{{ old('caption', $caption ?? '') }}</textarea>
                </div>

                @if(!empty($hashtags))
                    <p class="hashtags mb-3" id="previewHashtags">{{ $hashtags }}</p>
                @endif

                <div class="mb-3">
                    <label for="changes" class="form-label fw-semibold">Suggest Changes (optional)</label>
                    <textarea name="changes" id="changes" class="form-control" rows="2"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fb_username" class="form-label fw-semibold">Facebook Username</label>
                        <input type="text" name="fb_username" id="fb_username" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fb_password" class="form-label fw-semibold">Facebook Password</label>
                        <input type="password" name="fb_password" id="fb_password" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="schedule_time" class="form-label fw-semibold">Schedule Time</label>
                    <input type="datetime-local" name="schedule_time" id="schedule_time" class="form-control" required>
                    <input type="hidden" name="timezone" id="timezone">
                </div>

                <input type="hidden" name="image" value="{{ $image }}" id="hiddenImage">
                <input type="hidden" name="hashtags" value="{{ $hashtags }}" id="hiddenHashtags">
                <input type="hidden" name="uid" value="{{ $uid }}" id="hiddenUid">

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-danger" id="reviewBtn"> Suggest Changes</button>
                    <button type="button" class="btn btn-primary px-4" id="saveBtn"> Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Overlay --}}
<div id="loadingOverlay">
    <div class="spinner-border text-primary mb-3" role="status"></div>
    <h5 class="fw-semibold text-dark" id="overlayMessage">Generating image...</h5>
</div>

@include('layouts.footer')

{{-- Fabric.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>

<script>
function showAlert(message, type = "success") {
    let alertBox = document.getElementById("alertBox");
    let alertContainer = document.getElementById("alertContainer");
    alertBox.className = "alert alert-" + type;
    alertBox.innerHTML = message;
    alertContainer.style.display = "block";
    setTimeout(() => { alertContainer.style.display = "none"; }, 4000);
}

// Overlay messages rotation
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

// === REVIEW BUTTON ===
document.getElementById("reviewBtn").addEventListener("click", function() {
    let feedback = document.getElementById("changes").value;
    toggleOverlay(true);

    fetch("{{ route('marketing.review') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({
            feedback: feedback,
            regenerate: true,
            uid: document.getElementById("hiddenUid").value,
        })
    })
    .then(res => res.json())
    .then(data => {
        toggleOverlay(false);
        if (data.success && data.ad) {
            let ad = data.ad;
            if (ad.versions && ad.versions.length > 0) {
                let latest = ad.versions[ad.versions.length - 1];
                document.getElementById("previewImage").src = latest.image;
                document.getElementById("instaPreview").src = latest.image;
                document.getElementById("hiddenImage").value = latest.image;
            }
            document.getElementById("caption").value = ad.caption || "";
            document.getElementById("previewHashtags").textContent = ad.hashtags || "";
            document.getElementById("hiddenHashtags").value = ad.hashtags || "";
            document.getElementById("hiddenUid").value = ad.uid || "";

            document.getElementById("fbCaptionPreview").textContent = ad.caption || "";
            document.getElementById("igCaptionPreview").textContent = ad.caption || "";
            document.getElementById("fbHashtagsPreview").textContent = ad.hashtags || "";
            document.getElementById("igHashtagsPreview").textContent = ad.hashtags || "";

            showAlert("✅ Preview updated with your feedback!", "success");
        } else {
            showAlert("⚠️ Error: " + (data.message || 'Unknown error'), "danger");
        }
    })
    .catch(err => {
        toggleOverlay(false);
        showAlert("❌ Review failed: " + err, "danger");
    });
});

// === SAVE BUTTON ===
document.getElementById("saveBtn").addEventListener("click", function() {
    document.getElementById("timezone").value = Intl.DateTimeFormat().resolvedOptions().timeZone;
    let formData = new FormData(document.getElementById("postForm"));
    formData.set("caption", document.getElementById("caption").value);

    fetch("{{ route('marketing.save') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(" Post Schedule successfully!", "success");
            setTimeout(() => window.location.href = "{{ route('upload-marketing-post') }}", 1500);
        } else {
            alert("⚠️ Error saving post: " + (data.message || 'Unknown error'), "danger");
        }
    })
    .catch(err => {
        showAlert("❌ Save failed: " + err, "danger");
    });
});

// === Sync caption live ===
document.getElementById("caption").addEventListener("input", function(e){
    document.getElementById("fbCaptionPreview").textContent = e.target.value;
    document.getElementById("igCaptionPreview").textContent = e.target.value;
});


// === FABRIC.JS IMAGE EDITING ===
let canvas = new fabric.Canvas('imageCanvas');

// Load preview image
@if($image)
fabric.Image.fromURL("{{ $image }}", function(img) {
    img.set({ selectable: false, evented: false });
    img.scaleToWidth(canvas.width);
    canvas.add(img);
    canvas.sendToBack(img);
});
@endif

// Add editable text
document.getElementById('addTextBtn').onclick = function() {
    let text = new fabric.IText('Edit me', {
        left: 50,
        top: 50,
        fontFamily: 'Arial',
        fill: '#000',
        fontSize: 22
    });
    canvas.add(text).setActiveObject(text);
};

// Change text color
document.getElementById('textColor').oninput = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('fill', this.value);  // update color
        canvas.renderAll();              // refresh canvas
    } else {
        alert("⚠️ Please select a text object first!");
    }
};


// Change font family
document.getElementById('fontFamily').onchange = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('fontFamily', this.value);
        canvas.renderAll();
    }
};

// Save edited image
document.getElementById('saveEdited').onclick = function() {
    let dataURL = canvas.toDataURL('image/png');
    document.getElementById("previewImage").src = dataURL;
    document.getElementById("instaPreview").src = dataURL;
    document.getElementById("hiddenImage").value = dataURL;
    showAlert("✅ Edited image applied to previews!", "success");
};

// Change font size
document.getElementById('fontSize').onchange = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('fontSize', parseInt(this.value));
        canvas.renderAll();
    }
};

// Toggle Bold
document.getElementById('boldText').onclick = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('fontWeight', active.fontWeight === 'bold' ? 'normal' : 'bold');
        canvas.renderAll();
    }
};

// Toggle Italic
document.getElementById('italicText').onclick = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('fontStyle', active.fontStyle === 'italic' ? 'normal' : 'italic');
        canvas.renderAll();
    }
};

// Toggle Underline
document.getElementById('underlineText').onclick = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('underline', !active.underline);
        canvas.renderAll();
    }
};
// Opacity
document.getElementById('textOpacity').oninput = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('opacity', parseFloat(this.value));
        canvas.renderAll();
    }
};

// Text Alignment
document.getElementById('textAlign').onchange = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('textAlign', this.value);
        canvas.renderAll();
    }
};

// Line Height
document.getElementById('lineHeight').oninput = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('lineHeight', parseFloat(this.value));
        canvas.renderAll();
    }
};

// Letter Spacing
document.getElementById('charSpacing').oninput = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('charSpacing', parseInt(this.value));
        canvas.renderAll();
    }
};

// Background Color
document.getElementById('bgColor').onchange = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        active.set('backgroundColor', this.value);
        canvas.renderAll();
    }
};

// Shadow Toggle
document.getElementById('toggleShadow').onclick = function() {
    let active = canvas.getActiveObject();
    if (active && active.type === 'i-text') {
        if (active.shadow) {
            active.set('shadow', null);
        } else {
            active.set('shadow', {
                color: 'rgba(0,0,0,0.4)',
                blur: 5,
                offsetX: 3,
                offsetY: 3
            });
        }
        canvas.renderAll();
    }
};

</script>
