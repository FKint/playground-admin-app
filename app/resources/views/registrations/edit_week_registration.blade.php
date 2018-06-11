@extends('layouts.internal')

@push('styles')
    <style>
        #registration-table {
            white-space: nowrap;
            table-layout: auto;
        }

        #registration-table-div {
            overflow-x: scroll;
        }

        col.registration-child-col:nth-child(even) {
            background-color: rgb(252, 252, 252);
        }

        col.registration-child-col:nth-child(odd) {
            background-color: rgb(240, 240, 240);
        }
    </style>
@endpush

@section('content')
    <h1>Wijzig registratie voor familie {{ $family->id }}
        : {{$family->guardian_first_name}} {{$family->guardian_last_name}}</h1>
    <div class="row">
        <div class="col-xs-9" id="registration-table-div">
            <table id="registration-table" class="table table-condensed" data-populating="0" data-nb-requests="0">
                <colgroup>
                    <col style="font-weight:bold" span="2">
                    @foreach($family->children as $child)
                        <col span="4" class="registration-child-col">
                    @endforeach
                </colgroup>
                <tr>
                    <th colspan="2">Kind</th>
                    @foreach($family->children as $child)
                        <th colspan="4" class="th-child" data-child-id="{{ $child->id }}">
                            {{ $child->first_name }} {{ $child->last_name }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <th colspan="2">Werking</th>
                    @foreach($family->children as $child)
                        <td colspan="4">
                            {{ $child->age_group->name }}
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th colspan="2">Week</th>
                    @foreach($family->children as $child)
                        <td colspan="4" data-child-id="{{$child->id}}" class="whole-week-registration">
                            <input class="registration-checkbox" title="Registreer voor volledige week"
                                   type="checkbox"/>
                            <span class="price">€ 0.00</span>
                        </td>
                    @endforeach
                </tr>
                @foreach($week->playground_days as $playground_day)
                    <tr data-week-day-id="{{$playground_day->week_day->id}}"
                        data-date="{{ $playground_day->date()->format('Y-m-d') }}"
                        class="day-row">
                        <td>{{ $playground_day->week_day->name }}</td>
                        <td>{{ $playground_day->date()->format('Y-m-d') }}</td>
                        @foreach($family->children as $child)
                            <td class="day-registration" data-child-id="{{$child->id}}">
                                <input class="registration-checkbox" title="Registreer voor deze dag" type="checkbox"/>
                                <span class="price">€ 0.00</span>
                            </td>
                            <td class="day-age-group" data-child-id="{{$child->id}}">
                                <select class="age-group" title="Werking">
                                    @foreach($year->age_groups as $age_group)
                                        <option value="{{ $age_group->id }}">{{ $age_group->abbreviation }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="day-day-part" data-child-id="{{$child->id}}">
                                <select class="day-part" title="Dagdeel">
                                    @foreach($year->day_parts as $day_part)
                                        <option value="{{ $day_part->id }}">{{ $day_part->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="day-attendance" data-child-id="{{$child->id}}">
                                <input type="checkbox" title="Aanwezig" class="attendance-checkbox"/>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">Extraatjes</th>
                    @foreach($family->children as $child)
                        <td colspan="4">
                        </td>
                    @endforeach
                </tr>
                @foreach($week->playground_days as $playground_day)
                    @foreach($year->supplements as $supplement)
                        <tr data-supplement-id="{{ $supplement->id }}"
                            data-week-day-id="{{ $playground_day->week_day->id }}">
                            <td>{{ $playground_day->week_day->name }}</td>
                            <td>{{ $supplement->name }}</td>
                            @foreach($family->children as $child)
                                <td class="day-supplement" data-child-id="{{ $child->id }}">
                                    <input class="registration-checkbox" title="Extraatje bestellen" type="checkbox"/>
                                    <span class="price">€ 0.00</span>
                                </td>
                                <td colspan="3"></td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <th colspan="2">Lijsten</th>
                    @foreach($family->children as $child)
                        <td colspan="4">
                        </td>
                    @endforeach
                </tr>
                @foreach($year->activity_lists->where('show_on_attendance_form', '=', true) as $activity_list)
                    <tr data-activity-list-id="{{ $activity_list->id }}">
                        <td colspan="2">{{ $activity_list->name }}</td>
                        @foreach($family->children as $child)
                            <td class="activity-list-registration" data-child-id="{{ $child->id }}">
                                <input class="registration-checkbox" type="checkbox"/>
                                <span class="price">€ 0.00</span>
                            </td>
                            <td colspan="3"></td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="col-xs-3">
            {{ Form::open(['class' => 'form-horizontal', 'id' => 'register-payment-form']) }}
            {{ Form::bsDropdown('tariff_id', $year->getAllTariffsById(), ['readonly' => true, 'disabled' => true]) }}
            {{ Form::bsNumber('saldo_difference',
            ['id' => 'saldo-difference', 'pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01', 'readonly' => true]) }}
            {{ Form::bsNumber('received_money',
            ['id' => 'received-money', 'pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01']) }}
            {{ Form::bsText('remarks', ['id' => 'remarks']) }}
            {{ Form::bsNumber('previous_saldo',
            ['id' => 'previous-saldo', 'pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01', 'readonly' => true]) }}
            {{ Form::bsNumber('new_saldo',
            ['id' => 'new-saldo', 'pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01', 'readonly' => true]) }}
            {{ Form::close() }}

            <button class="btn btn-default" id="btn-set-all-attending-today">Inchecken</button><br><br>
            <button class="btn btn-primary" id="submit-registration-data">Opslaan</button>
            <button class="btn btn-primary" id="submit-registration-data-and-next">Opslaan en volgende</button><br>
            <button class="btn btn-default" id="btn-cancel">Annuleren</button><br><br>
            <a href="{{ route('internal.show_family_transactions', ['family' => $family]) }}"
               class="btn btn-default">Transactiegeschiedenis</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const today = new Date('{{ $today->format('Y-m-d') }}');

        $(function () {
            let form = $('#register-payment-form');
            let table = $('#registration-table');

            function clearRegistrationData() {
                table.data('populating', parseInt(table.data('populating')) + 1);
                table.find('input[type=checkbox]').prop('checked', false);
                table.find('select').each(function () {
                    this.selectedIndex = 0;
                });
                table.find('span.price').html(formatPrice(0));
                table.data('populating', parseInt(table.data('populating')) - 1);
            }

            function getRegistrationFormData() {
                const data = {};
                data.tariff_id = $('select[name=tariff_id]').val();
                data.children = {};
                @foreach($family->children as $child)
                    data.children["{!! $child->id !!}"] = {days: {}, activity_lists: {}};
                @foreach($week->playground_days as $playground_day)
                    data.children["{!! $child->id !!}"]['days']["{!! $playground_day->week_day_id !!}"] = {supplements: {}};
                @endforeach
                @endforeach
                table.find('td.whole-week-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    data.children[child_id].whole_week_registered = $(this).find('.registration-checkbox').is(':checked');
                });
                table.find('td.day-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    data.children[child_id].days[week_day_id].registered = $(this).find('.registration-checkbox').is(':checked');
                });
                table.find('td.day-age-group').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    data.children[child_id].days[week_day_id].age_group_id = $(this).find('.age-group').val();
                });
                table.find('td.day-day-part').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    data.children[child_id].days[week_day_id].day_part_id = $(this).find('.day-part').val();
                });
                table.find('td.day-attendance').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const child_day_data = data.children[child_id].days[week_day_id];
                    data.children[child_id].days[week_day_id].attended = $(this).find('.attendance-checkbox').is(':checked');
                });
                table.find('td.day-supplement').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const supplement_id = $(this).parent('tr').data('supplement-id');
                    data.children[child_id].days[week_day_id].supplements[supplement_id] = {
                        ordered: $(this).find('.registration-checkbox').is(':checked')
                    };
                });
                table.find('td.activity-list-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    const activity_list_id = $(this).parent('tr').data('activity-list-id');
                    data.children[child_id].activity_lists[activity_list_id] = {
                        registered: $(this).find('.registration-checkbox').is(':checked')
                    };
                });

                data.received_money = form.find('#received-money').val();
                data.transaction_remarks = form.find('#remarks').val();
                return data;
            }

            function increaseNbRequests() {
                table.data('nb-requests', parseInt(table.data('nb-requests')) + 1);
            }

            function decreaseNbRequests() {
                table.data('nb-requests', parseInt(table.data('nb-requests')) - 1);
            }

            function requestsOutstanding() {
                const nb_requests = parseInt(table.data('nb-requests'));
                return nb_requests > 0;
            }

            function loadCurrentRegistrationData(callback) {
                increaseNbRequests();
                $.get('{{ route('api.registration_data', ['week'=>$week, 'family'=>$family]) }}',
                    function (result) {
                        decreaseNbRequests();
                        callback(result);
                    },
                    "json"
                );
            }

            function clearTransactionData() {
                form.find('#remarks').val('');
            }

            function populateRegistrationData(data) {
                if (requestsOutstanding()) {
                    console.log('requests outstanding');
                    return;
                }
                table.data('populating', parseInt(table.data('populating')) + 1);
                clearRegistrationData();
                console.log("Populating: ", data);
                // TODO: assert that the children linked to this family have not been changed in the meantime
                form.find('select[name=tariff_id]').val(data.tariff_id);
                table.find('td.whole-week-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    $(this).find('.registration-checkbox').prop('checked', data.children[child_id].whole_week_registered);
                    $(this).find('.price').html(formatPrice(data.children[child_id].whole_week_price))
                });
                table.find('td.day-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const child_day_data = data.children[child_id].days[week_day_id];
                    $(this).find('.registration-checkbox').prop('checked', child_day_data.registered);
                    $(this).find('.price').html(formatPrice(child_day_data.day_price));
                });
                table.find('td.day-age-group').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const child_day_data = data.children[child_id].days[week_day_id];
                    $(this).find('.age-group').val(child_day_data.age_group_id);
                });
                table.find('td.day-day-part').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const child_day_data = data.children[child_id].days[week_day_id];
                    $(this).find('.day-part').val(child_day_data.day_part_id);
                });
                table.find('td.day-attendance').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const child_day_data = data.children[child_id].days[week_day_id];
                    $(this).find('.attendance-checkbox').prop('checked', child_day_data.attended);
                });
                table.find('td.day-supplement').each(function () {
                    const child_id = $(this).data('child-id');
                    const week_day_id = $(this).parent('tr').data('week-day-id');
                    const supplement_id = $(this).parent('tr').data('supplement-id');
                    if (!(supplement_id in data.children[child_id].days[week_day_id].supplements)) {
                        return;
                    }
                    const child_day_supplement_data = data.children[child_id].days[week_day_id].supplements[supplement_id];
                    $(this).find('.registration-checkbox').prop('checked', child_day_supplement_data.ordered);
                    $(this).find('.price').html(formatPrice(child_day_supplement_data.price));
                });
                table.find('td.activity-list-registration').each(function () {
                    const child_id = $(this).data('child-id');
                    const activity_list_id = $(this).parent('tr').data('activity-list-id');
                    if (!(activity_list_id in data.children[child_id].activity_lists)) {
                        return;
                    }
                    const activity_list_data = data.children[child_id].activity_lists[activity_list_id];
                    $(this).find('.registration-checkbox').prop('checked', activity_list_data.registered);
                    $(this).find('.price').html(formatPrice(activity_list_data.price));
                });
                table.data('populating', parseInt(table.data('populating')) - 1);


                form.find("#saldo-difference").val(formatPriceWithoutSign(data.price_difference));
                form.find("#previous-saldo").val(formatPriceWithoutSign(data.saldo));
                form.find('#received-money').val(formatPriceWithoutSign(data.price_difference));
                updateNewSaldo();
            }

            function updateNewSaldo() {
                const received_money = parseFloat(form.find('#received-money').val());
                const previous_saldo = parseFloat(form.find('#previous-saldo').val());
                const saldo_difference = parseFloat(form.find('#saldo-difference').val());
                form.find('#new-saldo').val(formatPriceWithoutSign(previous_saldo + saldo_difference - received_money));
            }

            $('#btn-set-all-attending-today').click(function () {
                table.find('td.day-attendance').each(function () {
                    const date = new Date($(this).parent('tr').data('date'));
                    if (date.getTime() !== today.getTime()) {
                        return;
                    }
                    const child_id = $(this).data('child-id');
                    const whole_week_registration = table
                        .find('td.whole-week-registration[data-child-id=' + child_id + '] > input.registration-checkbox')
                        .is(':checked');
                    const day_registration = $(this)
                        .parent('tr')
                        .find('td.day-registration[data-child-id=' + child_id + '] > input.registration-checkbox')
                        .is(':checked');
                    $(this).find('.attendance-checkbox').prop('checked', day_registration || whole_week_registration);
                });
            });

            $('#previous-saldo, #received-money').change(function () {
                updateNewSaldo();
            });

            function populateCurrentRegistrationData() {
                loadCurrentRegistrationData(populateRegistrationData);
            }

            function populateUpdatedRegistrationPrices() {
                const data = getRegistrationFormData();
                console.log("sending registration data (no submit)", data);
                increaseNbRequests();
                $.post('{{ route('api.simulate_submit_registration_data', [
            'week_id' => $week->id, 'family_id' => $family->id]) }}',
                    data, function (response) {
                        console.log("got prices data back", response);
                        decreaseNbRequests();
                        populateRegistrationData(response);
                    }
                );
            }

            function submitRegistrationData(done) {
                const data = getRegistrationFormData();
                console.log("sending registration data: ", data);
                increaseNbRequests();
                $.post('{{ route('api.submit_registration_data', ['week' => $week, 'family' => $family]) }}',
                    data, function (response) {
                        console.log('Submitted registration data, got following back: ', response);
                        decreaseNbRequests();
                        populateRegistrationData(response);
                        clearTransactionData();
                        if (done !== null) {
                            done();
                        }
                    });
            }

            $('#submit-registration-data').click(function () {
                submitRegistrationData(function () {
                    window.location.href = '{!! route('internal.registrations') !!}';
                });
            });
            $('#submit-registration-data-and-next').click(function () {
                submitRegistrationData();
                window.location.href = '{!! route('internal.show_find_family_registration', ['week' => $week]) !!}';
            });
            $('#btn-cancel').click(function () {
                window.location.href = '{!! route('internal.registrations') !!}';
            });

            table.find('.registration-checkbox').change(function () {
                if (parseInt(table.data('populating')) === 0) {
                    populateUpdatedRegistrationPrices();
                }
            });

            populateCurrentRegistrationData();
        });
    </script>
@endpush