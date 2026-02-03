@extends('layouts.admin')

@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')

@include('users._form', [
    'action' => route('users.store'),
    'buttonText' => 'Create User'
])

@endsection
