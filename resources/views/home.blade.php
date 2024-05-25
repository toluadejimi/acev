@extends('layout.main')
@section('content')

    <section id="technologies mt-4">
        <div class="container title">
            <div
                class="row justify-content-center text-center wow fadeInUp"
                data-wow-delay="0.2s"
            >
                <div class="col-md-8 col-xl-6">
                    <h4 class="mb-3 text-danger">Welcome {{Auth::user()->username}}</h4>
                    <p class="mb-0">
                        Experience the AceSMSVerify advantage today and unlock seamless,<br/> reliable SMS verifications for all your needs
                    </p>
                </div>
            </div>
        </div>

        <div class="container technology-block">

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


            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">


                            <div class="d-flex justify-content-center my-3">
                                <div class="btn-group" role="group" aria-label="Third group">
                                    <a style="font-size: 12px;" href="/home"
                                       class="btn btn-primary w-200 mt-1">
                                        🇺🇸 USA NUMBERS

                                    </a>

                                    <a style="font-size: 12px;" href="/world"
                                       class="btn btn-dark w-200 mt-1">
                                        🌎 ALL COUNTRIES

                                    </a>


                                </div>

                            </div>


                            <p class="d-flex justify-content-center">You are on 🇺🇸 USA Numbers only Panel</p>


                            <div class="">

                                <div class="p-2 col-lg-6">
                                    <input type="text" id="searchInput" class="form-control"
                                           placeholder="Search for a service..." onkeyup="filterServices()">
                                </div>


                                <div class="row my-3 p-1 text-white"
                                     style="background: #dedede; border-radius: 10px; font-size: 10px; border-radius: 12px">
                                    <div class="col-5">
                                        <h5 class="mt-2">Services</h5>
                                    </div>
                                    <div class="col">
                                        <h5 class="mt-2">Price</h5>
                                    </div>
                                </div>


                            </div>


                            <div style="height:300px; width:100%; overflow-y: scroll;" class="p-2">


                                @foreach ($services as $key => $value)
                                    <div class="row service-row">
                                        @foreach ($value as $innerKey => $innerValue)
                                            <div style="font-size: 11px" class="col-5 service-name">
                                                🇺🇸 {{ $innerValue->name }}
                                            </div>

                                            <div style="font-size: 11px" class="col">
                                                @php $cost = $get_rate * $innerValue->cost + $margin  @endphp
                                                <strong>N{{ number_format($cost, 2) }}</strong>
                                            </div>

                                            <div style="font-size: 11px" class="col">

                                            </div>


                                            <div class="col mr-3">
                                                @auth
                                                    <a class="myButton" onclick="hideButton(this)"
                                                       href="/order-now?service={{ $key }}&price={{ $cost }}&cost={{ $innerValue->cost }}&name={{ $innerValue->name }}">
                                                        <i class="fa fa-shopping-bag"> Buy</i>
                                                    </a>
                                                @else

                                                    <a class=""
                                                       href="/login">
                                                        <i class="fa fa-lock text-dark"> Login</i>
                                                    </a>
                                                @endauth


                                                <script>
                                                    function hideButton(link) {
                                                        // Hide the clicked link
                                                        link.style.display = 'none';

                                                        setTimeout(function () {
                                                            link.style.display = 'inline'; // or 'block' depending on your layout
                                                        }, 5000); // 5 seconds
                                                    }
                                                </script>


                                            </div>


                                            <hr style="border-color: #cccccc" class=" my-2">
                                        @endforeach
                                    </div>
                                @endforeach


                            </div>


                        </div>
                    </div>
                </div>

                @auth
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">

                                <div class="">

                                    <div class="p-2 col-lg-6">
                                        <strong>
                                            <h4>Verifications</h4>
                                        </strong>
                                    </div>

                                    <div>


                                        <div class="table-responsive ">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Service</th>
                                                    <th>Phone No</th>
                                                    <th>Code</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                    <th>Date</th>


                                                </tr>
                                                </thead>
                                                <tbody>


                                                @forelse($verification as $data)
                                                    <tr>
                                                        <td style="font-size: 12px;">{{ $data->id }}</td>
                                                        <td style="font-size: 12px;">{{ $data->service }}</td>
                                                        <td style="font-size: 12px; color: green"><a
                                                                href="receive-sms?phone={{ $data->id }}">{{ $data->phone }} </a>
                                                        </td>
                                                        <td style="font-size: 12px;">{{ $data->sms }}</td>
                                                        <td style="font-size: 12px;">
                                                            ₦{{ number_format($data->cost, 2) }}</td>
                                                        <td>
                                                            @if ($data->status == 1)
                                                                <span
                                                                    style="background: orange; border:0px; font-size: 10px"
                                                                    class="btn btn-warning btn-sm">Pending</span>
                                                                <a href="cancle-sms?id={{  $data->id }}&delete=1"
                                                                   style="background: rgb(168, 0, 14); border:0px; font-size: 10px"
                                                                   class="btn btn-warning btn-sm">Delete</span>

                                                                    @else
                                                                        <span style="font-size: 10px;"
                                                                              class="text-white btn btn-success btn-sm">Completed</span>
                                                            @endif

                                                        </td>
                                                        <td style="font-size: 12px;">{{ $data->created_at }}</td>
                                                    </tr>

                                                @empty

                                                    <h6>No verification found</h6>
                                                @endforelse

                                                </tbody>

                                                {{ $verification->links() }}

                                            </table>
                                        </div>
                                    </div>


                                </div>


                            </div>
                        </div><!-- [ sample-page ] end -->
                        @endauth
                    </div>
            </div>
        </div>

    </section>



    <script>
        function filterServices() {
            var input, filter, serviceRows, serviceNames, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            serviceRows = document.getElementsByClassName("service-row");
            for (i = 0; i < serviceRows.length; i++) {
                serviceNames = serviceRows[i].getElementsByClassName("service-name");
                txtValue = serviceNames[0].textContent || serviceNames[0].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    serviceRows[i].style.display = "";
                } else {
                    serviceRows[i].style.display = "none";
                }
            }
        }
    </script>

@endsection
