@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Opportunities</strong></h1>
                    <div class="row">
                        <div class="col-sm-6 ">
                            <div class="input-group ">
                                <input type="search" name="search" id="search" class="form-control" placeholder="Search...">
                                <button class="btn btn-primary searchbtn"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
					<div class="row mt-4">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
                               
                                    @foreach(['New', 'Contacted', 'Follow-Up', 'Converted', 'Lost'] as $status)
                                        <div class="col-md-2 m-4 mt-4">
                                            <button class="mb-3" style="background-color: #3b65ea;width:16rem; border: none;outline: none;border-radius: 9px;">
                                                <h3 style="color:white; text-align: left;padding-top: 7px; margin-left: 0.6rem;">{{ $status }}</h3>
                                            </button>
                                            <div id="column-{{ strtolower($status) }}">
                                                {{-- Leads will load via AJAX --}}
                                            </div>
                                        </div>
                                    @endforeach
									
								</div>
							</div>
						</div>

						
					</div>

					

					

				</div>
			</main>

@push('scripts') 
       
<script>
    $(document).ready(function () {
        const statuses = ['New', 'Contacted', 'Follow-Up', 'Converted', 'Lost'];

        // Initial load
        statuses.forEach(status => {
            loadLeads(status, 1,search = '');
        });

        $('.searchbtn').click(function(){
            let searchvalue=$('#search').val();
          
            statuses.forEach(status => {
                loadLeads(status, 1,searchvalue);
            });

        })

        // Load leads via AJAX
        function loadLeads(status, page,search) {
            $.ajax({
                url: "{{ route('leads.fetch') }}",
                method: "GET",
                data: { status: status, page: page,search:search },
                success: function (data) {
                    $('#column-' + status.toLowerCase()).html(data);
                }
            });
        }

        // Delegate click events for Prev / Next
        $(document).on('click', '.prev-page, .next-page', function () {
            const page = $(this).data('page');
            const status = $(this).data('status');
            loadLeads(status, page);
        });
    });
</script>
@endpush
@include('layouts.footer')