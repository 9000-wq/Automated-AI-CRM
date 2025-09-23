<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Http;
use App\Models\Lead;

class EmailApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call Email Api';

    /**
     * Execute the console command.
     */
    public function handle()
    {
     
        // Get latest 10 unsent leads
        $results = Lead::select(
                'leads.id',
                'leads.name as customer_name',
                'c.company_name',
                'c.company_description',
                'c.bussiness_knowledge',
                'c.price_guidelines',
                'c.business_type',
                'c.company_email',
                'contacts.email',
                'contacts.phone'
            )
            ->join('companies as c', 'c.id', '=', 'leads.company_id')
            ->leftJoin('lead_contacts as lc', 'lc.lead_id', '=', 'leads.id')
            ->leftJoin('contacts', 'contacts.id', '=', 'lc.contact_id')
            ->where('leads.email_sent', 0)
            ->whereNotNull('contacts.email')
            ->where('contacts.email', '!=', '')
            ->where('leads.status', 'New')
            ->orderBy('leads.created_at', 'desc')
            ->limit(10)
            ->get();

        if ($results->isEmpty()) {
            $this->warn('⚠️ No new leads found.');
            return;
        }

        foreach ($results as $lead) {
            $companyDescription = ($lead->company_description ?? '')
                . ' | Knowledge: ' . ($lead->bussiness_knowledge ?? '')
                . ' | Pricing: ' . ($lead->price_guidelines ?? '');

            $payload = [
                'user_id' => 1, // ✅ Scheduler doesn't have Auth, set static or map
                'to' => $lead->email ?? '',
                'our_company_name' => $lead->company_name ?? '',
                'customer_name' => $lead->customer_name ?? '',
                'company_description' => $companyDescription,
                'number' => (int) ($lead->phone ?? 0)
            ];

            try {
                $response = Http::post(env('API_URL_ENDPOINT').'/send-email', $payload);

                if ($response->successful()) {
                    $this->info('✅ Email sent to Lead ID: ' . $lead->id);

                    // Mark lead as emailed
                    Lead::where('id', $lead->id)->update(['email_sent' => 1]);
                } else {
                    $this->error('❌ Failed for Lead ID: ' . $lead->id . ' | Status: ' . $response->status());
                }
            } catch (\Exception $e) {
                $this->error('⚠️ Error for Lead ID: ' . $lead->id . ' | ' . $e->getMessage());
            }


        }
    }
}
