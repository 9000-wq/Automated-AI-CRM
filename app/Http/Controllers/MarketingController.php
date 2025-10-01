<?php

namespace App\Http\Controllers;
use App\Jobs\SendMarketingPostJob;
use App\Models\MarketingPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;



class MarketingController extends Controller
{
    /**
     * Show the upload form
     */
   public function showUploadForm()
{
    $posts = MarketingPost::where('user_id', Auth::id())
    ->latest()
    ->paginate(10);
    return view('Marketing.uploadMarketingPost', compact('posts'));
}

public function uploadFile(Request $request)
{
   $request->validate([
    'document' => 'nullable|mimes:pdf,docx,txt|max:2048',
    'text'     => 'nullable|string',
]);

// make sure at least one is provided
if (!$request->hasFile('document') && empty(trim(string: $request->input('text', '')))) {
    return response()->json([
        'success' => false,
        'message' => 'Please provide either a document or some text.'
    ], 400);
}

    try {
        // 1. Get text either from file OR textarea
 $text = trim($request->input('text', ''));

if ($request->hasFile('document')) {
    $file = $request->file('document');
    $extension = $file->getClientOriginalExtension();
    $text = $this->extractText($file, $extension);
}

        if (trim($text) === '') {
            return response()->json([
                'success' => false,
                'message' => 'No text found in input.'
            ], 400);
        }

        // 2. Call FastAPI
        $apiResponse = Http::timeout(120)->post('http://192.168.18.86:8001/generate', [
            'product_info' => $text
        ]);

        if ($apiResponse->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'FastAPI service failed',
                'error'   => $apiResponse->body()
            ], 500);
        }

        $data      = $apiResponse->json();
        $imageRaw  = $data['data']['image'] ?? ($data['data']['versions'][0]['image'] ?? null);
        $caption   = $data['data']['caption'] ?? '';
        $hashtags  = isset($data['data']['hashtags']) ? implode(' ', $data['data']['hashtags']) : '';
        $uid       = $data['data']['uid'];

        // 3. Save image
        $previewId = uniqid('preview_', true);
        Storage::disk('public')->makeDirectory('previews');
        $imageUrl = null;

        if ($imageRaw) {
            $imageUrl = $this->saveImage($imageRaw, $previewId); // must exist
        }

        // 4. Save metadata JSON
        $meta = [
            'image'    => $imageUrl,
            'caption'  => $caption,
            'hashtags' => $hashtags,
            'uid'      => $uid,
        ];

        Storage::disk('local')->put("previews/{$previewId}.json", json_encode($meta));

    

        // 6. Redirect to preview
        return response()->json([
            'success' => true,
            'preview_url' => route('marketing.preview', ['id' => $previewId])
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Show preview page
     */
    public function showPreview($id)
    {
        $jsonPath = "previews/{$id}.json";
        if (!Storage::disk('local')->exists($jsonPath)) {
            abort(404, 'Preview not found or expired.');
        }

        $meta = json_decode(Storage::disk('local')->get($jsonPath), true);
       
        return view('Marketing.preview', [
            'image'    => $meta['image'] ?? null,
            'caption'  => $meta['caption'] ?? '',
            'hashtags' => $meta['hashtags'] ?? '',
            'uid'=>$meta['uid']?? ''
        ]);
    }

    /**
     * Extract text from different file types
     */


    /**
     * Save base64 or URL image to public disk
     */
    private function saveImage($imageRaw, $previewId)
    {
        $imageUrl = null;

        if (Str::startsWith($imageRaw, 'data:')) {
            if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,/', $imageRaw, $matches)) {
                $mime = $matches[1];
                $ext  = explode('/', $mime)[1];
                $base64 = substr($imageRaw, strpos($imageRaw, ',') + 1);
                $decoded = base64_decode($base64);
                $filename = "previews/{$previewId}.{$ext}";
                Storage::disk('public')->put($filename, $decoded);
                $imageUrl = Storage::url($filename);
            }
        } elseif (filter_var($imageRaw, FILTER_VALIDATE_URL)) {
            $imageUrl = $imageRaw;
        } else {
            $decoded = base64_decode($imageRaw);
            $filename = "previews/{$previewId}.png";
            Storage::disk('public')->put($filename, $decoded);
            $imageUrl = Storage::url($filename);
        }

        return $imageUrl;
    }

    /**
     * Save Marketing Post into DB
     */



public function saveMarketingPost(Request $request)
{
    $request->validate([
        'image'         => 'required|string',
        'caption'       => 'required|string',
        'hashtags'      => 'required|string',
        'fb_username'   => 'required|string',
        'fb_password'   => 'required|string',
        'schedule_time' => 'required|date',
        'timezone'      => 'required|string', 
    ]);

    $scheduleTime = Carbon::parse($request->schedule_time, $request->timezone);
    $utcTime = $scheduleTime->clone()->setTimezone('UTC');
    $post = MarketingPost::create([
        'user_id'       => auth()->id(),
        'image'         => $request->image,
        'caption'       => $request->caption,
        'hashtags'      => $request->hashtags,
        'fb_username'   => $request->fb_username,
        'fb_password'   => $request->fb_password,
        'schedule_time' => $utcTime,
        'status'        => 'pending',
    ]);
    if ($utcTime->isPast()) {
        SendMarketingPostJob::dispatch($post->id);
        Log::warning("⚠️ Post {$post->id} scheduled in the past, running immediately.");
    } else {
        SendMarketingPostJob::dispatch($post->id)->delay($utcTime);
        Log::info("✅ Post {$post->id} scheduled for {$scheduleTime->toDateTimeString()} ({$request->timezone}), saved as {$utcTime->toDateTimeString()} UTC");
    }
    return response()->json(['success' => true]);
}

        private function extractText($file, $extension)
    {
        if ($extension === 'txt') {
            return file_get_contents($file->getRealPath());
        }

        if ($extension === 'docx') {
            $phpWord = IOFactory::load($file->getRealPath());
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $text .= $child->getText() . ' ';
                            }
                        }
                    } elseif (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }

            return $text;
        }

        if ($extension === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            return $pdf->getText();
        }

        return '';
    }

 public function reviewAd(Request $request)
{
    $apiResponse = Http::timeout(120)->post('http://192.168.18.86:8001/review-ad', [
        'uid'        => $request->input('uid'),
        'feedback'   => $request->input('feedback'),
        'regenerate' => $request->boolean('regenerate'),
    ]);

    if ($apiResponse->failed()) {
        return response()->json([
            'success' => false,
            'message' => $apiResponse->json('detail') ?? 'Review API failed',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => $apiResponse->json('message'),
        'ad'      => $apiResponse->json('ad'),
    ]);
}


}
