@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')

@include('users._form', [
    'action' => route('users.update', $user),
    'method' => 'PUT',
    'user' => $user,
    'buttonText' => 'Update User'
])

@endsection
