@include('layouts.header')
<main class="content">
    <div class="container-fluid p-0">
        <div class="container">
            <h2>Email Integrations</h2>

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
                @if(isset($accounts['outlook']))
                    <p class="text-success">
                        ✅ Connected as <strong>{{ $accounts['outlook']->email }}</strong>
                    </p>
                @else
                    <a href="{{ route('outlook.redirect') }}" class="btn btn-primary">Connect Outlook</a>
                @endif
            </div>

            {{-- IMAP --}}
            <div class="card p-3 mb-3">
                <h4>Custom IMAP/SMTP</h4>
                @if(isset($accounts['imap']))
                    <p class="text-success">
                        ✅ Connected as <strong>{{ $accounts['imap']->email }}</strong>
                    </p>
                @else
                    <form action="{{ route('imap.save') }}" method="POST">
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
                @endif
            </div>
        </div>
    </div>
</main>
@include('layouts.footer')
