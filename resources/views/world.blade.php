@extends('layout.dashboard-modern')
@section('title', 'World numbers · SMS')

@push('styles')
    <link rel="stylesheet" href="{{ url('') }}/public/css/verification-page.css?v=9">
    <style>
        .vf-quick-buy {
            margin-top: 0.85rem;
            padding: 0.9rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .vf-quick-buy__title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.55rem;
        }
        .vf-quick-buy__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }
        .vf-quick-chip {
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0f172a;
            border-radius: 999px;
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all .2s ease;
        }
        .vf-quick-chip:hover {
            border-color: #2563eb;
            color: #1d4ed8;
            transform: translateY(-1px);
        }
        .vf-quick-chip--active {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #fff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }
    </style>
@endpush

@section('content')

<div class="vf-shell">

    <header class="vf-hero vf-hero--world">
        <div class="vf-hero__row">
            <div class="vf-hero__lead">
                <span class="vf-hero__badge"><i class="bi bi-globe2" aria-hidden="true"></i> International</span>
                @php
                    $worldMode = (string) ($worldServer ?? 'world');
                    $isHero = $worldMode === 'world_hero';
                    $isSv3 = $worldMode === 'world_sv3';
                    $isSv1 = $worldMode === 'world';
                @endphp
                <h1 class="vf-hero__title">{{ $isHero ? 'All countries SV2' : ($isSv3 ? 'All countries SV3' : 'All countries SV1') }}</h1>
                <p class="vf-hero__text">Choose a country and service, confirm price, then buy a number.</p>
            </div>
            <div class="vf-hero__stats">
                <p class="vf-hero__user">{{ Auth::user()->username }}</p>
                <p class="vf-hero__balance">₦{{ number_format(Auth::user()->wallet ?? 0, 2) }}</p>
                <p class="vf-hero__bal-label">Wallet balance</p>
                <a href="{{ url('/fund-wallet') }}" class="vf-hero__cta"><i class="bi bi-plus-lg" aria-hidden="true"></i> Fund wallet</a>
            </div>
        </div>
    </header>

    @php
        $vfServers = $verificationServers ?? ['us1' => false, 'us2' => true, 'world' => true, 'world_hero' => true, 'world_sv3' => true];
    @endphp
    <nav class="vf-servers" aria-label="Number pools">
        @if(!empty($vfServers['us1']))
            <a href="{{ route('verification.index') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
                <span class="vf-server__name">USA · Server 1</span>
            </a>
        @endif
        @if(!empty($vfServers['us2']))
            <a href="{{ url('/usa2') }}" class="vf-server">
                <span class="vf-server__flag" aria-hidden="true">🇺🇸</span>
                <span class="vf-server__name">USA · Server 1</span>
            </a>
        @endif
        @if(!empty($vfServers['world']))
            <a href="{{ url('/world') }}" class="vf-server {{ $isSv1 ? 'vf-server--active' : '' }}">
                <span class="vf-server__flag" aria-hidden="true">🌎</span>
                <span class="vf-server__name">All countries · SV1</span>
                @if($isSv1)<span class="vf-server__hint">Current panel</span>@endif
            </a>
        @endif
        @if(!empty($vfServers['world_hero']))
            <a href="{{ url('/world-sv2') }}" class="vf-server {{ $isHero ? 'vf-server--active' : '' }}">
                <span class="vf-server__flag" aria-hidden="true">🌍</span>
                <span class="vf-server__name">All countries · SV2</span>
                <span class="vf-server__tag-recommended">Recommended</span>
                @if($isHero)<span class="vf-server__hint">Current panel</span>@endif
            </a>
        @endif
        @if(!empty($vfServers['world_sv3']))
            <a href="{{ url('/world-sv3') }}" class="vf-server {{ $isSv3 ? 'vf-server--active' : '' }}">
                <span class="vf-server__flag" aria-hidden="true">🌐</span>
                <span class="vf-server__name">All countries · SV3</span>
                @if($isSv3)<span class="vf-server__hint">Current panel</span>@endif
            </a>
        @endif
    </nav>
    <p class="vf-servers-mobile-note"><i class="bi bi-arrow-right-short" aria-hidden="true"></i> Swipe right to see more servers</p>

    @if (session('topMessage'))
        <div class="vf-alert vf-alert--info" role="status">{{ session('topMessage') }}</div>
    @endif

    <div class="vf-alerts">
        @if ($errors->any())
            <div class="vf-alert vf-alert--danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session()->has('message'))
            <div class="vf-alert vf-alert--success" role="status">{{ session()->get('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="vf-alert vf-alert--danger" role="alert">{{ session()->get('error') }}</div>
        @endif
    </div>

    <div class="vf-grid">
        <article class="vf-panel vf-panel--order">
            <div class="vf-panel__head">
                <h2 class="vf-panel__title">Order a number</h2>
                <p class="vf-panel__sub">Select country, then service. We check live price and availability before you pay.</p>
            </div>
            <div class="vf-panel__body vf-world-form">
                <form id="worldForm" method="POST">
                    @csrf
                    <label class="vf-label" for="countrySelect">Country</label>
                    <select id="countrySelect" name="country" class="form-control">
                        <option value="">Select country</option>
                        @foreach ($countries as $data)
                            <option value="{{ $data['ID'] }}">{{ $data['name'] }}</option>
                        @endforeach
                    </select>

                    <label class="vf-label" for="serviceSelect">Service</label>
                    <select id="serviceSelect" name="service" class="form-control" disabled>
                        <option value="">Select a country first</option>
                    </select>

                    <div class="vf-quick-buy">
                        <p class="vf-quick-buy__title">Quick buy popular services</p>
                        <div class="vf-quick-buy__chips" role="group" aria-label="Quick buy popular services">
                            <button type="button" class="vf-quick-chip" data-quick-service="whatsapp">WhatsApp</button>
                            <button type="button" class="vf-quick-chip" data-quick-service="facebook">Facebook</button>
                            <button type="button" class="vf-quick-chip" data-quick-service="telegram">Telegram</button>
                            <button type="button" class="vf-quick-chip" data-quick-service="pof">POF</button>
                            <button type="button" class="vf-quick-chip" data-quick-service="gmail">Gmail</button>
                            <button type="button" class="vf-quick-chip" data-quick-service="signal">Signal</button>
                        </div>
                    </div>

                    <div id="priceSection" class="vf-price-panel" style="display: none;">
                        <p class="vf-price-panel__label">Price</p>
                        <p class="vf-price-panel__amount" id="priceDisplay">—</p>
                        <div id="heroPriceOptionsWrap" class="vf-hero-price-wrap d-none">
                            <p class="vf-hero-price-wrap__title">Choose a price tier</p>
                            <div id="heroPriceOptions" class="vf-hero-price-options" role="radiogroup" aria-label="World server 2 price options"></div>
                        </div>
                        <button type="button" id="buyNowBtn" class="vf-btn-buy">Buy number</button>
                    </div>
                </form>
            </div>
        </article>

        <article class="vf-panel vf-panel--requests">
            <div class="vf-panel__head vf-panel__head--split">
                <div>
                    <h2 class="vf-panel__title">Verification requests</h2>
                    <p class="vf-panel__sub">Codes update automatically. Filter applies only to this table.</p>
                </div>
                <div>
                    <label class="visually-hidden" for="vf-requests-filter">Filter requests</label>
                    <input type="search" id="vf-requests-filter" class="vf-filter-input" placeholder="Filter table…" autocomplete="off">
                </div>
            </div>
            <p class="vf-panel__note"><strong>Active numbers</strong> — tap a code to copy.</p>

            <div class="vf-table-scroll">
                <table class="vf-table" id="vf-requests-table">
                    <thead>
                    <tr>
                        <th>Service</th>
                        <th>Phone</th>
                        <th>Code</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($verification as $data)
                        @php
                            $vfDigits = preg_replace('/\D/', '', (string) $data->phone);
                            if (strlen($vfDigits) === 11 && str_starts_with($vfDigits, '1')) {
                                $vfDigits = substr($vfDigits, 1);
                            }
                            $vfPhoneDisplay = (string) $data->phone;
                            if (strlen($vfDigits) === 10) {
                                $vfPhoneDisplay = '(' . substr($vfDigits, 0, 3) . ') ' . substr($vfDigits, 3, 3) . '-' . substr($vfDigits, 6);
                            }
                            $heroCooldownEndMs = null;
                            if ((int) ($data->type ?? 0) === 9 && (int) ($data->status ?? 0) === 1 && $data->created_at) {
                                $heroCoolEnd = $data->created_at->copy()->addSeconds(120);
                                if ($heroCoolEnd->isFuture()) {
                                    $heroCooldownEndMs = (int) round($heroCoolEnd->timestamp * 1000);
                                }
                            }
                        @endphp
                        <tr class="vf-req-row"
                            data-id="{{ $data->id }}"
                            data-status="{{ $data->status }}"
                            data-phone="{{ $data->phone }}"
                            data-type="{{ $data->type }}">
                            <td data-label="Service">{{ $data->service }}</td>
                            <td class="vf-phone-cell" data-label="Phone">
                                <div class="vf-copy-row">
                                    <span class="vf-mono vf-phone-display">{{ $vfPhoneDisplay }}</span>
                                    <button type="button" class="vf-btn-copy" data-copy="{{ e($data->phone) }}" title="Copy full number" aria-label="Copy phone number">
                                        <i class="bi bi-clipboard" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                            <td data-label="Code">
                                <div id="smsContainer{{ $data->id }}" class="vf-sms-cell">
                                    <div class="vf-code-loader" id="loader{{ $data->id }}">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
                                        <span class="vf-code-loading-text">Waiting for SMS…</span>
                                    </div>
                                    <div id="vf-sms-wrap{{ $data->id }}" class="vf-sms-wrap d-none">
                                        <span id="data-sm{{ $data->id }}" class="vf-sms-code" title="Tap to copy"></span>
                                        <button type="button" class="vf-btn-copy vf-btn-copy--sms" id="vf-copy-sms{{ $data->id }}" hidden aria-label="Copy SMS">
                                            <i class="bi bi-clipboard" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div id="extraSmsList{{ $data->id }}" class="vf-extra-codes d-none"></div>
                                </div>
                            </td>
                            <td data-label="Price">₦{{ number_format($data->cost, 2) }}</td>
                            <td data-label="Status" id="vf-status-cell-{{ $data->id }}">
                                @if ($data->status == 1)
                                    <div class="vf-status-row">
                                        <span class="vf-status vf-status--pending">Pending</span>
                                        <form method="POST"
                                              action="{{ (int) $data->type === 3 ? url('delete-order-usa2?id='.$data->id.'&delete=1') : url('delete-order?id='.$data->id.'&delete=1') }}"
                                              class="d-inline vf-cancel-form"
                                              @if($heroCooldownEndMs) data-hero-cooldown-end="{{ $heroCooldownEndMs }}" @endif
                                              onsubmit="return confirmDeleteWorld(event, this);">
                                            @csrf
                                            <span class="vf-cancel-inline">
                                                <button type="submit" class="vf-btn-del @if($heroCooldownEndMs) vf-btn-del--locked @endif" @if($heroCooldownEndMs) disabled aria-disabled="true" @endif>Cancel</button>
                                                @if($heroCooldownEndMs)
                                                    <span class="vf-hero-cancel-hint" role="status" aria-live="polite"></span>
                                                @endif
                                            </span>
                                        </form>
                                    </div>
                                @else
                                    <span class="vf-status vf-status--done">Completed</span>
                                @endif
                            </td>
                            <td class="vf-date-cell" data-label="Date">{{ $data->created_at?->format('M j, Y g:i A') ?? $data->created_at }}</td>
                        </tr>
                    @empty
                        <tr class="vf-empty-row">
                            <td colspan="6" class="vf-empty">No verification requests yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function vfFlashCopy(btn) {
        if (!btn) return;
        var ic = btn.querySelector('i');
        if (!ic) return;
        var prev = ic.className;
        ic.className = 'bi bi-check2';
        setTimeout(function () { ic.className = prev; }, 1100);
    }

    function confirmDeleteWorld(event, form) {
        event.preventDefault();
        var btn = form.querySelector('.vf-btn-del');
        if (btn && btn.disabled) return false;
        Swal.fire({
            title: 'Cancel order?',
            text: 'Are you sure you want to cancel this verification?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'Keep it'
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
        return false;
    }

    function vfInitHeroCancelCooldown() {
        document.querySelectorAll('form.vf-cancel-form[data-hero-cooldown-end]').forEach(function (form) {
            var end = parseInt(form.getAttribute('data-hero-cooldown-end'), 10);
            var btn = form.querySelector('.vf-btn-del');
            var hint = form.querySelector('.vf-hero-cancel-hint');
            if (!btn || !end || isNaN(end)) return;

            function fmt(secs) {
                var m = Math.floor(secs / 60);
                var s = secs % 60;
                return m + ':' + (s < 10 ? '0' : '') + s;
            }

            function tick() {
                var left = Math.ceil((end - Date.now()) / 1000);
                if (left <= 0) {
                    btn.disabled = false;
                    btn.removeAttribute('aria-disabled');
                    btn.classList.remove('vf-btn-del--locked');
                    if (hint) {
                        hint.textContent = '';
                        hint.classList.add('d-none');
                    }
                    form.removeAttribute('data-hero-cooldown-end');
                    return;
                }
                btn.disabled = true;
                btn.setAttribute('aria-disabled', 'true');
                if (hint) {
                    hint.classList.remove('d-none');
                    hint.textContent = 'Cancel unlocks in ' + fmt(left);
                }
                setTimeout(tick, 1000);
            }
            tick();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        vfInitHeroCancelCooldown();
        var el = document.getElementById('vf-requests-filter');
        var table = document.getElementById('vf-requests-table');
        if (!el || !table) return;
        table.addEventListener('click', function (e) {
            var btn = e.target.closest('.vf-btn-copy[data-copy]');
            if (!btn || btn.hasAttribute('disabled')) return;
            var t = btn.getAttribute('data-copy');
            if (!t) return;
            e.preventDefault();
            navigator.clipboard.writeText(t).then(function () { vfFlashCopy(btn); });
        });
        el.addEventListener('input', function () {
            var filter = el.value.toLowerCase().trim();
            table.querySelectorAll('tbody tr').forEach(function (row) {
                if (row.classList.contains('vf-empty-row')) {
                    row.style.display = '';
                    return;
                }
                var text = row.textContent.toLowerCase();
                row.style.display = !filter || text.includes(filter) ? '' : 'none';
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        function vfIsWaitingSmsMessage(msg) {
            return /waiting\s*for\s*sms/i.test(String(msg || ''));
        }
        function vfExtractOtp(msg) {
            var s = String(msg || '').trim();
            var m = s.match(/\b(\d{4,8})\b/);
            return m ? m[1] : s;
        }
        function vfMarkVerificationRowCompleted(row, id) {
            if (!row || !id) return;
            row.setAttribute('data-status', '2');
            var cell = document.getElementById('vf-status-cell-' + id);
            if (cell) {
                cell.innerHTML = '<span class="vf-status vf-status--done">Completed</span>';
            }
        }

        document.querySelectorAll('tr.vf-req-row').forEach(function (row) {
            var id = row.dataset.id;
            var status = parseInt(row.dataset.status || '0', 10);
            var phone = row.dataset.phone;
            var type = parseInt(row.dataset.type || '0', 10);

            var smsSpan = document.getElementById('data-sm' + id);
            var loader = document.getElementById('loader' + id);
            var wrap = document.getElementById('vf-sms-wrap' + id);
            var copySmsBtn = document.getElementById('vf-copy-sms' + id);
            var extraList = document.getElementById('extraSmsList' + id);

            var countdownTimer = null;
            var lastCodes = [];

            var mainUrl = type === 3
                ? @json(url('/get-smscode-usa2')) + '?num=' + encodeURIComponent(phone)
                : @json(url('/get-smscode')) + '?num=' + encodeURIComponent(phone);
            var fetchUrl = @json(url('/check-more-sms')) + '?num=' + encodeURIComponent(phone);

            function fetchMainSMS() {
                return fetch(mainUrl)
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var msg = (data && data.message) ? String(data.message).trim() : '';
                        var st = (data && data.status !== undefined && data.status !== null)
                            ? parseInt(data.status, 10) : NaN;
                        if (!vfIsWaitingSmsMessage(msg) && msg.length > 0) {
                            if (loader) loader.classList.add('d-none');
                            if (smsSpan) {
                                if (wrap) wrap.classList.remove('d-none');
                                smsSpan.textContent = msg;
                                if (copySmsBtn) {
                                    copySmsBtn.hidden = false;
                                    copySmsBtn.setAttribute('data-copy', msg);
                                }
                                smsSpan.onclick = function () {
                                    navigator.clipboard.writeText(msg).then(function () {
                                        smsSpan.innerHTML = msg + ' <i class="bi bi-check2 text-success"></i>';
                                        setTimeout(function () { smsSpan.textContent = msg; }, 600);
                                    });
                                };
                            }
                            if (countdownTimer) clearInterval(countdownTimer);
                            vfMarkVerificationRowCompleted(row, id);
                        } else if (st === 2) {
                            vfMarkVerificationRowCompleted(row, id);
                        }
                    })
                    .catch(function (err) { console.error('[MAIN FETCH ERROR]', err); });
            }

            function fetchExtraCodes() {
                return fetch(fetchUrl)
                    .then(function (res) { return res.json(); })
                    .then(function (result) {
                        var messages = Array.isArray(result) ? result : (result.codes || []);
                        var valid = messages.map(function (msg) {
                            var raw = (msg && msg.sms !== undefined) ? msg.sms : (msg && msg.full_sms !== undefined ? msg.full_sms : msg);
                            return vfExtractOtp(String(raw || ''));
                        }).filter(Boolean);
                        valid = valid.filter(function (c, i, a) { return a.indexOf(c) === i; });
                        var mainOtp = (smsSpan && smsSpan.textContent) ? vfExtractOtp(smsSpan.textContent) : '';
                        if (mainOtp) {
                            valid = valid.filter(function (c) { return c !== mainOtp; });
                        }
                        if (valid.length > 0) {
                            if (loader) loader.classList.add('d-none');
                            if (wrap) wrap.classList.remove('d-none');
                            if (extraList) extraList.classList.remove('d-none');
                            if (countdownTimer) clearInterval(countdownTimer);
                            vfMarkVerificationRowCompleted(row, id);

                            if (JSON.stringify(valid) !== JSON.stringify(lastCodes)) {
                                extraList.innerHTML = '';
                                valid.forEach(function (code) {
                                    var div = document.createElement('div');
                                    div.className = 'vf-code-line';
                                    div.innerHTML = '<span>' + code + '</span>';
                                    extraList.appendChild(div);
                                    div.addEventListener('click', function () {
                                        navigator.clipboard.writeText(code);
                                    });
                                });
                                lastCodes = valid;
                            }
                        } else if (extraList) {
                            extraList.classList.add('d-none');
                            extraList.innerHTML = '';
                            lastCodes = [];
                        }
                    })
                    .catch(function (err) { console.error('[EXTRA FETCH ERROR]', err); });
            }

            function startCountdown() {
                fetch(@json(url('/getInitialCountdown')) + '?id=' + id)
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var secs = data.seconds || 0;
                        countdownTimer = setInterval(function () {
                            secs--;
                            if (secs <= 0) {
                                clearInterval(countdownTimer);
                                fetch(@json(url('/api/delete-order')), {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ id: parseInt(id, 10) })
                                }).then(function () { location.reload(); });
                            }
                        }, 1000);
                    })
                    .catch(function (e) { console.error('[COUNTDOWN ERROR]', e); });
            }

            function updateAll() {
                fetchMainSMS().then(function () { return fetchExtraCodes(); });
            }

            var vfPollMs = 60000;
            function updateAllIfVisible() {
                if (document.visibilityState === 'hidden') return;
                updateAll();
            }

            if (status === 2) {
                vfMarkVerificationRowCompleted(row, id);
            }
            if (status === 1) {
                startCountdown();
                updateAll();
                setInterval(updateAllIfVisible, vfPollMs);
                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState === 'visible') updateAll();
                });
            } else {
                updateAll();
            }
        });
    });

    $(document).ready(function () {
        $('select').select2();
        var pendingQuickService = null;
        var isWorldServer2 = @json($isHero);
        var isWorldServer1 = @json($isSv1);
        var worldServer1QuickIds = {
            whatsapp: '1012',
            telegram: '907',
            gmail: '395',
            signal: '829'
        };

        function normalizeServiceName(v) {
            return String(v || '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '');
        }

        function quickAliases(key) {
            var map = {
                whatsapp: ['whatsapp', 'wa'],
                facebook: ['facebook', 'fb'],
                telegram: ['telegram', 'tg'],
                pof: ['pof', 'plentyoffish', 'plentyfish'],
                gmail: ['gmail', 'googlemail', 'google', 'go'],
                signal: ['signal', 'bw']
            };
            return map[key] || [key];
        }

        function applyQuickServiceIfPossible(key) {
            // World Server 1: use explicit service IDs provided by admin.
            if (isWorldServer1 && worldServer1QuickIds[key]) {
                var targetId = String(worldServer1QuickIds[key]);
                var hasId = $('#serviceSelect option').filter(function () {
                    return String($(this).val()) === targetId;
                }).length > 0;
                if (hasId) {
                    $('#serviceSelect').val(targetId).trigger('change');
                    $('.vf-quick-chip').removeClass('vf-quick-chip--active');
                    $('.vf-quick-chip[data-quick-service="' + key + '"]').addClass('vf-quick-chip--active');
                    pendingQuickService = null;
                    return true;
                }
            }

            var aliases = quickAliases(key).map(normalizeServiceName);
            var $opts = $('#serviceSelect option');
            var matched = null;

            $opts.each(function () {
                var val = $(this).val();
                if (!val) return;
                var raw = $(this).text();
                var n = normalizeServiceName(raw);
                for (var i = 0; i < aliases.length; i++) {
                    if (n.includes(aliases[i])) {
                        matched = val;
                        return false;
                    }
                }
            });

            if (matched) {
                $('#serviceSelect').val(matched).trigger('change');
                $('.vf-quick-chip').removeClass('vf-quick-chip--active');
                $('.vf-quick-chip[data-quick-service="' + key + '"]').addClass('vf-quick-chip--active');
                pendingQuickService = null;
                return true;
            }

            return false;
        }

        $('.vf-quick-chip').on('click', function () {
            var key = String($(this).data('quick-service') || '').toLowerCase();
            if (!key) return;

            var countryID = $('#countrySelect').val();
            pendingQuickService = key;

            if (!countryID) {
                Swal.fire('Select country first', 'Choose a country, then tap the quick service again.', 'info');
                return;
            }

            if (!applyQuickServiceIfPossible(key)) {
                Swal.fire('Loading services', 'Please wait while we load services for this country.', 'info');
                $('#countrySelect').trigger('change');
            }
        });

        $('#countrySelect').on('change', function () {
            var countryID = $(this).val();
            $('#serviceSelect').html('<option value="">Loading…</option>').prop('disabled', true);

            if (countryID) {
                $.ajax({
                    url: '{{ url('/get-world-services') }}/' + countryID,
                    type: 'GET',
                    success: function (res) {
                        $('#serviceSelect').empty().append('<option value="">Select service</option>');
                        res.forEach(function (service) {
                            $('#serviceSelect').append('<option value="' + service.ID + '">' + service.name + '</option>');
                        });
                        $('#serviceSelect').prop('disabled', false).trigger('change.select2');
                        if (pendingQuickService) {
                            if (!applyQuickServiceIfPossible(pendingQuickService)) {
                                Swal.fire('Unavailable', 'That quick-buy service is not available for this country now.', 'warning');
                                pendingQuickService = null;
                                $('.vf-quick-chip').removeClass('vf-quick-chip--active');
                            }
                        }
                    }
                });
            }
        });

        $('#serviceSelect').on('change', function () {
            var country = $('#countrySelect').val();
            var service = $(this).val();

            if (country && service) {
                $.ajax({
                    url: '{{ url('/check-world-availability') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        country: country,
                        service: service
                    },
                    beforeSend: function () {
                        $('#priceSection').hide();
                        $('#heroPriceOptionsWrap').addClass('d-none').find('#heroPriceOptions').empty();
                        $('#buyNowBtn').removeData('heroApiCost');
                        Swal.fire({
                            title: 'Checking…',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: function () { Swal.showLoading(); }
                        });
                    },
                    success: function (res) {
                        Swal.close();
                        if (res.status === 'success') {
                            $('#priceSection').show();
                            if (res.price_options && res.price_options.length > 0) {
                                var opts = res.price_options;
                                var wrap = $('#heroPriceOptionsWrap');
                                var list = $('#heroPriceOptions');

                                function applyHeroOption(o) {
                                    if (!o) return;
                                    $('#priceDisplay').text('₦' + o.ngn_total_formatted);
                                    $('#buyNowBtn').data('country', country).data('service', service).data('price', o.ngn_total_formatted).data('heroApiCost', o.api_cost);
                                }

                                function syncHeroOptionHighlight() {
                                    list.find('.vf-hero-price-option').removeClass('vf-hero-price-option--active');
                                    list.find('input[name="heroPricePick"]:checked').closest('.vf-hero-price-option').addClass('vf-hero-price-option--active');
                                }

                                if (opts.length === 1) {
                                    wrap.addClass('d-none');
                                    list.empty();
                                    applyHeroOption(opts[0]);
                                } else {
                                    wrap.removeClass('d-none');
                                    list.empty();
                                    opts.forEach(function (o, i) {
                                        var id = 'heroPriceOpt' + i;
                                        var label = $('<label/>', { 'class': 'vf-hero-price-option', 'for': id });
                                        label.append($('<input/>', {
                                            type: 'radio',
                                            name: 'heroPricePick',
                                            id: id,
                                            value: String(i),
                                            class: 'vf-hero-price-option__input',
                                            checked: i === 0
                                        }));
                                        var body = $('<span/>', { 'class': 'vf-hero-price-option__body' });
                                        body.append($('<span/>', { 'class': 'vf-hero-price-option__label', text: o.label }));
                                        body.append($('<span/>', { 'class': 'vf-hero-price-option__meta', text: '₦' + o.ngn_total_formatted + ' total' }));
                                        label.append(body);
                                        list.append(label);
                                    });
                                    list.off('change.heroPrice').on('change.heroPrice', 'input[name="heroPricePick"]', function () {
                                        var idx = parseInt($(this).val(), 10);
                                        applyHeroOption(opts[idx]);
                                        syncHeroOptionHighlight();
                                    });
                                    applyHeroOption(opts[0]);
                                    syncHeroOptionHighlight();
                                }
                            } else {
                                $('#heroPriceOptionsWrap').addClass('d-none').find('#heroPriceOptions').empty();
                                $('#buyNowBtn').removeData('heroApiCost');
                                $('#priceDisplay').text('₦' + res.price);
                                $('#buyNowBtn').data('country', country).data('service', service).data('price', res.price);
                            }
                        } else {
                            Swal.fire('Unavailable', res.message || 'Try another combination', 'error');
                        }
                    },
                    error: function () {
                        Swal.close();
                        Swal.fire('Error', 'Unable to check availability', 'error');
                    }
                });
            }
        });

        $('#buyNowBtn').on('click', function () {
            var btn = $(this);
            var country = btn.data('country');
            var service = btn.data('service');
            var price = btn.data('price');
            var hc = btn.data('heroApiCost');
            if ($('#heroPriceOptionsWrap').is(':visible') && (hc === undefined || hc === null || hc === '')) {
                Swal.fire('Choose a tier', 'Select a price option above before buying.', 'info');
                return;
            }

            Swal.fire({
                title: 'Confirm purchase',
                text: 'Buy number for ₦' + price + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Buy now',
                cancelButtonText: 'Cancel'
            }).then(function (res) {
                if (!res.isConfirmed) return;

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> Getting number…');

                $.ajax({
                    url: '{{ url('/order-world-number') }}',
                    type: 'POST',
                    data: (function () {
                        var label = $('#serviceSelect option:selected').text().trim();
                        var d = {
                            _token: '{{ csrf_token() }}',
                            country: country,
                            service: service,
                            price: price
                        };
                        if (label) {
                            d.service_name = label;
                        }
                        if (hc !== undefined && hc !== null && hc !== '') {
                            d.api_cost = btn.data('heroApiCost');
                            // Backward compatibility with older backend fields.
                            d.hero_api_cost = btn.data('heroApiCost');
                        }
                        return d;
                    })(),
                    success: function (resp) {
                        try { resp = JSON.parse(resp); } catch (e) {}

                        if (resp === 3 || resp.status === 3 || resp.status === 'success') {
                            Swal.fire('Success', 'Reloading…', 'success').then(function () { location.reload(); });
                        } else if (resp === 99) {
                            Swal.fire('Error', 'Insufficient balance', 'error');
                        } else if (resp === 5) {
                            Swal.fire('Error', 'Service unavailable or failed', 'error');
                        } else if (resp === 8) {
                            Swal.fire('Error', 'Wallet not found. Please try again later.', 'error');
                        } else {
                            Swal.fire('Error', (resp && resp.message) ? resp.message : 'Something went wrong', 'error');
                        }
                    },
                    error: function (xhr) {
                        var msg = 'Unable to process your request';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr && xhr.responseText) {
                            try {
                                var parsed = JSON.parse(xhr.responseText);
                                if (parsed && parsed.message) {
                                    msg = parsed.message;
                                }
                            } catch (e) {}
                        }
                        Swal.fire('Error', msg, 'error');
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('Buy number');
                    }
                });
            });
        });
    });
</script>
@endpush
