@extends('layouts/app')

@section('content')
    <form action="/urlshorten" method="post">
        @csrf
        <label for="input">Enter URL:</label>
        <input type="text" id="input" name="urlinput" placeholder="example.com...">
        <input type="hidden" name="ipaddress" value="{{request()->ip()}}">
        <button type="submit" class="btn btn-dark">Shorten</button>
    </form>

    @error('urlinput')
      <p class="invalid-feedback" role="alert">
       <strong>{{ $message }}</strong>
      </p>
     @enderror

@endsection
