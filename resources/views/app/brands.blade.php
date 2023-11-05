@extends('layouts.app')

@section('content')
    <brands-component csrf_token="{{ @csrf_token() }}"></brands-component>
@endsection
