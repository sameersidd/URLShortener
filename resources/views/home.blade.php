@extends('layouts/app')

@section('content')
<div class="content">
    @if(session('key'))
    <div>
        <p>
            Here's the shortened version: <a href="{{session('key')}}">{{session('key')}}</a>
        </p>
    </div>
    <br/><br/>
    @endif

    @if($urls->first())
        Here's your previous shortens:
    <ul style="list-style-type: none">
        @foreach ($urls as $url)
    <li><a href="{{$url->key}}">{{request()->getSchemeAndHttpHost()}}/{{$url->key}}</a></li>
        @endforeach
    </ul>
    <br/><br/>
    @endif

    @error('urlinput')
    <p>
        {{$message}}
    </p>
    @enderror
    <form action="urlshorten" method="post">
        @csrf
        <label for="input">Enter URL:</label>
        <input type="text" id="input" name="urlinput" placeholder="example.com...">
        <input type="hidden" name="ipaddress" value="{{request()->ip()}}">
        <button type="submit" class="btn btn-dark">Shorten</button>
    </form>
<br><br>
        @if(session('token'))
            <p>
                Here's your API Token: {{session('token')}}
            </p>
        @endif

        @if (session('fail'))
            <p>
                Error: {{session('fail')}}
            </p>
        @endif
        @error('ipaddress')
        <p>
            Error: {{ $message }}
        </p>
        @enderror
       <form action="/api/register" method="post">
        @csrf
        <label for="register">Register for the API</label>
        <input type="hidden" name="ipaddress" value="{{request()->ip()}}">
        <button id="register" class="btn btn-default" type="submit">Register</button>
    </form>

</div>
@endsection
