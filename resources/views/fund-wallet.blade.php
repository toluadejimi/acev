@extends('layout.dashboard-modern')

@section('title', 'Fund wallet')

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/fund-wallet.css">
@endpush

@section('content')
    <div class="fw-page">
        <header class="fw-hero">
            <div>
                <p class="fw-hero__user">{{ Auth::user()->username }}</p>
                <p class="fw-hero__bal">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
                <p class="fw-hero__label">Available balance</p>
            </div>
            <a href="{{ url('/home') }}" class="fw-hero__back">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Back to dashboard
            </a>
        </header>

        <div class="fw-notice" role="note">
            <span class="fw-notice__icon" aria-hidden="true"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <div>
                <strong>Before you pay</strong> — use <strong>Fund wallet</strong> below each time you need a fresh virtual account number. Pay the <strong>exact amount</strong> you enter. Need a walkthrough?
                <a href="https://t.me/acesmsverify/8" target="_blank" rel="noopener">Deposit tutorial on Telegram</a>.
            </div>
        </div>

        <div class="fw-alerts">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success" role="alert">{{ session()->get('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
            @endif
        </div>

        @php
            $get_account = \App\Models\PaymentPoint::where('email', Auth::user()->email)->first() ?? null;
        @endphp

        <div class="fw-grid">
            <article class="fw-card">
                <div class="fw-card__head">
                    <h2 class="fw-card__title">Bank details</h2>
                    <p class="fw-card__sub">Transfer to this account to credit your wallet</p>
                </div>
                <div class="fw-card__body">
                    @if ($get_account != null)
                        <div class="fw-field">
                            <span class="fw-field__label">Account number</span>
                            <div class="fw-copy-row">
                                <p class="fw-field__value mb-0" id="accountNoText">{{ $account_no ?? 'NIL' }}</p>
                                @if (!empty($account_no))
                                    <button type="button" class="fw-btn-copy" onclick="copyAccountNo()" title="Copy account number">
                                        <i class="bi bi-clipboard" aria-hidden="true"></i> Copy
                                    </button>
                                    <span class="fw-copy-msg d-none" id="copyMsg">Copied</span>
                                @endif
                            </div>
                        </div>
                        <div class="fw-field">
                            <span class="fw-field__label">Account name</span>
                            <p class="fw-field__value">{{ $account_name ?? 'NIL' }}</p>
                        </div>
                        <div class="fw-field">
                            <span class="fw-field__label">Bank</span>
                            <p class="fw-field__value">{{ $bank_name ?? 'NIL' }}</p>
                        </div>
                        <p class="fw-tip mb-0">
                            Transfers usually reflect quickly. You can reuse this account for future deposits when shown here.
                        </p>
                    @else
                        <p class="text-secondary small mb-3">Generate a dedicated virtual account to receive bank transfers.</p>
                        <button type="button" class="fw-btn-generate" data-bs-toggle="modal" data-bs-target="#generateAccountModal">
                            Get account number
                        </button>
                    @endif
                </div>
            </article>

            <article class="fw-card">
                <div class="fw-card__head">
                    <h2 class="fw-card__title">Fund wallet</h2>
                    <p class="fw-card__sub">Enter amount and choose how you want to pay</p>
                </div>
                <div class="fw-card__body">
                    <form action="{{ url('/fund-now') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="fw-label" for="fw-amount">Amount (NGN)</label>
                            <input id="fw-amount" type="text" name="amount" class="fw-input" inputmode="decimal" autocomplete="transaction-amount" placeholder="e.g. 5000" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-label" for="fw-type">Payment mode</label>
                            <select id="fw-type" name="type" class="fw-select">
                                <option value="1">Instant</option>
                                @if ($status == 'ON')
                                    <option value="2">Manual</option>
                                @endif
                            </select>
                        </div>
                        <button type="submit" class="fw-submit">Continue to pay</button>
                    </form>
                </div>
            </article>
        </div>

        <section class="fw-table-card" aria-labelledby="fw-tx-heading">
            <div class="fw-card__head">
                <h2 class="fw-card__title" id="fw-tx-heading">Recent funding</h2>
                <p class="fw-card__sub">Your latest wallet top-ups</p>
            </div>
            <div class="fw-table-wrap">
                <table class="fw-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaction as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>₦{{ number_format($data->amount, 2) }}</td>
                                <td>
                                    @if ($data->status == 1)
                                        <span class="fw-badge fw-badge--pending">Pending</span>
                                    @elseif ($data->status == 2)
                                        <span class="fw-badge fw-badge--done">Completed</span>
                                    @else
                                        <span class="fw-badge fw-badge--pending">—</span>
                                    @endif
                                </td>
                                <td>{{ $data->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="fw-empty">No transactions yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($transaction->hasPages())
                    <div class="fw-pagination">{{ $transaction->links('vendor.pagination.bootstrap-5') }}</div>
                @endif
            </div>
        </section>
    </div>

    <div class="modal fade fw-modal" id="generateAccountModal" tabindex="-1" aria-labelledby="generateAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateAccountModalLabel">Generate account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="fw-generate-form" action="{{ url('/generate-account') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label class="fw-label" for="fw-fullname">Full name</label>
                            <input id="fw-fullname" type="text" name="fullname" class="fw-input" placeholder="As on your bank account" required>
                        </div>
                        <div class="mb-3">
                            <label class="fw-label" for="fw-phone">Phone number</label>
                            <input id="fw-phone" type="text" name="phone" class="fw-input" placeholder="Active mobile number" required>
                        </div>
                        <button type="submit" class="fw-submit" id="generateBtn">
                            <span id="btnText">Generate</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyAccountNo() {
            var el = document.getElementById('accountNoText');
            if (!el) return;
            var text = el.innerText.trim();
            navigator.clipboard.writeText(text).then(function () {
                var msg = document.getElementById('copyMsg');
                if (msg) {
                    msg.classList.remove('d-none');
                    setTimeout(function () { msg.classList.add('d-none'); }, 1500);
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('fw-generate-form');
            if (!form) return;
            form.addEventListener('submit', function () {
                var btn = document.getElementById('generateBtn');
                var txt = document.getElementById('btnText');
                var loader = document.getElementById('btnLoader');
                if (btn) btn.disabled = true;
                if (txt) txt.textContent = 'Please wait…';
                if (loader) loader.classList.remove('d-none');
            });
        });
    </script>
@endpush
