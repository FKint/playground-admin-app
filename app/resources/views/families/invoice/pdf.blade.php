<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        div.organization_details {
            width: 30%;
            float: left;
        }

        div.organization_logo {
            float: left;
            width: 65%;
        }
        div.organization_logo img {
            width: 100%;
        }

        div.child_family_info {
            margin-bottom: 30px;
        }

        table#invoice_info_table {
            width: 100%;
        }

        table#invoice_info_table th {
            font-weight: bold;
            text-decoration: underline;
        }

        div.invoice_details {
            margin-bottom: 10px;
        }

        table#invoice_entries_table {
            border-collapse: collapse;
            margin: 0px;
            width: 100%;
        }

        table#invoice_entries_table th,
        table#invoice_entries_table td {
            border: 1px solid black;
        }

        table#invoice_entries_table th {
            text-align: center;
        }

        div#invoice_details_total {
            text-align: right;
            width: 100%;
            font-weight: bold;
        }

        div.invoice_call_to_action {
            background-color: darkgray;
            border: 1px solid gray;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="organization_details">
            {!! nl2br($year->invoice_header_text) !!}
        </div>

        <div class="organization_logo">
            <img src="{{ 'data:image/jpeg;base64,'.base64_encode( $year->invoice_header_image ) }}" />
        </div>
    </div>

    <h1>Uitnodiging tot betaling</h1>

    <div class="child_family_info">
        <table id="invoice_info_table">
            <tr>
                <th colspan="2">Deelnemer</th>
                <th>Contact</th>
                <th>Periode</th>
                <th>Referentie</th>
            </tr>

            <tr>
                <th>Voornaam</th>
                <td>{{ $child->first_name }}</td>

                <td>
                    @if($family->social_contact)
                        $family->social_contact
                    @else
                        N/A
                    @endif
                </td>

                <td>
                    {{ $year->title }}
                </td>

                <td>{{ $reference }}</td>
            </tr>
            <tr>
                <th>Naam</th>
                <td>{{ $child->last_name }}</td>
            </tr>
        </table>
    </div>

    <div class="invoice_details">
        <table id="invoice_entries_table">
            <tr>
                <th rowspan="2">Lijn</th>
                <th colspan="2">Datum</th>
                <th>Basis</th>
                <th colspan="{{ 1+$year->supplements()->count() }}">Extra's</th>
                <th rowspan="2">Subtotaal</th>
            </tr>
            <tr>
                <th>Van</th>
                <th>Tot</th>
                <th>Deelname</th>

                @foreach ($year->supplements as $supplement)
                <th>{{ $supplement->name }}</th>
                @endforeach
                <th>Overige</th>
            </tr>
            @foreach($invoice as $entry)
            <tr>
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    @if (isset($entry['from']))
                    {{ $entry['from']->date()->format('Y-m-d') }}
                    @endif
                </td>
                <td>
                    @if (isset($entry['until']))
                    {{ $entry['until']->date()->format('Y-m-d') }}
                    @endif
                </td>
                <td>
                    @if (isset($entry['registration_price']))
                    € {{ number_format($entry['registration_price'], 2) }}
                    @endif
                </td>
                @foreach ($year->supplements as $supplement)
                <td>
                    @if (isset($entry['supplements'][$supplement->id]))
                    € {{ number_format($entry['supplements'][$supplement->id], 2) }}
                    @endif
                </td>
                @endforeach
                <td>
                    @if(isset($entry['other']['total']))
                    € {{ number_format($entry['other']['total'], 2) }}
                    @push('footnotes')
                    @foreach($entry['other']['items'] as $activity)
                    <li>
                        Lijn {{ $loop->iteration }}:
                        @if(isset($activity->date))
                        {{ $activity->date->format('Y-m-d') }}
                        @endif
                        {{ $activity->name }} (€ {{ number_format($activity->price, 2) }})
                    </li>
                    @endforeach
                    @endpush
                    @endif
                </td>
                <td>
                    € {{ number_format($entry['total'], 2) }}
                </td>
            </tr>
            @endforeach
        </table>

        <div id="invoice_details_total">
            Totaal: € {{ number_format($total, 2) }}
        </div>
    </div>

    <div class="invoice_call_to_action">
        Gelieve {{ number_format($total, 2) }} euro over te schrijven op rekeningnummer
        {{ $year->invoice_bank_account }} met referentie
        {{ $reference }}.
    </div>

    @if($footnotesRequired)
    <div class="footnotes">
        <h3>Toelichting</h3>
        <ul>
            @stack('footnotes')
        </ul>
    </div>
    @endif
</body>

</html>