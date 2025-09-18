@include('layouts.header')
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>

					<div class="row">
						<div class="col-xl-6 col-xxl-5 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-6">
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">New</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="fas fa-user-plus"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">{{$newLeadsCount}}</h1>
												<div class="mb-0">
													<!-- <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> -3.65% </span> -->
													<span class="text-muted">Leads This week</span>
												</div>
											</div>
										</div>
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">Contacted</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="fas fa-envelope-open-text"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">{{$contactedLeads}}</h1>
												<div class="mb-0">
													<!-- <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 5.25% </span> -->
													<span class="text-muted">Leads This week</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">Follow-up</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="fas fa-calendar-check"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">{{$FollowUpLeads}}</h1>
												<div class="mb-0">
													<!-- <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> 6.65% </span> -->
													<span class="text-muted">Leads This week</span>
												</div>
											</div>
										</div>
										<div class="card">
											<div class="card-body">
												<div class="row">
													<div class="col mt-0">
														<h5 class="card-title">Coverted</h5>
													</div>

													<div class="col-auto">
														<div class="stat text-primary">
															<i class="fas fa-check-circle"></i>
														</div>
													</div>
												</div>
												<h1 class="mt-1 mb-3">{{$convertedLeads}}</h1>
												<div class="mb-0">
													<!-- <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> -2.25% </span> -->
													<span class="text-muted">Leads This week</span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-xl-6 col-xxl-7">
							<div class="card flex-fill w-100">
								<div class="card-header">

									<h5 class="card-title mb-0">Weekly Leads Report</h5>
								</div>
								<div class="card-body py-3">
									<div class="chart chart-sm">
										<canvas id="chartjs-dashboard-line"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>

					

					<div class="row">
						<div class="col-12 col-lg-8 col-xxl-9 d-flex">
							<div class="card flex-fill p-4">
								<div class="card-header">

									<h5 class="card-title mb-0">Latest Leads</h5>
								</div>
								<table class="table table-hover my-0 " id="leads-table">
									<thead>
										<tr>
											<th>Name</th>
											<th class="d-none d-xl-table-cell">Lead Date</th>
											<th>Status</th>
											<th class="d-none d-md-table-cell">Assignee</th>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-12 col-lg-4 col-xxl-3 d-flex">
							<div class="card flex-fill w-100">
								<div class="card-header">

									<h5 class="card-title mb-0">Monthly Sales</h5>
								</div>
								<div class="card-body d-flex w-100">
									<div class="align-self-center chart chart-lg">
										<canvas id="chartjs-dashboard-bar"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</main>


@push('scripts')


<script>
document.addEventListener("DOMContentLoaded", function() {

	function loadLeadChart() {
		$.ajax({
			url: "{{ route('leads.stats') }}", // Your Laravel route
			method: "GET",
			success: function (response) {
				const ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");

				const gradient = ctx.createLinearGradient(0, 0, 0, 225);
				gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
				gradient.addColorStop(1, "rgba(215, 227, 244, 0)");

		

				

				new Chart(ctx, {
					type: "bar", // ✅ Change to bar chart
					data: {
						labels: response.labels, // e.g. ['2025-09-10', '2025-09-11', ...]
						datasets: [
							{
								label: "New Leads",
								data: response.new_leads,
								backgroundColor: "rgba(78, 115, 223, 0.7)",
								borderColor: "#4e73df",
								borderWidth: 1
							},
							{
								label: "Follow-Up",
								data: response.follow_up,
								backgroundColor: "rgba(246, 194, 62, 0.7)",
								borderColor: "#f6c23e",
								borderWidth: 1
							},
							{
								label: "Contacted",
								data: response.contacted,
								backgroundColor: "rgba(54, 185, 204, 0.7)",
								borderColor: "#36b9cc",
								borderWidth: 1
							},
							{
								label: "Converted",
								data: response.converted,
								backgroundColor: "rgba(28, 200, 138, 0.7)",
								borderColor: "#1cc88a",
								borderWidth: 1
							},
							{
								label: "Lost",
								data: response.lost,
								backgroundColor: "rgba(223, 78, 114, 0.7)",
								borderColor: "#e61e1e",
								borderWidth: 1
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						interaction: {
							mode: "index",
							intersect: false
						},
						plugins: {
							legend: {
								display: true,
								position: "bottom"
							},
							tooltip: {
								mode: "index",
								intersect: false
							}
						},
						scales: {
							x: {
								stacked: true // ✅ Stack bars horizontally
							},
							y: {
								stacked: true, // ✅ Stack bars vertically
								beginAtZero: true
							}
						}
					}
				});






			},
			error: function (xhr) {
				console.error("Failed to load leads chart data", xhr.responseText);
			}
		});
	}


    // Call function on page load
    loadLeadChart();
});
</script>


<script>

	document.addEventListener("DOMContentLoaded", function () {
		fetch("{{ route('monthlySuccess') }}")
			.then(response => response.json())
			.then(data => {
				new Chart(document.getElementById("chartjs-dashboard-bar"), {
					type: "bar",
					data: {
						labels: data.labels,
						datasets: [{
							label: "Converted Leads",
							backgroundColor: "#1cc88a",
							borderColor: "#1cc88a",
							hoverBackgroundColor: "#17a673",
							hoverBorderColor: "#17a673",
							data: data.data,
							barPercentage: 0.75,
							categoryPercentage: 0.5
						}]
					},
					options: {
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false }
						},
						scales: {
							x: {
								stacked: false,
								grid: { display: false }
							},
							y: {
								stacked: false,
								beginAtZero: true,
								ticks: { stepSize: 5 }
							}
						}
					}
				});
			});
	});
</script>


<script>
$(document).ready(function() {
    $('#leads-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('leads.index') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at',
				render: function(data, type, row) {
                return moment(data).format('DD MMM YYYY, h:mm A'); 
				}
			},
            { data: 'status', name: 'status' },
            { data: 'assigned_to', name: 'assigned_to' },
           
        ],

		order: [[1, 'desc']] // Default sort by created_at descending

    });
});
</script>


@endpush
@include('layouts.footer')