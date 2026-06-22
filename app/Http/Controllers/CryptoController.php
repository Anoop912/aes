<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CryptoService;

class CryptoController extends Controller
{
    protected $crypto;

    public function __construct(CryptoService $crypto)
    {
        $this->crypto = $crypto;
    }

    public function index()
    {
        return view('crypto');
    }

    public function encrypt(Request $request)
    {
        $encrypted = $this->crypto->encrypt(
            $request->plain_text,
            $request->key
        );

        return back()->with('encrypted', $encrypted);
    }

    public function decrypt(Request $request)
    {
        $decrypted = $this->crypto->decrypt(
            $request->encrypted_text,
            $request->key
        );

        return back()->with('decrypted', $decrypted);
    }
}