
    <div class="container-fluid mt-3 p-0">
            <h1 class="h3 mb-3"><strong>Email Integrations</strong></h1>

            {{-- Gmail --}}
            <div class="card p-3 mb-3">
            <h4>Gmail</h4>
            @if(!empty($accounts) && isset($accounts[0]) && $accounts[0]->provider === 'gmail')
                <p class="text-success">
                    ✅ Connected as <strong>{{ $accounts[0]->email }}</strong>
                </p>
            @else
                <a href="{{ route('google.redirect') }}" class="btn btn-danger">Connect Gmail</a>
            @endif
            </div>

            {{-- Outlook --}}
            <div class="card p-3 mb-3">
                <h4>Outlook</h4>
                @if(!empty($accounts) && isset($accounts[0]) && $accounts[0]->provider === 'outlook')
                    <p class="text-success">
                        ✅ Connected as <strong>{{ $accounts[0]->email }}</strong>
                    </p>
                @else
                    <a href="{{ route('outlook.redirect') }}" class="btn btn-primary">Connect Outlook</a>
                @endif
            </div>

            {{-- IMAP --}}
            <div class="card p-3 mb-3">
                <h4>Custom IMAP/SMTP</h4>
                @if(!empty($accounts) && isset($accounts[0]) && $accounts[0]->provider === 'imap')
                    <p class="text-success">
                        ✅ Connected as <strong>{{ $accounts[0]->email }}</strong>
                    </p>
                @else
                    <form id="imapForm" action="{{ route('imap.save') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>IMAP Host</label>
                            <input type="text" name="imap_host" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>IMAP Port</label>
                            <input type="number" name="imap_port" class="form-control" required value="993">
                        </div>
                        <div class="mb-2">
                            <label>Encryption</label>
                            <select name="imap_encryption" class="form-control">
                                <option value="ssl">SSL</option>
                                <option value="tls">TLS</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" name="imap_username" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Password</label>
                            <input type="password" name="imap_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Save IMAP Settings</button>
                    </form>
                    <div id="imapMessage" class="mt-2"></div>
                    <button class="btn btn-secondary" id="resetButton">Reset</button>
                @endif
            </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#resetButton').on('click', function() {
        if (confirm('Are you sure you want to reset Email settings?')) {
            $.ajax({
                url: '{{ route("email.reset") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Email settings have been reset.');
                    location.reload();
                },
                error: function() {
                    alert('Error resetting Email settings.');
                }
            });
        }
    });
    $('#imapForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formData = form.serialize();

        $('#imapMessage').html('<p class="text-info">⏳ Checking IMAP connection...</p>');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            success: function(response, status, xhr) {
                // If Laravel returns a redirect, jQuery won't auto-follow it
                if (xhr.getResponseHeader('Content-Type').includes('text/html')) {
                    // Laravel redirect HTML response -> just reload the page
                    window.location.href = xhr.responseURL;
                } else if (response.success) {
                    $('#imapMessage').html('<p class="text-success">✅ ' + response.message + '</p>');
                }
            },
            error: function(xhr) {
                let errMsg = "❌ Unexpected error occurred.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                $('#imapMessage').html('<p class="text-danger">' + errMsg + '</p>');
            }
        });
    });
});
</script>
