<p style="text-align: center;">
    <strong>1010 EPHESIANS HUMAN RESOURCES INC.<br>
        DEPLOYMENT REPORT
        {{ $records->first()->date_deployed->format('F j, Y') }} - {{ $records->last()->date_deployed->format('F j, Y') }}
    </strong>
</p>

<p style="text-align: left;"><strong>&nbsp; &nbsp;NAME:&nbsp; {{ str()->upper(auth()->user()->name) }}</strong><br>

<strong>&nbsp; &nbsp;DATE SUBMITTED:&nbsp; {{ str()->upper(now()->format('F j, Y')) }}</strong></p>

<p style="text-align: left;"><strong>&nbsp;</strong></p>
<table style="width: 100%; border-collapse: collapse; border: 2px solid rgb(0, 0, 0);">
    <tbody>
        <tr>
            <td style="width: 6.281%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>NO.</strong></div>
            </td>
            <td style="width: 13.0755%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>NAME OF APPLICANT</strong></div>
            </td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;"><strong>POSITION</strong></td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;"><strong>PASSPORT NO.</strong></td>
            <td style="width: 13.8476%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>COMPANY</strong></div>
            </td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>COUNTRY</strong></div>
            </td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>DATE DEPLOYED</strong></div>
            </td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>STATUS</strong></div>
            </td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0);">
                <div style="text-align: center;"><strong>HANDLER</strong></div>
            </td>
        </tr>
        @foreach ($records as $key => $item)
        <tr>
            <td style="width: 6.281%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $key + 1 }}</td>
            <td style="width: 13.0755%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->worker->fullname }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->position }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->worker->workerInformation?->passport_number }}</td>
            <td style="width: 13.8476%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->foreignAgency->name }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->country }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->date_deployed->format('F j, Y') }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->status }}</td>
            <td style="width: 11.1111%; border: 2px solid rgb(0, 0, 0); text-align: center;">{{ $item->handler->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p><strong><span style="color: rgb(209, 72, 65);">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</span></strong></p>
<table style="width: 100%;">
    <tbody>
        <tr>
            <td style="width: 50.0000%;"><strong><span style="color: rgb(235, 107, 86);">PREPARED BY:</span></strong><br><br><strong><span style="color: rgb(235, 107, 86);">{{ str()->upper(auth()->user()->name) }}</span></strong><br><br></td>
            <td style="width: 50.0000%;"><strong><span style="color: rgb(226, 80, 65);">RECEIVED BY:</span></strong><br><strong><br>______________________________<br>&nbsp; &nbsp; &nbsp; Printed Name And Signature</strong></td>
        </tr>
    </tbody>
</table>
