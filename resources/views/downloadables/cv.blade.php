<html>

<head>
    <style>
        html {
            margin: .5px
        }

        body {
            background-image: url("{{ $cvTemplate }}");
            background-size: cover;
            background-repeat: no-repeat;
            font-family: "Arial Black", Gadget, sans-serif;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <span style="float: right; margin-top: 15%; margin-right: 10%">
        <img src="{{ $records->getFirstMediaUrl('pic_face') }}" width="120" height="120px" alt="">
    </span>

    <p style="text-align: center; margin-top: 23%">
        <strong>
            <span style='font-size: 22px;'>
                {{ $records->first_name }} {{ $records->last_name }}
            </span>
        </strong>
    </p>

    <div style="margin-left: 50px; margin-right: 50px; ">
        <p style="text-align: left; margin-top: 1px;"><strong><span
                    style="font-family: &quot;Arial Black&quot;, Gadget, sans-serif; font-size: 19px;"></span><span
                    style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><b><span
                            style="color: rgb(0, 0, 0); font-size: 14px;"><u>POSITION :</u> {{ $records->position }}
                        </span></b></span><br></strong></p>

        <p style="text-align: left; margin-top: 10px;"><strong><span
                    style="font-family: &quot;Arial Black&quot;, Gadget, sans-serif; font-size: 19px;"></span><span
                    style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><b><span
                            style="color: rgb(0, 0, 0); font-size: 14px;"><u>OBJECTIVES :</u> <br><br>
                            {{ $records->workerInformation->cover_letter }}
                        </span></b></span><br></strong></p>

        <p style="text-align: left;"><strong><span
                    style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><strong><span
                            style="color: rgb(0, 0, 0); font-size: 14px;"><u>SKILLS
                                :</u></span></strong></span></strong></p>

        <ul style="list-style-type: disc;">
            @foreach ($records->skills as $skill)
                <li style="padding-bottom: 3px;">{{ $skill->description }}</li>
            @endforeach
        </ul>

        <p style="text-align: left;"><strong><span
                    style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><strong><span
                            style="color: rgb(0, 0, 0); font-size: 14px;"><u>EDUCATION
                                :</u></span></strong></span></strong></p>

        <ul style="list-style-type: disc;">
            @foreach ($records->education as $item)
                <li style="padding-top: 5px;">
                    <strong>
                        <span style="font-size: 17px;  text-transform: uppercase;">
                            {{ $item->level }} -
                        </span>
                    </strong>
                    <em> {{ $item->title }}</em>
                    <br>
                    {{ \Carbon\Carbon::parse($item->from_date)->format('F j, Y') }} -
                    {{ \Carbon\Carbon::parse($item->to_date)->format('F j, Y') }}
                </li>
            @endforeach
        </ul>

        <div class="page-break"></div>

        <p style="text-align: center; margin-top: 20%; text-align: left;"><strong><span
                    style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><strong><span
                            style="color: rgb(0, 0, 0); font-size: 14px;"><u>WORK EXPERIENCE
                                :</u></span></strong></span></strong></p>

        <ul style="list-style-type: disc;">
            @foreach ($records->workHistory()->orderBy('from_date', 'desc')->get() as $history)
                <li style="padding-top: 5px;">
                    <span style="font-size: 17px;">
                        <strong>{{ $history->position }}</strong> - {{ $history->address }}
                    </span>
                    <span>{{ $history->company }}</span>
                    <br>
                    {{ \Carbon\Carbon::parse($history->from_date)->format('F j, Y') }} -
                    {{ \Carbon\Carbon::parse($history->to_date)->format('F j, Y') }}
                </li>
            @endforeach
        </ul>
    </div>
</body>

</html>
