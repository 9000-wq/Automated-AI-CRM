@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Price Management </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                                                                                                
                                                <form method="POST" action="{{ isset($plan) ? route('plans.update', $plan) : route('plans.store') }}">
                                                    @csrf
                                                    @if(isset($plan))
                                                        @method('PUT')
                                                    @endif
                                                    <div class="row">
                                                        <div class="col-md-4">

                                                            <label class="form-label">Name:</label>
                                                            <input type="text" name="name" class="form-control mb-4" value="{{ old('name', $plan->name ?? '') }}">

                                                        </div>

                                                        <div class="col-md-4">

                                                            <label class="form-label">Price:</label>
                                                            <input type="number"  class="form-control mb-4" name="price" step="0.01" value="{{ old('price', $plan->price ?? '') }}">

                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label">Billing Cycle:</label>
                                                            <select name="billing_cycle" class="form-select form-control">
                                                                <option value="" selected>Please Choose Billing Cycle</option>
                                                                <option value="monthly" {{ (old('billing_cycle', $plan->billing_cycle ?? '') === 'monthly') ? 'selected' : '' }}>Monthly</option>
                                                                <option value="yearly" {{ (old('billing_cycle', $plan->billing_cycle ?? '') === 'yearly') ? 'selected' : '' }}>Yearly</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                   <div class="row">
                                                        <div class="col-md-12">
                                                            <label class="form-label">Description:</label>
                                                            <textarea class="form-control mb-4" name="description">{{ old('description', $plan->description ?? '') }}</textarea>

                                                        </div>
                                                   </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label class="form-label">Features (one per line):</label>
                                                            <textarea class="form-control mb-4" name="features">{{ old('features', isset($plan) ? str_replace(',', "\n", $plan->features) : '') }}</textarea>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Save</button>
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

@include('layouts.footer')
