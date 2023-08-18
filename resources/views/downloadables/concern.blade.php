<html>
<hr>
<strong>{{ $record->title }}</strong>
<br>
<br>
{!! $record->description !!}
<p>Below were the written names and concerns of oit deployed workers who need attentions from the said Foreign
    Recruitment Agency:</p>
<ol>
    @foreach ($record->concernReport as $key => $reports)
        <li style="margin-bottom: 5px">{{ $reports->worker->fullname }} - {!! $reports->feedback !!}</li>
    @endforeach
</ol>
<hr>

</html>
