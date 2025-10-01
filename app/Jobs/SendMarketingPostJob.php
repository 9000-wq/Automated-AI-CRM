<?php
namespace App\Jobs;

use App\Models\MarketingPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Support\Facades\Storage;
use App\Mail\PostPublishedMail;
use Illuminate\Support\Facades\Mail;


class SendMarketingPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postId;

    public function __construct($postId)
    {
        $this->postId = $postId;
    }

    public function handle()
    {
        $post = MarketingPost::find($this->postId);

        if (! $post || $post->status !== 'pending') {
            return;
        }

        try {
$imagePath = str_replace('/storage/', '', $post->image);

$response = Http::timeout(60)->attach(
    'picture',
    file_get_contents(storage_path('app/public/' . $imagePath)),
    basename($imagePath)
)->post('http://192.168.18.86:8001/facebook/post', [
    'caption'           => $post->caption,
    'hashtags'          => $post->hashtags,  // keep as string (not array)
    'page_id'           => $post->fb_username,
    'page_access_token' => $post->fb_password,
]);



               
            if ($response->successful()) {
                Log::info("Post {$post->id} sent successfully");
                Log::info($response);
                 $pageId  = explode('_', $response['facebook_response']['post_id'])[0];
    $postId  = explode('_', $response['facebook_response']['post_id'])[1];

    $link = "https://www.facebook.com/{$pageId}/posts/{$postId}";
    $post->update(['status' => 'posted', 'Link'=>$link]);


    // Send Email
    Mail::to("usmanhameedabdulhameed@gmail.com")->send(new PostPublishedMail($link));
            } else {
                $post->update([
                    'status' => 'failed',
                ]);
                Log::error("Post {$post->id} failed: ".$response->body());
            }
        } catch (Throwable $e) {
            $post->update([
                'status' => 'failed',
                'error'  => substr($e->getMessage(),0,500),
            ]);
            Log::error("Post {$post->id} failed with exception: ".$e->getMessage());
            throw $e; // so Laravel can retry
        }
    }
}
