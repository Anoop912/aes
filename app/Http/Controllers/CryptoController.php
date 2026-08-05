<?php

namespace App\Http\Controllers;

use App\Services\CryptoService;
use Illuminate\Http\Request;

class CryptoController extends Controller
{
    protected CryptoService $crypto;

    public function __construct(CryptoService $crypto)
    {
        $this->crypto = $crypto;
    }

    public function index()
    {
        return view('crypto');
    }

    /*
    |--------------------------------------------------------------------------
    | AES-256-GCM
    |--------------------------------------------------------------------------
    */

    public function encrypt(Request $request)
    {
        $request->validate([
            'plain_text' => ['required'],
            'key' => ['required', 'size:32'],
        ]);

        try {

            $encrypted = $this->crypto->encrypt(
                $request->plain_text,
                $request->key
            );

            return back()->with([
                'encrypted' => $encrypted,
                'active_tab' => 'encrypt'
            ]);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'encrypt' => $e->getMessage()
                ]);
        }
    }

    public function decrypt(Request $request)
    {
        $request->validate([
            'encrypted_text' => ['required'],
            'key' => ['required', 'size:32'],
        ]);

        try {

            $decrypted = $this->crypto->decrypt(
                $request->encrypted_text,
                $request->key
            );

            return back()->with([
                'decrypted' => $decrypted,
                'active_tab' => 'decrypt'
            ]);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'decrypt' => $e->getMessage()
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AES-256-GCM + RSA
    |--------------------------------------------------------------------------
    */

    public function encryptRsa(Request $request)
    {
        $request->validate([
            'plain_text' => ['required'],
            'private_key' => ['required'],
            'public_key' => ['required'],
        ]);

        try {

            $encrypted = $this->crypto->encryptAes256rsa(
                $request->plain_text,
                $request->private_key,
                $request->public_key
            );

            return back()->with([
                'encrypted_rsa' => $encrypted,
                'active_tab' => 'encrypt_rsa'
            ]);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'encrypt_rsa' => $e->getMessage()
                ]);
        }
    }

    public function decryptRsa(Request $request)
    {
        $request->validate([
            'encrypted_text' => ['required'],
            'private_key' => ['required'],
            'public_key' => ['required'],
        ]);

        try {

            $decrypted = $this->crypto->decryptAes256rsa(
                $request->encrypted_text,
                $request->private_key,
                $request->public_key
            );

            return back()->with([
                'decrypted_rsa' => $decrypted,
                'active_tab' => 'decrypt_rsa'
            ]);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'decrypt_rsa' => $e->getMessage()
                ]);
        }
    }
}