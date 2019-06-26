<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\URLs;
use App\Keys;
use App\APIKey;

class URLController extends Controller
{
    public function index()
    {
        $ip = request()->ip();
        $urls_query = URLs::where('ip_address', $ip)->select('id')->get();
        $urls = Keys::whereIn('url_id', $urls_query)->select('key')->get();
        return view('home')->with('urls', $urls);
    }

    public function saveURL()
    {
        request()->validate([
            'urlinput' => 'bail|required|url',
            'ipaddress' => ''
        ]);

        $url = URLs::where('url', request('urlinput'))->first();
        if ($url !== null) {
            $Key = $url->key()->key;
            return redirect()->route('home')->with('key', request()->getSchemeAndHttpHost() . "/${Key}");
        }

        $url = new URLs();
        $url->ip_address = request()->ip();
        $url->url = request()->input('urlinput');
        $url->save();

        $key = new Keys();
        $key->key = ShortURL::encode($url->id);
        $key->url_id = $url->id;
        $url->key()->save($key);
        $Key = $key->key;
        return redirect()->route('home')->with('key', request()->getSchemeAndHttpHost() . "/${Key}");
    }

    public function redirectURL($key)
    {
        $Key = Keys::where('key', $key)->firstOrFail();
        $uid = ShortURL::decode($Key->key);
        $url = URLs::find($uid);
        $URL = $url->url;

        $parse = parse_url($URL);
        if (empty($parse['scheme'])) {
            $URL = "http://" . ltrim($URL, '/');
        }

        return redirect()->away("{$URL}");
    }

    public function register()
    {
        // request()->validate([
        //     'ipaddress' => 'unique:a_p_i_keys,ip_address'
        // ]);
        $ip = request()->ip();
        $Key = APIKey::where('ip_address', $ip)->first();
        if ($Key !== null) {
            $token = $Key->token;
            return redirect('home')->with('fail', "Already Registered with token: ${token}");
        }

        $register = new APIKey();
        $register->ip_address = $ip;
        $register->token = ShortURL::getToken(10);
        $register->save();

        return redirect('home')->with('token', $register->token);
    }

    public function apiView($key)
    {
        $ip = request()->ip();
        $IP = APIKey::where('ip_address', $ip)->first();

        if (request()->isJson())
            return response()->json([
                'error' => 'Request using JSON only!'
            ], 400);
        else if ($IP == null)
            return response()->json([
                'error' => 'IP Not Registered!'
            ], 400);
        else if ($IP->token !== $key)
            return response()->json([
                'error' => 'Token Incorrect!'
            ]);
        else {
            $urls_query = URLs::where('ip_address', $ip)->select('id')->get();
            $URLs = Keys::whereIn('url_id', $urls_query)->select('key')->get();

            return response()->json($URLs);
        }
    }

    public function APIsaveURL($key)
    {
        $ip = request()->ip();
        $IP = APIKey::where('ip_address', $ip)->firstorFail();

        if (!request()->isJson())
            return response()->json([
                'error' => 'Request using JSON only!'
            ], 400);
        else if ($IP == null)
            return response()->json([
                'error' => 'IP Not Registered!'
            ], 400);
        else if ($IP->token !== $key)
            return response()->json([
                'error' => 'Token Incorrect!'
            ]);
        else if (!(request()->has('url'))) {
            return response()->json([
                'error' => '"url" key is not defined!'
            ]);
        } else {
            if (filter_var(request()->input('url'), FILTER_VALIDATE_URL) === FALSE) {
                return response()->json([
                    'error' => 'Please input a valid url!'
                ]);
            }
            $url = new URLs();
            $url->ip_address = request()->ip();
            $url->url = request()->input('url');
            $url->save();

            $key = new Keys();
            $key->key = ShortURL::encode($url->id);
            $key->url_id = $url->id;
            $key->save();
            $Key = $key->key;

            return response()->json([
                'status' => 'Success',
                'shorten' => request()->getSchemeAndHttpHost() . "/${Key}"
            ]);
        }
    }
}

class ShortURL
{
    const ALPHABET = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ-_';
    const BASE = 51; // strlen(self::ALPHABET)
    public static function encode($num)
    {
        $str = '';
        while ($num > 0) {
            $str = self::ALPHABET[($num % self::BASE)] . $str;
            $num = (int)($num / self::BASE);
        }
        return $str;
    }
    public static function decode($str)
    {
        $num = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
        }
        return $num;
    }

    public static function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}
