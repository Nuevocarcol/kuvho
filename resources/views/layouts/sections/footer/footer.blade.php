@php
    $containerFooter = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }} d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>
                document.write(new Date().getFullYear())
            </script>
            Primocys All rights reserved.
        </div>
    </div>
</footer>
<!--/ Footer-->
