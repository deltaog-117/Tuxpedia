@extends('layouts.app')

@section('title', 'Distro Family Tree')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Linux Distribution Family Tree</h1>
    <p class="text-tux-muted mb-4">Which distributions descend from which. Select one to open its article.</p>

    <livewire:family-tree />
</div>
@endsection
