<?php

namespace App\Http\Controllers;
use Mail;
use Illuminate\Http\Request;
use App\Mail\infolink_mail;
class MailController extends Controller
{
    public function index()
    {
        $mailData = [
            'title' => 'Mail from Webappfix',
            'body' => 'This is for pasting email using smptp',
    ];
    mail::to('umairahmed486950@gmail.com')->send(new infolink($mailData));
    dd("Email Sent Successfully");
    }
}
