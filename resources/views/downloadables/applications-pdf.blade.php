<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<table>
    <thead>
        <th>Position Applied</th>
        <th>Firstname</th>
        <th>lastname</th>
        <th>Middlename</th>
        <th>Contact Number</th>
        <th>E-mail</th>
        <th>Cover Letter</th>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $record->jobPost->title ?? 'None' }}</td>
                <td>{{ $record->first_name }}</td>
                <td>{{ $record->last_name }}</td>
                <td>{{ $record->middle_name }}</td>
                <td>{{ $record->contact_number }}</td>
                <td>{{ $record->email }}</td>
                <td>{{ $record->cover_letter }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
