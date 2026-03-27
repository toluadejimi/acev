@extends('layout.admin')
@section('content')

    <main class="main-content">


        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif



        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">Price setting</div>
            </div>

        </header>

        <!-- Page Title -->
        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-exchange-alt"></i>
                <span>Price Setting</span>
            </div>
            <div class="welcome-message">
            </div>
        </section>


        <section class="my-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <form action="{{ url('set-verification-server-card/us2') }}" method="post" class="card h-100 border-0 shadow-sm">
                        @csrf
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">USA Server 1</h5>
                                    <p class="text-muted small mb-0">Unlimited integration · <code>/usa2</code></p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="us2-enabled" name="enabled" {{ !empty($verificationServerFlags['us2']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="us2-enabled">Enabled</label>
                                </div>
                            </div>
                            <label class="form-label mt-3">Unlimited Rate Setting</label>
                            <input name="rate" class="form-control" type="number" step="0.0001" min="0" value="{{ $set3->rate }}" required>
                            <label class="form-label mt-3">Unlimited Margin Setting</label>
                            <input name="margin" class="form-control" type="number" step="0.0001" min="0" value="{{ $set3->margin }}" required>
                            <label class="form-label mt-3">API Key</label>
                            <input name="api_key" class="form-control" value="{{ $verificationServerKeys['us2'] ?? '' }}" placeholder="Enter Unlimited API key">
                            <button class="btn btn-primary w-100 mt-4" type="submit">Save USA Server 1</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <form action="{{ url('set-verification-server-card/world') }}" method="post" class="card h-100 border-0 shadow-sm">
                        @csrf
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">All Countries (World)</h5>
                                    <p class="text-muted small mb-0">SMS Pool integration · <code>/world</code></p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="world-enabled" name="enabled" {{ !empty($verificationServerFlags['world']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="world-enabled">Enabled</label>
                                </div>
                            </div>
                            <label class="form-label mt-3">SMS Pool Rate Setting</label>
                            <input name="rate" class="form-control" type="number" step="0.0001" min="0" value="{{ $set2->rate }}" required>
                            <label class="form-label mt-3">SMS Pool Margin Setting</label>
                            <input name="margin" class="form-control" type="number" step="0.0001" min="0" value="{{ $set2->margin }}" required>
                            <label class="form-label mt-3">API Key</label>
                            <input name="api_key" class="form-control" value="{{ $verificationServerKeys['world'] ?? '' }}" placeholder="Enter SMS Pool API key">
                            <button class="btn btn-primary w-100 mt-4" type="submit">Save World Server</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4">
                    <form action="{{ url('set-verification-server-card/world_hero') }}" method="post" class="card h-100 border-0 shadow-sm">
                        @csrf
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">All Countries (Sv2)</h5>
                                    <p class="text-muted small mb-0">World SV2 · <code>/world-sv2</code></p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="world-sv2-enabled" name="enabled" {{ !empty($verificationServerFlags['world_hero']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="world-sv2-enabled">Enabled</label>
                                </div>
                            </div>
                            <label class="form-label mt-3">HeroSMS Rate Setting</label>
                            <input name="rate" class="form-control" type="number" step="0.0001" min="0" value="{{ $verificationServerRates['world_hero'] ?? 0 }}" required>
                            <label class="form-label mt-3">HeroSMS Margin Setting</label>
                            <input name="margin" class="form-control" type="number" step="0.0001" min="0" value="{{ $verificationServerMargins['world_hero'] ?? 0 }}" required>
                            <label class="form-label mt-3">API Key</label>
                            <input name="api_key" class="form-control" value="{{ $verificationServerKeys['world_hero'] ?? '' }}" placeholder="Enter HeroSMS API key">
                            <button class="btn btn-primary w-100 mt-4" type="submit">Save HeroSMS Server</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Transactions Table -->
    </main>

@endsection
