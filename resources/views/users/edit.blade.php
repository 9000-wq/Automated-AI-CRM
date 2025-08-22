@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Users List </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                            <h1>Edit User</h1>

                                            <form action="{{ route('users.update', $user) }}" method="POST">
                                                @csrf @method('PUT')

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">First Name:</label>
                                                        <input type="text" class="form-control mb-4" name="first_name" value="{{ $user->first_name }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Last Name:</label>
                                                        <input type="text" class="form-control mb-4" name="last_name" value="{{ $user->last_name }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email:</label>
                                                        <input type="email" class="form-control mb-4" name="email" value="{{ $user->email }}">

                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">User Role:</label>
                                                        <select class="form-control form-select mb-4" name="user_role" id="user_role">
                                                            <option value="" selected>Choose User Role</option>
                                                            <option value="admin" {{ (old('user_role', $user->user_role ?? '') === 'admin') ? 'selected' : '' }}>Admin</option>
                                                            <option value="sales officer" {{ (old('user_role', $user->user_role ?? '') === 'sales officer') ? 'selected' : '' }}>Sales Officer</option>
                                                            <option value="ai agent" {{ (old('user_role', $user->user_role ?? '') === 'ai agent') ? 'selected' : '' }}>AI Agent</option>
                                                            <option value="client" {{ (old('user_role', $user->user_role ?? '') === 'client') ? 'selected' : '' }}>Client</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Password:</label>
                                                        <input type="password" class="form-control mb-4" name="password">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Company:</label>
                                                        <select name="company" class="form-select form-control" id="company">
                                                            <option value="" selected>Select an Option</option>
                                                            @foreach($companies as $company)
                                                            <option value="{{$company->id}}"  {{ $user->company_id == $company->id ? 'selected' : '' }} >{{$company->company_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                @if ($errors->any())
                                                    <div class="alert alert-danger mt-4 ">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li class="text-danger">{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </form>
                                            </div>
										</div>
										
									</div>
									
								</div>
							</div>
						</div>

						
					</div>

					

					

				</div>
			</main>

@push('scripts') 
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>        
<script type="text/javascript">
 
</script>
@endpush
@include('layouts.footer')
