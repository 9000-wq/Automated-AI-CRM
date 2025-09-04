@include('layouts.header')

<style>
    body {
            background: #f8f9fa;
        }
        .btn-scrape {
            font-size: 18px;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 12px;
            transition: all 0.3s ease-in-out;
        }
        .btn-scrape:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

       
          /* Container for the status box */
        #scraperStatus {
            width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 2px solid #007bff;
            border-radius: 10px;
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Loader animation */
        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: 0 auto 15px auto;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Caption text */
        .caption {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }

        /* Last run message */
        .last-run {
            font-size: 16px;
            color: #007bff;
            font-weight: bold;
        }



</style>
			<main class="content">
				<div class="container-fluid p-0">

					<h1 class="h3 mb-3"><strong>Scrap Contacts </strong></h1>

					<div class="row">
						<div class="col-xl-12 col-xxl-12 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-12">
										<div class="card" style='border-radius:10px;'>
                                        <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
											<div class="card-body">
                         
                                                <button id="scrapeBtn" class="btn btn-primary btn-scrape">
                                                    🚀 Scrap Contacts
                                                </button>

                                                <div id="scraperStatus">
                                                <!-- Loader or last run message will appear here -->
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

@push('scripts') 
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>        
<script>
        document.getElementById("scrapeBtn").addEventListener("click", async () => {
            try {
                let response = await fetch("{{ route('StartScrapper') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        companyid: "{{Auth::user()->company_id}}"
                    })
                });

                let data = await response.json();
                updateScraperStatus(1, null);
                // alert("✅ Scraping completed! Found: " + JSON.stringify(data));
            } catch (error) {
                // alert("❌ Something went wrong: " + error);
            }
        });


        function updateScraperStatus(isRunning, lastRunTime) {
            const container = document.getElementById('scraperStatus');
            container.innerHTML = ''; // clear previous content

            if (isRunning == 1) {
                // Show loader
                const loader = document.createElement('div');
                loader.className = 'loader';

                const caption = document.createElement('div');
                caption.className = 'caption';
                caption.innerText = 'Scraper is currently running...';

                container.appendChild(loader);
                container.appendChild(caption);
            } else {
                // Show last run time
                const message = document.createElement('div');
                message.className = 'last-run';
                message.innerHTML = `Scraper last ran at: ${lastRunTime}`;
                container.appendChild(message);
            }
        }

        // Example usage
        updateScraperStatus("{{$scrapper}}", "{{$scrapperTime}}"); // Scraper running
        // updateScraperStatus(0, '2025-09-04 15:30'); // Scraper not running



    </script>
@endpush
@include('layouts.footer')