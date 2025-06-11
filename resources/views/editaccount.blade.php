@include('layouts.header')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Edit Account</strong></h1>

        <div class="card" style="border-radius: 10px;">
            <div style="width: 100%; background-color: #3b65ea; height: 10px; border-radius: 10px 10px 0px 0px;"></div>
            
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('accounts.update', $account->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control mb-2 @error('name') is-invalid @enderror"
                                value="{{ old('name', $account->name) }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Industry <span class="text-danger">*</span></label>
                            <input type="text" name="industry"
                                class="form-control mb-2 @error('industry') is-invalid @enderror"
                                value="{{ old('industry', $account->industry) }}">
                            @error('industry')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                class="form-control mb-2 @error('email') is-invalid @enderror"
                                value="{{ old('email', $account->email) }}">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone"
                                class="form-control mb-2 @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $account->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Website <span class="text-danger">*</span></label>
                            <input type="text" name="website"
                                class="form-control mb-2 @error('website') is-invalid @enderror"
                                value="{{ old('website', $account->website) }}">
                            @error('website')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Address <span class="text-danger">*</span></label>
                            <input type="text" name="address"
                                class="form-control mb-2 @error('address') is-invalid @enderror"
                                value="{{ old('address', $account->address) }}">
                            @error('address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>City <span class="text-danger">*</span></label>
                            <input type="text" name="city"
                                class="form-control mb-2 @error('city') is-invalid @enderror"
                                value="{{ old('city', $account->city) }}">
                            @error('city')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Country <span class="text-danger">*</span></label>
                            <input type="text" name="country"
                                class="form-control mb-2 @error('country') is-invalid @enderror"
                                value="{{ old('country', $account->country) }}">
                            @error('country')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status"
                                class="form-control mb-2 @error('status') is-invalid @enderror">
                                <option value="">-- Select Status --</option>
                                <option value="active" {{ old('status', $account->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $account->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Account</button>
                    <a href="{{ route('home.account') }}" class="btn btn-secondary">Back</a>
                </form>

            </div>
        </div>
    </div>
</main>
@include('layouts.footer')
