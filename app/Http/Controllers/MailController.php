<?php

namespace App\Http\Controllers;

use App\Mail\NotifyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->data;

        try {
            Mail::to($data['toEmail'])->send(new NotifyMail($data));

            return redirect()->route('users.index');
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }
}
