@extends('layout.main')

@section('content')
    <section id="api-docs" class="my-5">
        <div class="container">

            <!-- Greeting Section -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-md-8 col-xl-6">
                    <h4 class="text-danger mb-3">Hi {{ Auth::user()->username }},</h4>
                    <p class="text-muted">Easily integrate our services into your application with our RESTful API.</p>
                </div>
            </div>

            <!-- API Key & Webhook Settings -->
            <div class="card border-0 shadow-lg p-4 mb-5 rounded-4">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <h5 class="fw-bold mb-3">API Key</h5>
                    <div class="row align-items-center mb-4">
                        <div class="col-md-8 mb-2">
                            <input type="text" class="form-control" value="{{ $api_key }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <a href="/generate-token" class="btn btn-dark w-100">Generate API Key</a>
                        </div>
                    </div>

                    <form action="set-webhook" method="POST" class="mt-4">
                        @csrf
                        <h5 class="fw-bold mb-3">Webhook URL</h5>
                        <div class="row align-items-center">
                            <div class="col-md-8 mb-2">
                                <input type="text" name="webhook" class="form-control" value="{{ $webhook_url }}" placeholder="https://yourdomain.com/webhook">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Set Webhook</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>


            <div class="card border-0 shadow-lg p-4 rounded-4">
                <div class="card-body">

                    <h3 class="fw-bold text-center mb-4">USA Numbers only Api Documentation</h3>
                    <p class="text-muted">
                        All requests must include your API key, either in the <code>ApiKey</code>  as <code>api_key</code> in the query string.
                    </p>

                    @php
                        $baseUrl = url('');
                    @endphp

                    <div class="mb-5">
                        <h5 class="fw-bold">1. Get Wallet Balance</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/balance?api_key={{ $api_key }}&action=balance"

# Success Response
{
    "status": true,
    "main_balance": 200
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">2. Get USA Only Services</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/usa-services?api_key={{ $api_key }}&action=get-usa-services"

# Success Response
{
    "status": true,
    "data":"status": true,
    "data": {
        "2redbeans": {
            "name": "2RedBeans",
            "count": 100,
            "repeatable": true,
            "cost_ngn": 1275
        }
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">3. Get World Services</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/get-world-services?api_key={{ $api_key }}&action=get-world-services"

# Success Response
{
    "status": true,
    "data": [
        { "ID": 1, "name": "1688", "favourite": 0 }
    ]
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">4. Check Number Availability</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/check-world-number-availability?api_key={{ $api_key }}&action=check-availability&country=US&service=1"

# Success Response
{
    "status": true,
    "cost": 500,
    "stock": 8444,
    "country": "US",
    "service": "1"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">5. Rent World Number</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/rent-world-number?api_key={{ $api_key }}&action=rent-world-number&country=US&service=1"

# Success Response
{
    "status": true,
    "order_id": 389,
    "phone_no": "19362441517",
    "country": "US",
    "service": "1012"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div>
                        <h5 class="fw-bold">6. Get World SMS</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/get-world-sms?api_key={{ $api_key }}&action=get-world-sms&order_id=389"

# Success Response
{
    "status": true,
    "sms_status": "COMPLETED",
    "full_sms": "Do not send code 123456 to anyone",
    "code": "123456",
    "country": "United States",
    "service": "WhatsApp"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                </div>
            </div>


            <!-- API Documentation -->
            <div class="card border-0 shadow-lg p-4 rounded-4">
                <div class="card-body">

                    <h3 class="fw-bold text-center mb-4">World Numbers API Documentation</h3>
                    <p class="text-muted">
                        All requests must include your API key, either in the <code>X-ApiKey</code> header or as <code>api_key</code> in the query string.
                    </p>

                    @php
                        $baseUrl = url('');
                    @endphp

                    <div class="mb-5">
                        <h5 class="fw-bold">1. Get Wallet Balance</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/balance?api_key={{ $api_key }}&action=balance"

# Success Response
{
    "status": true,
    "main_balance": 200
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">2. Get World Countries</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/get-world-countries?api_key={{ $api_key }}&action=get-world-countries"

# Success Response
{
    "status": true,
    "data": [
        {
            "ID": 1,
            "name": "United States",
            "short_name": "US",
            "cc": "1",
            "region": "North America"
        }
    ]
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">3. Get World Services</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/get-world-services?api_key={{ $api_key }}&action=get-world-services"

# Success Response
{
    "status": true,
    "data": [
        { "ID": 1, "name": "1688", "favourite": 0 }
    ]
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">4. Check Number Availability</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/check-world-number-availability?api_key={{ $api_key }}&action=check-availability&country=US&service=1"

# Success Response
{
    "status": true,
    "cost": 500,
    "stock": 8444,
    "country": "US",
    "service": "1"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold">5. Rent World Number</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/rent-world-number?api_key={{ $api_key }}&action=rent-world-number&country=US&service=1"

# Success Response
{
    "status": true,
    "order_id": 389,
    "phone_no": "19362441517",
    "country": "US",
    "service": "1012"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                    <div>
                        <h5 class="fw-bold">6. Get World SMS</h5>
                        <pre class="bg-light p-3 rounded">
GET "{{ $baseUrl }}/api/get-world-sms?api_key={{ $api_key }}&action=get-world-sms&order_id=389"

# Success Response
{
    "status": true,
    "sms_status": "COMPLETED",
    "full_sms": "Do not send code 123456 to anyone",
    "code": "123456",
    "country": "United States",
    "service": "WhatsApp"
}

# Error
"Wrong or Bad Api key"
                    </pre>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection
