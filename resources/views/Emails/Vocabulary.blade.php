<x-mail::message>
# Introduction

The body of your message.

@if (count($vocabularies)>0)
    <table border="1" width="100%" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>English</th>
                <th>Dutch</th>
                <th>Arabic</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vocabularies as $vocabulary)
            <tr>
                <td>{{ $vocabulary['en'] }}</td>
                <td>{{ $vocabulary['nl'] }}</td>
                <td>{{ $vocabulary['ar'] ?? 'No translation' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if (count($grammar)>0)
    <table border="1" width="100%" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Vocabulary</th>
                <th>Presens</th>
                <th>Perfectum</th>
                <th>Imperfectum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grammar as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['presens'] }}</td>
                <td>{{ $item['perfectum'] }}</td>
                <td>{{ $item['imperfectum'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

</x-mail::message>
