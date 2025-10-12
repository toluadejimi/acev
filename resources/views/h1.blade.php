@extends('layout.main')
@section('content')

    <section id="technologies mt-4 my-5">


        <div class="container technology-block">


            @if(!empty($centerMessage))
                <div id="center-popup" class="popup-center my-3">
                    <div class="popup-box">
                        <h4 class="header my-3">📢 Important Announcement !! </h4>
                        <hr>
                        <span class="mb-4">{{ $centerMessage }}</span>
                        <hr class="mb-3">

                        <a href="https://t.me/acesmsverify" class="telegram-btn mt-4">Join us on Telegram</a><br><br>
                        <a href="us" class="popup-btn mt-4">Close</a><br><br>
                    </div>
                </div>
            @endif


        </div>

    </section>




    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const popup = document.getElementById("page-popup");
            const closeBtn = document.getElementById("close-popup");

            if (popup) {
                // Slide down after page load
                setTimeout(() => popup.classList.add("show"), 300);

                // Auto close after 5 seconds
                setTimeout(() => popup.classList.remove("show"), 5000);

                // Manual close
                if (closeBtn) {
                    closeBtn.addEventListener("click", () => popup.classList.remove("show"));
                }
            }
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const topPopup = document.getElementById("top-popup");
            const centerPopup = document.getElementById("center-popup");

            // Show top banner
            if (topPopup) {
                setTimeout(() => topPopup.classList.add("show"), 400);
                setTimeout(() => topPopup.classList.remove("show"), 8000);
            }

            // Show center popup a few seconds later
            if (centerPopup) {
                setTimeout(() => centerPopup.classList.add("show"), 100);
            }
        });

        function closeTopPopup() {
            document.getElementById("top-popup")?.classList.remove("show");
        }

        function closeCenterPopup() {
            document.getElementById("center-popup")?.classList.remove("show");
        }
    </script>

@endsection
