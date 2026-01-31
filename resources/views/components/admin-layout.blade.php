@props(['breadcrumb' => null])

@php
    $breadcrumbValue = trim((string) ($breadcrumb ?? 'Admin')) ?: 'Admin';
@endphp

@include('admin.layouts.app', ['slot' => $slot, 'breadcrumb' => $breadcrumbValue])
