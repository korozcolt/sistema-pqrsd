<div class="top-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-sm-6">
                <ul class="left-info">
                    <li>
                        <a href="mailto:{{ $info->email ?? '' }}">
                            <i class='bx bxs-envelope'></i>
                            {{ $info->email ?? '' }}
                        </a>
                    </li>
                    <li>
                        <a href="tel:{{ $info->phone ?? '' }}">
                            <i class='bx bxs-phone-call'></i>
                            {{ $info->phone ?? '' }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-6 col-sm-6">
                <ul class="right-info">

                    <li>
                        <a href="{{ $info->facebook ?? '#' }}" target="_blank">
                            <i class='bx bxl-facebook'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $info->twitter ?? '#' }}" target="_blank">
                            <i class='bx bxl-twitter'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $info->linkedin ?? '#' }}" target="_blank">
                            <i class='bx bxl-linkedin'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $info->instagram ?? '#' }}" target="_blank">
                            <i class='bx bxl-instagram'></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
