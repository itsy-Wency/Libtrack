<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /** Validate and log a borrowing status notification received by the API. */
    public function receive(Request $r)
    {
        $d = $r->validate(['borrowing_id' => 'required|integer|exists:borrowings,id', 'status' => 'required|in:pending,approved,rejected']);
        Log::info('Borrowing webhook received', $d);

        return response()->json([
            'message'         => 'Webhook received',
            'borrowing_id'    => $d['borrowing_id'],
            'received_status' => $d['status']]);
    }
}
