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
    </style>
</head>

<body>
    <p style="text-align: center; margin-top: 25%">
        <strong>
            <span style='font-size: 22px;'>
                {{ $records->first_name }} {{ $records->last_name }}
            </span>
        </strong>
    </p>

    <p style="text-align: left; margin-left: 50px; margin-right: 50px; margin-top: 10px;"><strong><span
                style="font-family: &quot;Arial Black&quot;, Gadget, sans-serif; font-size: 19px;"></span><span
                style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><b><span
                        style="color: rgb(0, 0, 0); font-size: 14px;"><u>POSITION : {{ $records->position }}
                        </u></span></b></span><br></strong></p>

    <p style="text-align: left; margin-left: 50px; margin-right: 50px; margin-top: 10px;"><strong><span
                style="font-family: &quot;Arial Black&quot;, Gadget, sans-serif; font-size: 19px;"></span><span
                style='font-family: "Arial Black", Gadget, sans-serif; font-size: 19px;'><b><span
                        style="color: rgb(0, 0, 0); font-size: 14px;"><u>OBJECTIVES :</u> <br><br>
                        {{ $records->workerInformation->cover_letter }}
                        </span></b></span><br></strong></p>


</body>

</html>
