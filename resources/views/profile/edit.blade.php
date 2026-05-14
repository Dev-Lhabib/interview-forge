@extends('layouts.app')

@section('page-title', 'Profil')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
        <div class="max-w-2xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
        <div class="max-w-2xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100 border-red-200">
        <div class="max-w-2xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
