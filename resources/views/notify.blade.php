@extends('layout.admin')
@section('content')

    <main class="main-content">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
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

        <header class="header">
            <div class="header-left">
                <div class="admin-badge">ADMIN</div>
                <div class="admin-role">User announcement</div>
            </div>
        </header>

        <section class="welcome-section">
            <div class="breadcrumb">
                <i class="fas fa-bullhorn"></i>
                <span>Dashboard popup</span>
            </div>
            <div class="welcome-message">
                <h1>Announcement</h1>
                <p class="text-muted mb-0">Shown as a modern popup to users on the main app (dashboard, verification, wallet, etc.). Turn off anytime.</p>
            </div>
        </section>

        <section class="my-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ url('save-notification') }}" method="POST">
                                @csrf
                                <input type="hidden" name="is_active" value="0">

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="annActive"
                                           {{ (string) old('is_active', ($notifyActive ?? false) ? '1' : '0') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="annActive">Show announcement popup to users</label>
                                    <div class="small text-muted mt-1">When off, the popup is hidden even if a message is saved.</div>
                                </div>

                                <label for="annTitle" class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" id="annTitle" class="form-control mb-3"
                                       value="{{ old('title', $notifyTitle ?? '') }}"
                                       placeholder="e.g. Scheduled maintenance"
                                       maxlength="255">

                                <label for="message" class="form-label fw-semibold">Message</label>
                                <textarea name="message" id="message" class="form-control" rows="6"
                                          placeholder="Plain text — line breaks are preserved.">{{ old('message', $notify ?? '') }}</textarea>
                                <div class="small text-muted mt-2">Users who dismiss the popup won’t see it again until you change the title or message (or they clear site data).</div>

                                <button type="submit" class="btn btn-primary mt-4 px-4">Save announcement</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

@endsection
